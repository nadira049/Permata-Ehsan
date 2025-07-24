<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\ActivityImage;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewEventOrActivityNotification;
use App\Models\User;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role;
            $action = $request->route()->getActionMethod();
            // Only admin and teacher can manage (not just view)
            if (!in_array($action, ['index', 'show']) && !in_array($role, ['admin', 'teacher'])) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $years = ['Year 4', 'Year 5', 'Year 6', 'All year'];
        $selectedYear = request('year', null); // null by default
        $query = Activity::with('images');

        $user = auth()->user();
        if ($user->role === 'parent') {
            // Get all years for this parent's children (from class relationship)
            $childYears = $user->children()->with('class')->get()->pluck('class.year')->unique()->filter()->values()->all();
            // Support both numeric and string year formats
            $childYears = array_merge($childYears, array_map(function($y) { return 'Year ' . $y; }, $childYears));
            if ($selectedYear) {
                // Only show activities for the selected year if it's in the parent's child years
                if (in_array($selectedYear, $childYears)) {
                    $query->where('year', $selectedYear);
                } else {
                    $query->whereRaw('0=1'); // Show nothing if not a child's year
                }
            } else {
                $query->whereIn('year', $childYears);
            }
        } else {
            // Kod asal untuk admin/teacher
            if (in_array($selectedYear, ['Year 4', 'Year 5', 'Year 6'])) {
                $query->where(function($q) use ($selectedYear) {
                    $q->where('year', $selectedYear)->orWhere('year', 'All year');
                });
            }
        }

        $activities = $query->paginate(10);

        $now = now()->toDateString();
        $summary = [
            'total' => $activities->total(),
            'done' => $activities->filter(function($a) use ($now) { return $a->date < $now; })->count(),
            'upcoming' => $activities->filter(function($a) use ($now) { return $a->date >= $now; })->count(),
        ];
        return view('activities.index', [
            'activities' => $activities,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'summary' => $summary,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $years = ['Year 4', 'Year 5', 'Year 6', 'All year', 'All activities'];
        return view('activities.create', ['classGroups' => $years]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $years = ['Year 4', 'Year 5', 'Year 6', 'All year', 'All activities'];
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'date' => 'required|date',
            'status' => 'nullable',
            'year' => 'required|in:' . implode(',', $years),
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'images' => 'required|array|min:1',
        ]);
        $activity = Activity::create($validated);
        foreach ($request->file('images') as $img) {
            $path = $img->store('activities', 'public');
            ActivityImage::create([
                'activity_id' => $activity->id,
                'image_path' => $path,
            ]);
        }
        // Send notification to all users
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new NewEventOrActivityNotification(
                'activity',
                $activity->name,
                $activity->description,
                route('activities.index'), // Go to main list page
                $activity->date
            ));
        }
        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        $years = ['Year 4', 'Year 5', 'Year 6', 'All year', 'All activities'];
        $activity->load('images');
        return view('activities.edit', ['activity' => $activity, 'classGroups' => $years]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $years = ['Year 4', 'Year 5', 'Year 6', 'All year', 'All activities'];
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'date' => 'required|date',
            'status' => 'nullable',
            'year' => 'required|in:' . implode(',', $years),
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'images' => 'nullable|array',
        ]);
        $activity->update($validated);
        // Only delete images the user marked for removal
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imgId) {
                $img = $activity->images()->where('id', $imgId)->first();
                if ($img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }
        }
        // Add new images (do not touch old images unless deleted above)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('activities', 'public');
                ActivityImage::create([
                    'activity_id' => $activity->id,
                    'image_path' => $path,
                ]);
            }
        }
        return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        foreach ($activity->images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }
}
