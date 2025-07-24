<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role;
            if (!in_array($role, ['teacher', 'admin'])) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $years = ['Year 4', 'Year 5', 'Year 6'];
        $selectedYear = request('year', null);
        $childrenQuery = Child::with('class');
        if (in_array($selectedYear, $years)) {
            $childrenQuery->whereHas('class', function($q) use ($selectedYear) {
                $q->where('year', $selectedYear);
            });
        }
        $children = $childrenQuery->get();
        // Add class year and name to each child for frontend use
        $children->transform(function($child) {
            $child->year = $child->class->year ?? '';
            $child->class_name = $child->class->name ?? '';
            return $child;
        });
        return view('children.index', compact('children', 'years', 'selectedYear'));
    }

    public function create()
    {
        $classes = \App\Models\Classroom::orderBy('year')->orderBy('name')->get();
        return view('children.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'class_id' => 'required|exists:classes,id',
        ]);
        $validated['user_id'] = auth()->id();
        \App\Models\Child::create($validated);
        return redirect()->route('child.index');
    }

    public function show(Child $child)
    {
        return view('children.show', compact('child'));
    }

    public function edit($child_progress)
    {
        $child = \App\Models\Child::findOrFail($child_progress);
        return view('children.edit', compact('child'));
    }

    public function update(Request $request, $child_progress)
    {
        $child = \App\Models\Child::findOrFail($child_progress);
        $data = $request->only(['progress', 'level']);
        $child->update($data);
        // Always redirect to the progress page with the year filter if present
        $year = $request->input('year');
        if ($year) {
            return redirect()->route('progress.index', ['year' => $year]);
        }
        return redirect()->route('progress.index');
    }

    public function destroy(Child $child)
    {
        $child->delete();
        return redirect()->route('child.index');
    }

    public function childrenByYear($year)
    {
        // Get all classes for the given year
        $classrooms = \App\Models\Classroom::where('year', $year)->orderBy('name')->get();
        $classIds = $classrooms->pluck('id');
        // Get all children in those classes
        $children = \App\Models\Child::with('class')->whereIn('class_id', $classIds)->get();
        return view('children.by_year', compact('children', 'year', 'classrooms'));
    }
} 