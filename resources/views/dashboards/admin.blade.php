@extends('layouts.app')
@section('content')
<div style="background:#f6f8fa;min-height:100vh;padding:2rem 0;">
    <h1 class="text-2xl font-bold mb-6" style="color:#232b33;">Admin Dashboard</h1>
    <div class="flex flex-col md:flex-row items-center justify-center gap-12 mb-8">
        <div style="background:#fff;border-radius:16px;box-shadow:0 2px 16px #0001;padding:2rem 2.5rem;max-width:420px;width:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <h2 class="text-lg font-semibold mb-4 text-center" style="color:#232b33;">User Roles Distribution</h2>
            <canvas id="userRolePieChart" width="300" height="300"></canvas>
        </div>
        <div class="flex flex-col gap-4 min-w-[220px] max-w-[260px] w-full justify-center items-center">
            <div class="bg-green-500 text-white rounded-xl shadow-lg p-6 text-center text-xl font-semibold w-full">
                Total Events
                <div class="text-4xl font-bold mt-2">{{ $eventCount }}</div>
            </div>
            <div class="bg-purple-400 text-white rounded-xl shadow-lg p-6 text-center text-xl font-semibold w-full">
                Total Activities
                <div class="text-4xl font-bold mt-2">{{ $activityCount }}</div>
            </div>
            <div class="bg-orange-400 text-white rounded-xl shadow-lg p-6 text-center text-xl font-semibold w-full">
                Learning Materials
                <div class="text-4xl font-bold mt-2">{{ $learningMaterialCount }}</div>
            </div>
            <div class="bg-blue-400 text-white rounded-xl shadow-lg p-6 text-center text-xl font-semibold w-full">
                Total Children
                <div class="text-4xl font-bold mt-2">{{ $totalChildren }}</div>
            </div>
        </div>
    </div>
    <div class="w-full flex flex-col items-center mb-6">
        <form method="GET" class="flex items-center gap-4">
            <label for="progress-date" class="font-semibold">Select Date:</label>
            <input type="date" id="progress-date" name="date" value="{{ $selectedDate }}" class="px-3 py-2 border rounded-lg" onchange="this.form.submit()">
            <label for="progress-year" class="font-semibold ml-4" style="margin-right: 8px;">Select Year:</label>
            <select id="progress-year" name="year" class="px-3 py-2 border rounded-lg" style="min-width: 110px; margin-right: 12px;" onchange="this.form.submit()">
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Year {{ $year }}</option>
                @endforeach
            </select>
            <label for="progress-class" class="font-semibold ml-4" style="margin-right: 8px;">Select Class:</label>
            <select id="progress-class" name="class" class="px-3 py-2 border rounded-lg" style="min-width: 140px; padding-right: 2.5em; margin-right: 12px;" onchange="this.form.submit()">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->name }}" {{ $selectedClass == $class->name ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
        </form>
        <div class="mt-2 text-sm text-gray-500">Showing progress for: <span class="font-semibold">{{ $selectedDate }}</span> | <span class="font-semibold">Year {{ $selectedYear }}</span></div>
    </div>
    <div class="w-full flex flex-col md:flex-row justify-center gap-8 mb-12">
        <div class="flex-1 bg-gray-100 rounded-xl shadow p-6 text-center">
            <div class="text-lg font-semibold mb-2">Year {{ $selectedYear }} Progress for {{ $selectedDate }}</div>
            <div class="text-3xl font-bold text-blue-700 mb-1">{{ $progressStats[$selectedYear]['withProgress'] }} / {{ $progressStats[$selectedYear]['total'] }}</div>
            <div class="text-sm text-gray-500">Progress / Total</div>
            @if($progressStats[$selectedYear]['total'] > 0)
                <div class="mt-2 text-xs text-gray-600">{{ round($progressStats[$selectedYear]['withProgress'] / $progressStats[$selectedYear]['total'] * 100, 1) }}% completed</div>
            @endif
        </div>
    </div>
    <div class="w-full flex flex-col items-center mb-12">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-8 w-full">
            <div class="bg-white rounded-xl shadow p-6 w-full">
                <h2 class="text-lg font-bold mb-4 text-center">Year {{ $selectedYear }} Latest Progress Level by Child</h2>
                <canvas id="progressBarChart_{{ $selectedYear }}" width="800" height="350"></canvas>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div style="background:#fff;border-radius:12px;box-shadow:0 2px 8px #0001;padding:1.5rem;">
            <h2 class="text-lg font-semibold mb-2" style="color:#232b33;">Recent Events</h2>
            <table class="w-full text-sm border border-gray-300">
                <thead>
                    <tr class="text-left" style="background:#232b33;color:#fff;">
                        <th class="py-1 border-b border-gray-300 font-bold">No.</th>
                        <th class="py-1 border-b border-gray-300 font-bold">Title</th>
                        <th class="py-1 border-b border-gray-300 font-bold">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @forelse ($recentEvents as $event)
                        <tr class="border-b border-gray-200">
                            <td class="py-1 border-r border-gray-200">{{ $i++ }}</td>
                            <td class="py-1 border-r border-gray-200">{{ $event->title ?? $event->name ?? 'Event' }}</td>
                            <td class="py-1">
                                {{ $event->start_date }}
                                @if($event->end_date && $event->end_date !== $event->start_date)
                                    <br><span class="text-xs text-gray-500">to {{ $event->end_date }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No recent events.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="background:#fff;border-radius:12px;box-shadow:0 2px 8px #0001;padding:1.5rem;">
            <h2 class="text-lg font-semibold mb-2" style="color:#232b33;">Recent Activities</h2>
            <table class="w-full text-sm border border-gray-300">
                <thead>
                    <tr class="text-left" style="background:#232b33;color:#fff;">
                        <th class="py-1 border-b border-gray-300 font-bold">No.</th>
                        <th class="py-1 border-b border-gray-300 font-bold">Title</th>
                        <th class="py-1 border-b border-gray-300 font-bold">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @forelse ($recentActivities as $activity)
                        <tr class="border-b border-gray-200">
                            <td class="py-1 border-r border-gray-200">{{ $i++ }}</td>
                            <td class="py-1 border-r border-gray-200">{{ $activity->title ?? $activity->name ?? 'Activity' }}</td>
                            <td class="py-1">{{ $activity->date ?? $activity->start_date }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No recent activities.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('userRolePieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Parent', 'Teacher', 'Admin'],
            datasets: [{
                data: [{{ $parentCount }}, {{ $teacherCount }}, {{ $adminCount }}],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)', // blue
                    'rgba(16, 185, 129, 0.7)', // green
                    'rgba(239, 68, 68, 0.7)'   // red
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
    @php
        $chartData = [];
        $chartColors = [];
        foreach($progressBarData[$selectedYear]['labels'] as $index => $label) {
            $level = $progressBarData[$selectedYear]['levels'][$index] ?? 0;
            $chartData[] = $level;
            $chartColors[] = $level === 0 ? '#e5e7eb' : ($level === 1 ? '#fbbf24' : ($level === 2 ? '#34d399' : '#3b82f6'));
        }
        $chartLabels = $progressBarData[$selectedYear]['labels'];
    @endphp
    document.addEventListener('DOMContentLoaded', function () {
        new Chart(document.getElementById('progressBarChart_{{ $selectedYear }}').getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Latest Level',
                    data: @json($chartData),
                    backgroundColor: @json($chartColors),
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 3,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                if (value === 0) return 'No Progress';
                                if (value === 1) return 'Level 1';
                                if (value === 2) return 'Level 2';
                                if (value === 3) return 'Level 3';
                                return '';
                            }
                        }
                    },
                    x: {
                        ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 }
                    }
                }
            }
        });
    });
</script>
@endsection 