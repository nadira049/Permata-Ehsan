<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Only get this parent's children, with their class
        $children = $user->children()->with('class')->get();
        // Only show years for this parent's children
        $childYears = $children->pluck('class.year')->unique()->filter()->values()->all();
        $years = $childYears;
        $childYears[] = 'All year';

        $selectedYear = request('year', $years[0] ?? null);
        $selectedClass = request('class', '');

        // Support both numeric and string year formats
        $selectedYearVariants = [$selectedYear];
        if (is_numeric($selectedYear)) {
            $selectedYearVariants[] = 'Year ' . $selectedYear;
        } elseif (strpos($selectedYear, 'Year ') === 0) {
            $selectedYearVariants[] = substr($selectedYear, 5);
        }

        // Get all children in the selected year (any format)
        $allChildrenInYear = collect();
        if ($selectedYear) {
            $allChildrenInYear = \App\Models\Child::with('class')
                ->whereHas('class', function($q) use ($selectedYearVariants) {
                    $q->whereIn('year', $selectedYearVariants);
                })->get();
        }

        $eventCount = \App\Models\Event::count();
        $activityQuery = \App\Models\Activity::query();
        $learningMaterialQuery = \App\Models\LearningMaterial::query();

        if ($selectedYear) {
            $activityQuery->whereIn('year', $selectedYearVariants);
            $learningMaterialQuery->whereIn('year', $selectedYearVariants);
        }
        if ($selectedClass) {
            $activityQuery->where('class', $selectedClass);
            $learningMaterialQuery->where('class', $selectedClass);
        }

        $activityCount = $activityQuery->count();
        $learningMaterialCount = $learningMaterialQuery->count();

        $oneMonthAgo = now()->subMonth();
        $recentEvents = \App\Models\Event::where('start_date', '>=', $oneMonthAgo)
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();
        $recentActivities = \App\Models\Activity::whereIn('year', $childYears)
            ->where('date', '>=', $oneMonthAgo)
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();
        $totalChildren = $children->count();
        $selectedDate = request('date', date('Y-m-d'));
        $progress = \App\Models\Progress::whereIn('child_id', $allChildrenInYear->pluck('id'))
            ->where('date', $selectedDate)
            ->get()
            ->keyBy('child_id');

        // DEBUG: Log children and activities in the selected year
        // \Log::info('Children in year: ' . $selectedYear, $allChildrenInYear->pluck('name')->toArray());
        // \Log::info('Activities in year: ' . $selectedYear, $activityQuery->pluck('name')->toArray());

        $progressBarData = [];
        foreach ($years as $year) {
            $selectedClass = request('class', '');
            $childrenInYear = $allChildrenInYear->filter(function($child) use ($year, $selectedClass) {
                if (!$child->class || $child->class->year != $year) return false;
                if ($selectedClass && $child->class->name != $selectedClass) return false;
                return true;
            });
            $labels = [];
            $levels = [];
            $barColors = [];
            foreach ($childrenInYear as $child) {
                $childProgress = $progress->get($child->id);
                $level = $childProgress ? ($childProgress->level === 'Level 1' ? 1 : ($childProgress->level === 'Level 2' ? 2 : ($childProgress->level === 'Level 3' ? 3 : null))) : null;
                $levels[] = $level;
                // Highlight parent's own children
                $barColors[] = $children->pluck('id')->contains($child->id) ? '#2d3748' : '#34d399';
            }
            $progressBarData[$year] = [
                'labels' => $labels,
                'levels' => $levels,
                'barColors' => $barColors,
            ];
        }

        $selectedYear = request('year', $years[0] ?? null);
        $classes = \App\Models\Classroom::where('year', $selectedYear)->orderBy('name')->get();

        return view('dashboards.parent', compact('eventCount', 'activityCount', 'learningMaterialCount', 'recentEvents', 'recentActivities', 'totalChildren', 'years', 'selectedDate', 'children', 'allChildrenInYear', 'progress', 'progressBarData', 'classes'));
    }
} 