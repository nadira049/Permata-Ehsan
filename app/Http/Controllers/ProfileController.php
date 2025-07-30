<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function progress(Request $request)
    {
        $user = auth()->user();
        $childrenQuery = \App\Models\Child::with('class');
        
        if ($user && $user->role === 'parent') {
            // Only show this parent's children
            $childrenQuery->where('user_id', $user->id);
            // Get years where this parent has children
            $parentYears = $user->children()->with('class')->get()->pluck('class.year')->unique()->filter()->values()->all();
            $years = $parentYears;
        } else {
            $years = ['4', '5', '6'];
        }
        
        $selectedYear = $request->input('year', '');
        $selectedDate = $request->input('date', date('Y-m-d'));
        $selectedClass = $request->input('class', '');
        
        // For parents, check if they have children in the selected year
        $hasChildrenInYear = true;
        if ($user && $user->role === 'parent' && $selectedYear) {
            $hasChildrenInYear = $user->children()->whereHas('class', function($q) use ($selectedYear) {
                $q->where('year', $selectedYear);
            })->exists();
        }
        
        if ($selectedYear && in_array($selectedYear, $years)) {
            $childrenQuery->whereHas('class', function($q) use ($selectedYear) {
                $q->where('year', $selectedYear);
            });
        }
        
        if ($selectedClass) {
            $childrenQuery->whereHas('class', function($q) use ($selectedClass) {
                $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($selectedClass))]);
            });
        }
        
        $classrooms = \App\Models\Classroom::where('year', $selectedYear)->orderBy('name')->get();
        $children = $childrenQuery->paginate(10)->withQueryString();
        
        // Add class year and name to each child for frontend use
        $children->getCollection()->transform(function($child) {
            $child->year = $child->class->year ?? '';
            $child->class_name = $child->class->name ?? '';
            return $child;
        });
        
        // Get progress for all children for the selected date
        $progress = \App\Models\Progress::whereIn('child_id', $children->pluck('id'))
            ->where('date', $selectedDate)
            ->get()
            ->keyBy('child_id')
            ->toArray();
            
        return view('progress.index', compact('children', 'years', 'selectedYear', 'selectedDate', 'progress', 'classrooms', 'hasChildrenInYear'));
    }

    public function saveProgress(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'date' => 'required|date',
            'progress' => 'nullable|string',
            'level' => 'nullable|in:Level 1,Level 2,Level 3',
        ]);
        
        try {
            $progress = \App\Models\Progress::firstOrNew([
                'child_id' => $validated['child_id'],
                'date' => $validated['date'],
            ]);
            $progress->progress = $validated['progress'] ?? '';
            $progress->level = $validated['level'] ?? null;
            $progress->confirmed = true;
            $progress->save();
            
            // Check if this is an AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Progress saved successfully'
                ]);
            }
            
            // Regular form submission - redirect
            $child = \App\Models\Child::find($validated['child_id']);
            $year = $child ? $child->year : null;
            return redirect()->route('progress.index', ['year' => $year, 'date' => $validated['date']]);
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving progress: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error saving progress: ' . $e->getMessage()]);
        }
    }
}
