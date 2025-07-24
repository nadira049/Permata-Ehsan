<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userCount = \App\Models\User::count();
        $eventCount = \App\Models\Event::count();
        $activityCount = \App\Models\Activity::count();
        $learningMaterialCount = \App\Models\LearningMaterial::count();
        $parentCount = \App\Models\User::where('role', 'parent')->count();
        $teacherCount = \App\Models\User::where('role', 'teacher')->count();
        $adminCount = \App\Models\User::where('role', 'admin')->count();
        $totalChildren = \App\Models\Child::count();
        $years = ['4', '5', '6'];
        $selectedYear = $request->input('year', $years[0]);
        $selectedClass = $request->input('class', '');
        $selectedDate = $request->input('date', date('Y-m-d'));
        $classes = \App\Models\Classroom::where('year', $selectedYear)->orderBy('name')->get();
        $childrenQuery = \App\Models\Child::whereHas('class', function($q) use ($selectedYear, $selectedClass) {
            $q->where('year', $selectedYear);
            if ($selectedClass) {
                $q->where('name', $selectedClass);
            }
        });
        $total = $childrenQuery->count();
        $childIds = $childrenQuery->pluck('id');
        $withProgress = \App\Models\Progress::where('date', $selectedDate)
            ->whereIn('child_id', $childIds)
            ->count();
        $progressStats[$selectedYear] = [
            'total' => $total,
            'withProgress' => $withProgress,
        ];
        $childrenInYear = $childrenQuery->get();
        $labels = [];
        $levels = [];
        foreach ($childrenInYear as $child) {
            $labels[] = $child->name;
            $progress = \App\Models\Progress::where('child_id', $child->id)
                ->where('date', $selectedDate)
                ->first();
            $level = $progress && $progress->level ? ($progress->level === 'Level 1' ? 1 : ($progress->level === 'Level 2' ? 2 : ($progress->level === 'Level 3' ? 3 : null))) : null;
            $levels[] = $level;
        }
        $progressBarData = [
            $selectedYear => [
                'labels' => $labels,
                'levels' => $levels,
            ]
        ];
        // Only show recent events/activities within the last 1 month
        $oneMonthAgo = now()->subMonth();
        $recentEvents = \App\Models\Event::where('start_date', '>=', $oneMonthAgo)
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();
        $recentActivities = \App\Models\Activity::where('date', '>=', $oneMonthAgo)
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();
        return view('dashboards.admin', compact('userCount', 'eventCount', 'activityCount', 'learningMaterialCount', 'recentEvents', 'recentActivities', 'parentCount', 'teacherCount', 'adminCount', 'totalChildren', 'years', 'selectedDate', 'selectedYear', 'selectedClass', 'classes', 'progressStats', 'progressBarData'));
    }
} 