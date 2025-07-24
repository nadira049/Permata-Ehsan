<?php

namespace App\Http\Controllers;

use App\Models\LearningMaterial;
use Illuminate\Http\Request;

class LearningMaterialController extends Controller
{
    public function __construct()
    {
        // No global middleware, restrict only specific methods below
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedYear = request('year');
        $query = LearningMaterial::query();
        $user = auth()->user();
        if ($user && $user->role === 'parent') {
            // Get all years for this parent's children (from class relationship)
            $childYears = $user->children()->with('class')->get()->pluck('class.year')->unique()->filter()->values()->all();
            // Support both numeric and string year formats
            $childYears = array_merge($childYears, array_map(function($y) { return 'Year ' . $y; }, $childYears));
            if ($selectedYear) {
                if (in_array($selectedYear, $childYears)) {
                    $query->where('year', $selectedYear);
                } else {
                    $query->whereRaw('0=1'); // Show nothing if not a child's year
                }
            } else {
                $query->whereIn('year', $childYears);
            }
        } else {
            if ($selectedYear) {
                $query->where('year', $selectedYear);
            }
        }
        $learningMaterials = $query->orderBy('id')->paginate(10);
        return view('learning-materials.index', compact('learningMaterials', 'selectedYear'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        return view('learning-materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'nullable',
            'file_path' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,mp4,jpeg,png',
            'year' => 'required|in:Year 4,Year 5,Year 6',
        ]);
        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('learning_materials', 'public');
            $validated['file_path'] = $path;
        }
        LearningMaterial::create($validated);
        return redirect()->route('learning-materials.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(LearningMaterial $learningMaterial)
    {
        return view('learning-materials.show', compact('learningMaterial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LearningMaterial $learningMaterial)
    {
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        return view('learning-materials.edit', compact('learningMaterial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LearningMaterial $learningMaterial)
    {
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'nullable',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,jpeg,png',
            'year' => 'required|in:Year 4,Year 5,Year 6',
        ]);
        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('learning_materials', 'public');
            $validated['file_path'] = $path;
        } else {
            unset($validated['file_path']); // Don't overwrite if not uploading new file
        }
        $learningMaterial->update($validated);
        return redirect()->route('learning-materials.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LearningMaterial $learningMaterial)
    {
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        $learningMaterial->delete();
        return redirect()->route('learning-materials.index');
    }

    public function adminUpdateStatus(Request $request, LearningMaterial $learningMaterial)
    {
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'status' => 'required|in:pending,approved',
        ]);
        $learningMaterial->status = $request->status;
        $learningMaterial->save();
        return redirect()->back()->with('success', 'Status updated!');
    }
}
