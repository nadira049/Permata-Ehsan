@extends('layouts.app')
@section('content')
<div class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow"
     x-data="{
        children: @js($children),
        progress: @js($progress),
        selectedDate: '{{ $selectedDate ?? '' }}',
        page: 1,
        itemsPerPage: 10,
        get pagedChildren() {
            return this.children.slice((this.page - 1) * this.itemsPerPage, this.page * this.itemsPerPage);
        },
        get totalPages() {
            return Math.ceil(this.children.length / this.itemsPerPage);
        },
        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; }
     }">
    <h1 class="text-2xl font-bold font-sans mb-4">Children Progress</h1>
    @php 
        $role = Auth::user()->role ?? '';
        $user = Auth::user();
        $years = ['Year 4', 'Year 5', 'Year 6'];
        $selectedYear = $selectedYear ?? request('year', null);
        if ($role === 'parent') {
            $childYears = $user->children->pluck('year')->unique()->toArray();
            $filterYears = array_intersect($years, $childYears);
        } else {
            $filterYears = $years;
        }
    @endphp
    <div class="flex gap-2 mb-4 items-center">
        <a href="{{ route('progress.index') }}"
           class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ empty($selectedYear) ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
            All Years
        </a>
        <a href="{{ route('progress.index', ['year' => 4]) }}"
           class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ $selectedYear == 4 ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
            Year 4
        </a>
        <a href="{{ route('progress.index', ['year' => 5]) }}"
           class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ $selectedYear == 5 ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
            Year 5
        </a>
        <a href="{{ route('progress.index', ['year' => 6]) }}"
           class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ $selectedYear == 6 ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
            Year 6
        </a>
        <form method="GET" class="ml-auto">
            <input type="date" name="date" value="{{ $selectedDate ?? '' }}" max="{{ \Carbon\Carbon::now('Asia/Kuala_Lumpur')->toDateString() }}" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg" style="max-width:180px;">
            @if(!empty($selectedYear))
                <input type="hidden" name="year" value="{{ $selectedYear }}">
            @endif
        </form>
    </div>
    @if($selectedYear && isset($classrooms) && $classrooms->count())
        <div class="flex gap-2 mb-4 items-center">
            @php $selectedClass = request('class', ''); @endphp
            <a href="{{ route('progress.index', ['year' => $selectedYear]) }}"
               class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ $selectedClass == '' ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
                All Classes
            </a>
            @foreach($classrooms as $classroom)
                <a href="{{ route('progress.index', ['year' => $selectedYear, 'class' => $classroom->name]) }}"
                   class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ $selectedClass == $classroom->name ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
                    {{ $classroom->name }}
                </a>
            @endforeach
            <form method="GET" class="ml-2 flex items-center" action="{{ route('progress.index') }}">
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                @if($selectedClass)
                    <input type="hidden" name="class" value="{{ $selectedClass }}">
                @endif
                <input type="text" name="search" value="{{ request('search', '') }}" placeholder="Search by name..." class="px-3 py-2 border rounded-lg" style="max-width:180px;">
            </form>
        </div>
    @endif
    @php
    $isToday = ($selectedDate ?? date('Y-m-d')) === \Carbon\Carbon::now('Asia/Kuala_Lumpur')->toDateString();
    $hasUnconfirmed = false;
    foreach ($children as $child) {
        $pid = is_object($child) ? $child->id : $child['id'];
        if (!isset($progress[$pid]) || empty($progress[$pid]['confirmed'])) {
            $hasUnconfirmed = true;
            break;
        }
    }
@endphp
@if($role === 'teacher' && $hasUnconfirmed)
    <div class="flex items-center gap-2 mb-4 p-3 bg-orange-50 border-l-4 border-orange-400 text-orange-700 rounded">
        <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='w-6 h-6 text-orange-400'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v2.25m0 3.75h.008v.008H12v-.008zm.008-9.008a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>
        <span class="font-semibold">
            {{ $isToday ? 'Please complete progress for today.' : 'You missed progress for this date.' }}
        </span>
    </div>
@endif
    {{-- Remove debug table --}}
    @php
        $selectedClass = request('class', '');
    @endphp
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-2 border-b text-left font-semibold">No</th>
                <th class="px-4 py-2 border-b text-left font-semibold">Name</th>
                <th class="px-4 py-2 border-b text-left font-semibold">Class</th>
                <th class="px-4 py-2 border-b text-left font-semibold">Progress</th>
                <th class="px-4 py-2 border-b text-left font-semibold">Level</th>
                @if($role === 'teacher')
                    <th class="px-4 py-2 border-b text-left font-semibold">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $row = 1; @endphp
            @foreach($children as $child)
                @if($selectedClass === '' || strtolower(trim($child->class_name)) === strtolower(trim($selectedClass)))
                    @php $p = $progress[$child->id] ?? null; @endphp
                    <tr>
                        <td class="px-4 py-2 border-b text-center">{{ $row++ }}</td>
                        <td class="px-4 py-2 border-b" style="white-space:normal;word-break:break-word;">{{ $child->name }}</td>
                        <td class="px-4 py-2 border-b">{{ trim(($child->year ? $child->year : '') . ' ' . ($child->class_name ?? '')) }}</td>
                        <td class="px-4 py-2 border-b">
                            @if($p && $p['confirmed'])
                                <span>{{ $p['progress'] }}</span>
                            @else
                                @if($role === 'teacher')
                                <form action="/progress/save" method="POST">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                                    <input type="text" name="progress" value="{{ $p['progress'] ?? '' }}" class="border rounded px-2 py-1 w-24">
                            @endif
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b">
                            @if($p && $p['confirmed'])
                                <span>{{ $p['level'] }}</span>
                            @else
                                @if($role === 'teacher')
                                    <select name="level" class="px-4 py-2 rounded-lg font-bold text-base bg-gray-100 border-2 border-gray-200 focus:border-blue-400 focus:outline-none w-40">
                                        <option value="">Select Level</option>
                                        <option value="Level 1" {{ ($p['level'] ?? '') === 'Level 1' ? 'selected' : '' }}>Level 1</option>
                                        <option value="Level 2" {{ ($p['level'] ?? '') === 'Level 2' ? 'selected' : '' }}>Level 2</option>
                                        <option value="Level 3" {{ ($p['level'] ?? '') === 'Level 3' ? 'selected' : '' }}>Level 3</option>
                                    </select>
                                @endif
                            @endif
                        </td>
                        @if($role === 'teacher')
                        <td class="px-4 py-2 border-b">
                            @if($p && $p['confirmed'])
                                <span class="text-green-600 font-semibold">Confirmed</span>
                            @else
                                <button type="submit" class="px-4 py-1 rounded text-sm font-semibold border transition"
                                    style="color:#3182ce;background:#e6f0fa;border:1px solid #bcdffb;"
                                    onmouseover="this.style.backgroundColor='#d0e7fa'" onmouseout="this.style.backgroundColor='#e6f0fa'">
                                    Save
                                </button>
                                </form>
                            @endif
                        </td>
                        @endif
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <div class="flex justify-center items-center gap-4 mt-6">
        <button
            @if ($children->onFirstPage()) disabled @endif
            onclick="window.location='{{ $children->previousPageUrl() }}'"
            class="px-6 py-2 rounded-lg bg-gray-100 font-semibold {{ $children->onFirstPage() ? 'text-gray-400 opacity-50' : 'text-gray-600 hover:bg-gray-200' }}"
        >
            Previous
        </button>
        <span class="font-semibold">
            Page {{ $children->currentPage() }} of {{ $children->lastPage() }}
        </span>
        <button
            @if (!$children->hasMorePages()) disabled @endif
            onclick="window.location='{{ $children->nextPageUrl() }}'"
            class="px-6 py-2 rounded-lg bg-gray-100 font-semibold {{ !$children->hasMorePages() ? 'text-gray-400 opacity-50' : 'text-gray-600 hover:bg-gray-200' }}"
        >
            Next
        </button>
    </div>
</div>
@endsection 