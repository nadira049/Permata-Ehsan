@extends('layouts.app')
@section('content')
<div class="w-full max-w-5xl mx-auto mt-8">
    <div class="flex flex-col items-center gap-8 mb-12">
        <div class="w-full flex flex-col md:flex-row justify-center gap-8">
            <div class="flex-1 bg-green-500 rounded-xl shadow-lg p-8 text-center text-white text-xl font-semibold">
                Total Events
                <div class="text-4xl font-bold mt-2">{{ $eventCount ?? 0 }}</div>
            </div>
            <div class="flex-1 bg-purple-400 rounded-xl shadow-lg p-8 text-center text-white text-xl font-semibold">
                Total Activities
                <div class="text-4xl font-bold mt-2">{{ $activityCount ?? 0 }}</div>
            </div>
            <div class="flex-1 bg-orange-400 rounded-xl shadow-lg p-8 text-center text-white text-xl font-semibold">
                Learning Materials
                <div class="text-4xl font-bold mt-2">{{ $learningMaterialCount ?? 0 }}</div>
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
                    <option value="{{ $year }}" {{ request('year', $years[0]) == $year ? 'selected' : '' }}>Year {{ $year }}</option>
                @endforeach
            </select>
            <label for="progress-class" class="font-semibold ml-4" style="margin-right: 8px;">Select Class:</label>
            <select id="progress-class" name="class" class="px-3 py-2 border rounded-lg" style="min-width: 140px; padding-right: 2.5em; margin-right: 12px;" onchange="this.form.submit()">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->name }}" {{ request('class', '') == $class->name ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
        </form>
        <div class="mt-2 text-sm text-gray-500">Showing progress for: <span class="font-semibold">{{ $selectedDate }}</span> | <span class="font-semibold">Year {{ request('year', $years[0]) }}</span></div>
    </div>
    <div class="w-full flex flex-col md:flex-row justify-center gap-8 mb-12">
        <div class="flex-1 bg-gray-100 rounded-xl shadow p-6 text-center">
            <div class="text-lg font-semibold mb-2">Year {{ request('year', $years[0]) }} Progress for {{ $selectedDate }}</div>
            <div class="text-3xl font-bold text-blue-700 mb-1">{{ $progressStats[request('year', $years[0])]['withProgress'] ?? 0 }} / {{ $progressStats[request('year', $years[0])]['total'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">Progress / Total</div>
            @if(($progressStats[request('year', $years[0])]['total'] ?? 0) > 0)
                <div class="mt-2 text-xs text-gray-600">{{ round(($progressStats[request('year', $years[0])]['withProgress'] ?? 0) / ($progressStats[request('year', $years[0])]['total'] ?? 1) * 100, 1) }}% completed</div>
            @endif
        </div>
    </div>
    <div class="w-full flex flex-col items-center mb-12">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-8 w-full">
            <div class="bg-white rounded-xl shadow p-6 w-full">
                <h2 class="text-lg font-bold mb-4 text-center">Year {{ request('year', $years[0]) }} Latest Progress Level by Child</h2>
                <canvas id="progressBarChart_{{ request('year', $years[0]) }}" width="800" height="350"></canvas>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Chart(document.getElementById('progressBarChart_{{ request('year', $years[0]) }}').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($allChildrenInYear as $child)
                            "{{ $child->name }}",
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Latest Level',
                        data: [
                            @foreach($allChildrenInYear as $child)
                                @php
                                    $childProgress = $progress->get($child->id);
                                    $level = $childProgress ? ($childProgress->level === 'Level 1' ? 1 : ($childProgress->level === 'Level 2' ? 2 : ($childProgress->level === 'Level 3' ? 3 : null))) : null;
                                @endphp
                                {{ $level ?? 'null' }},
                            @endforeach
                        ],
                        backgroundColor: [
                            @foreach($allChildrenInYear as $child)
                                "{{ $children->pluck('id')->contains($child->id) ? '#2d3748' : '#34d399' }}",
                            @endforeach
                        ],
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
                            min: 1,
                            max: 3,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
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
    <style>
        canvas {
            width: 100% !important;
            height: 350px !important;
        }
    </style>
    {{-- Remove the debug table after the chart --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold mb-4">Recent Events</h2>
            <table class="w-full border border-gray-300">
                <thead>
                    <tr style="background:#232b33;color:#fff;">
                        <th class="text-left font-bold border-b border-gray-300">No.</th>
                        <th class="text-left font-bold border-b border-gray-300">Title</th>
                        <th class="text-left font-bold border-b border-gray-300">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach(($recentEvents ?? []) as $event)
                        <tr class="border-b border-gray-200">
                            <td class="py-1 border-r border-gray-200">{{ $i++ }}</td>
                            <td class="py-1 border-r border-gray-200">{{ $event->title ?? '' }}</td>
                            <td class="py-1">
                                {{ $event->start_date }}
                                @if($event->end_date && $event->end_date !== $event->start_date)
                                    <br><span class="text-xs text-gray-500">to {{ $event->end_date }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold mb-4">Recent Activities</h2>
            <table class="w-full border border-gray-300">
                <thead>
                    <tr style="background:#232b33;color:#fff;">
                        <th class="text-left font-bold border-b border-gray-300">No.</th>
                        <th class="text-left font-bold border-b border-gray-300">Title</th>
                        <th class="text-left font-bold border-b border-gray-300">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach(($recentActivities ?? []) as $activity)
                        <tr class="border-b border-gray-200">
                            <td class="py-1 border-r border-gray-200">{{ $i++ }}</td>
                            <td class="py-1 border-r border-gray-200">{{ $activity->name ?? '' }}</td>
                            <td class="py-1">{{ $activity->date ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
