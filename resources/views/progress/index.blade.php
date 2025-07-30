@extends('layouts.app')
@section('content')
<div class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow">
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
    @php
        $selectedClass = request('class', '');
    @endphp
    @if($role === 'parent' && isset($hasChildrenInYear) && !$hasChildrenInYear && $selectedYear)
        <div class="flex items-center justify-center p-8 bg-gray-50 rounded-lg">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Not attempted</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any children in Year {{ $selectedYear }}.</p>
            </div>
        </div>
    @else
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
                        @if($p && $p['confirmed'])
                            <tr>
                                <td class="px-4 py-2 border-b text-center">{{ $row++ }}</td>
                                <td class="px-4 py-2 border-b" style="white-space:normal;word-break:break-word;">{{ $child->name }}</td>
                                <td class="px-4 py-2 border-b">{{ trim(($child->year ? $child->year : '') . ' ' . ($child->class_name ?? '')) }}</td>
                                <td class="px-4 py-2 border-b">
                                    <span>{{ $p['progress'] }}</span>
                                </td>
                                <td class="px-4 py-2 border-b">
                                    <span>{{ $p['level'] }}</span>
                                </td>
                                @if($role === 'teacher')
                                <td class="px-4 py-2 border-b">
                                    <span class="text-green-600 font-semibold">Confirmed</span>
                                </td>
                                @endif
                            </tr>
                        @else
                            @if($role === 'teacher')
                                <tr class="progress-row" data-child-id="{{ $child->id }}">
                                    <td class="px-4 py-2 border-b text-center">{{ $row++ }}</td>
                                    <td class="px-4 py-2 border-b" style="white-space:normal;word-break:break-word;">{{ $child->name }}</td>
                                    <td class="px-4 py-2 border-b">{{ trim(($child->year ? $child->year : '') . ' ' . ($child->class_name ?? '')) }}</td>
                                    <td class="px-4 py-2 border-b">
                                        <input type="text" name="progress" value="{{ $p['progress'] ?? '' }}" class="progress-input border rounded px-2 py-1 w-24" placeholder="Enter progress">
                                    </td>
                                    <td class="px-4 py-2 border-b">
                                        <select name="level" class="level-select px-4 py-2 rounded-lg font-bold text-base bg-gray-100 border-2 border-gray-200 focus:border-blue-400 focus:outline-none w-40">
                                            <option value="">Select Level</option>
                                            <option value="Level 1" {{ ($p['level'] ?? '') === 'Level 1' ? 'selected' : '' }}>Level 1</option>
                                            <option value="Level 2" {{ ($p['level'] ?? '') === 'Level 2' ? 'selected' : '' }}>Level 2</option>
                                            <option value="Level 3" {{ ($p['level'] ?? '') === 'Level 3' ? 'selected' : '' }}>Level 3</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-2 border-b">
                                        <button type="button" class="save-btn px-4 py-1 rounded text-sm font-semibold border transition disabled:opacity-50 disabled:cursor-not-allowed"
                                            style="color:#9ca3af;background:#f3f4f6;border:1px solid #d1d5db;"
                                            disabled>
                                            Save
                                        </button>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="px-4 py-2 border-b text-center">{{ $row++ }}</td>
                                    <td class="px-4 py-2 border-b" style="white-space:normal;word-break:break-word;">{{ $child->name }}</td>
                                    <td class="px-4 py-2 border-b">{{ trim(($child->year ? $child->year : '') . ' ' . ($child->class_name ?? '')) }}</td>
                                    <td class="px-4 py-2 border-b">
                                        <span class="text-gray-700">{{ $p['progress'] ?? 'Not recorded' }}</span>
                                    </td>
                                    <td class="px-4 py-2 border-b">
                                        <span class="text-gray-700">{{ $p['level'] ?? 'Not recorded' }}</span>
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to check if row is valid
    function checkRowValidity(row) {
        const progressInput = row.querySelector('.progress-input');
        const levelSelect = row.querySelector('.level-select');
        const saveBtn = row.querySelector('.save-btn');
        
        if (progressInput && levelSelect && saveBtn) {
            const progressValue = progressInput.value.trim();
            const levelValue = levelSelect.value;
            
            const isValid = progressValue !== '' && levelValue !== '';
            
            saveBtn.disabled = !isValid;
            
            if (isValid) {
                saveBtn.style.color = '#3182ce';
                saveBtn.style.background = '#e6f0fa';
                saveBtn.style.border = '1px solid #bcdffb';
                saveBtn.style.cursor = 'pointer';
            } else {
                saveBtn.style.color = '#9ca3af';
                saveBtn.style.background = '#f3f4f6';
                saveBtn.style.border = '1px solid #d1d5db';
                saveBtn.style.cursor = 'not-allowed';
            }
        }
    }
    
    // Function to handle save button click
    function handleSaveClick(event) {
        const row = event.target.closest('.progress-row');
        const progressInput = row.querySelector('.progress-input');
        const levelSelect = row.querySelector('.level-select');
        const childId = row.dataset.childId;
        
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('child_id', childId);
        formData.append('date', '{{ $selectedDate }}');
        formData.append('progress', progressInput.value);
        formData.append('level', levelSelect.value);
        
        fetch('/progress/save', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated status
                window.location.reload();
            } else {
                alert('Error saving progress: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving progress');
        });
    }
    
    // Add event listeners to all progress rows
    const rows = document.querySelectorAll('.progress-row');
    
    rows.forEach((row) => {
        const progressInput = row.querySelector('.progress-input');
        const levelSelect = row.querySelector('.level-select');
        const saveBtn = row.querySelector('.save-btn');
        
        if (progressInput) {
            progressInput.addEventListener('input', () => checkRowValidity(row));
            progressInput.addEventListener('blur', () => checkRowValidity(row));
        }
        
        if (levelSelect) {
            levelSelect.addEventListener('change', () => checkRowValidity(row));
            levelSelect.addEventListener('blur', () => checkRowValidity(row));
        }
        
        if (saveBtn) {
            saveBtn.addEventListener('click', handleSaveClick);
        }
        
        // Initial check
        checkRowValidity(row);
    });
});
</script>
@endsection 