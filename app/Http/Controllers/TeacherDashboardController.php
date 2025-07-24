<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $eventCount = \App\Models\Event::count();
        $activityCount = \App\Models\Activity::count();
        $learningMaterialCount = \App\Models\LearningMaterial::count();
        $oneMonthAgo = now()->subMonth();
        $recentEvents = \App\Models\Event::where('start_date', '>=', $oneMonthAgo)
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();
        $recentActivities = \App\Models\Activity::where('date', '>=', $oneMonthAgo)
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();
        $totalChildren = \App\Models\Child::count();
        $years = ['4', '5', '6'];
        $selectedYear = request('year', $years[0]);
        $selectedClass = request('class', '');
        $selectedDate = request('date', date('Y-m-d'));
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
        // Prepare line chart data for each year (last 14 days)
        $days = collect(range(0, 13))->map(fn($i) => date('Y-m-d', strtotime("-$i days")))->reverse()->values();
        $progressLineData = [];
        foreach ($years as $year) {
            $childrenInYear = \App\Models\Child::whereHas('class', function($q) use ($year) { $q->where('year', $year); })->get();
            $datasets = [];
            foreach ($childrenInYear as $child) {
                $progresses = \App\Models\Progress::where('child_id', $child->id)
                    ->whereIn('date', $days)
                    ->get()
                    ->keyBy('date');
                $data = [];
                foreach ($days as $day) {
                    $level = $progresses[$day]->level ?? null;
                    $data[] = $level === 'Level 1' ? 1 : ($level === 'Level 2' ? 2 : ($level === 'Level 3' ? 3 : null));
                }
                $datasets[] = [
                    'label' => $child->name,
                    'data' => $data,
                    'fill' => false,
                ];
            }
            $progressLineData[$year] = [
                'labels' => $days,
                'datasets' => $datasets,
            ];
        }
        // Prepare bar chart data for the selected year only
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
        return view('dashboards.teacher', compact('eventCount', 'activityCount', 'learningMaterialCount', 'recentEvents', 'recentActivities', 'totalChildren', 'years', 'selectedDate', 'selectedYear', 'selectedClass', 'classes', 'progressStats', 'progressBarData'));
    }
} 