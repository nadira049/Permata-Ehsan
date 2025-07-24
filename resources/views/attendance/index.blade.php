@extends('layouts.app')

@section('content')
@php 
    $role = Auth::user()->role ?? '';
    $user = Auth::user();
    $years = ['4', '5', '6'];
    if ($role === 'parent') {
        $childYears = $user->children->pluck('class.year')->unique()->toArray();
        $filterYears = array_intersect($years, $childYears);
    } else {
        $filterYears = $years;
    }
@endphp
<div class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow"
     x-data="{
        children: @js($children),
        attendances: @js($attendances),
        date: '{{ $date }}',
        year: '{{ $year }}',
        page: 1,
        itemsPerPage: 10,
        searchQuery: '',
        classFilter: '',
        get filteredChildren() {
            let filtered = this.children;
            if (this.classFilter) {
                filtered = filtered.filter(child => child.class && child.class.name === this.classFilter);
            }
            if (this.searchQuery) {
                filtered = filtered.filter(child => child.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
            }
            return filtered;
        },
        get pagedChildren() {
            return this.filteredChildren.slice((this.page - 1) * this.itemsPerPage, this.page * this.itemsPerPage);
        },
        get totalPages() {
            return Math.ceil(this.filteredChildren.length / this.itemsPerPage) || 1;
        },
        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; }
     }">
    <h1 class="text-2xl font-bold font-sans mb-4">Attendance</h1>
    <div class="flex gap-2 mb-4 items-center">
        <a href="?year=&date={{ $date }}" :class="!year ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none">All Years</a>
        <a href="?year=4&date={{ $date }}" :class="year == 4 ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none">Year 4</a>
        <a href="?year=5&date={{ $date }}" :class="year == 5 ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none">Year 5</a>
        <a href="?year=6&date={{ $date }}" :class="year == 6 ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none">Year 6</a>
        <form method="GET" class="ml-auto">
            <input type="date" name="date" value="{{ $date ?? '' }}" max="{{ \Carbon\Carbon::now('Asia/Kuala_Lumpur')->toDateString() }}" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg" style="max-width:180px;">
            <input type="hidden" name="year" :value="year">
        </form>
    </div>
    <template x-if="year == 4 || year == 5 || year == 6">
        <div class="flex gap-2 mb-4 items-center">
            <button @click="classFilter = ''" :class="classFilter === '' ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none">All Classes</button>
            <template x-for="className in [...new Set(children.map(child => child.class ? child.class.name : ''))]" :key="className">
                <button @click="classFilter = className" :class="classFilter === className ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none" x-text="className"></button>
            </template>
            <input type="text" x-model="searchQuery" placeholder="Search by name..." style="width:220px;padding:8px;border:1px solid #ccc;border-radius:4px;">
        </div>
    </template>
    @if($role === 'teacher')
    <template x-if="filteredChildren.length > 0 && filteredChildren.some(child => !attendances[child.id] || !attendances[child.id].confirmed)">
        <div class="flex items-center gap-2 mb-4 p-3 bg-orange-50 border-l-4 border-orange-400 text-orange-700 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-orange-400"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2.25m0 3.75h.008v.008H12v-.008zm.008-9.008a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="font-semibold" x-text="(date === new Date().toLocaleDateString('en-CA')) ? 'Please complete attendance for today.' : 'You missed attendance for this date.'"></span>
        </div>
    </template>
    @endif
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">No</th>
                    <th class="px-4 py-2 border-b">Name</th>
                    <th class="px-4 py-2 border-b">Class</th>
                    <th class="px-4 py-2 border-b">Status</th>
                    <th class="px-4 py-2 border-b">Time</th>
                    <th class="px-4 py-2 border-b">Comment</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(child, idx) in pagedChildren" :key="child.id">
                    <tr>
                        <td class="px-4 py-2 border-b text-center"><span x-text="(page - 1) * itemsPerPage + idx + 1"></span></td>
                        <td class="px-4 py-2 border-b" x-text="child.name"></td>
                        <td class="px-4 py-2 border-b" x-text="(child.class && child.class.year ? child.class.year + ' ' : '') + (child.class ? child.class.name : '')"></td>
                        <td class="px-4 py-2 border-b text-center">
                            <div x-data="{ saving: false, saved: false, confirmed: attendances[child.id] && attendances[child.id].confirmed }">
                                <template x-if="attendances[child.id] && attendances[child.id].confirmed">
                                    <span class="font-semibold text-green-600" x-text="attendances[child.id].status"></span>
                                </template>
                                @if($role === 'teacher')
                                <template x-if="!attendances[child.id] || !attendances[child.id].confirmed">
                                    <div class="flex items-center gap-2">
                                        <select class="border rounded-lg px-3 py-2 pr-8 bg-gray-100 focus:ring-2 focus:ring-blue-300 transition" :value="attendances[child.id] ? attendances[child.id].status : ''"
                                            :disabled="confirmed"
                                            @change="
                                                attendances[child.id] = attendances[child.id] || { status: '', comment: '', confirmed: false };
                                                attendances[child.id].status = $event.target.value;
                                                saving = false; saved = false;
                                            "
                                        >
                                            <option value="" disabled hidden>Select Status</option>
                                            <option value="Attend">Attend</option>
                                            <option value="Absent">Absent</option>
                                            <option value="Late">Late</option>
                                        </select>
                                        <template x-if="attendances[child.id] && attendances[child.id].status">
                                            <button type="button" class="px-3 py-1 rounded text-xs font-semibold border transition bg-blue-600 text-white hover:bg-blue-800" @click.prevent="
                                                saving = true;
                                                fetch(`/attendance/${child.id}/status`, {
                                                    method: 'PATCH',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json',
                                                    },
                                                    body: JSON.stringify({
                                                        status: attendances[child.id].status,
                                                        date: date
                                                    })
                                                })
                                                .then(r => r.json())
                                                .then(data => {
                                                    if (data.attendance) {
                                                        attendances[child.id] = data.attendance;
                                                    }
                                                    saving = false;
                                                });
                                            ">Confirm</button>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="saving">
                                    <span class="ml-2 text-gray-400">...</span>
                                </template>
                                <template x-if="saved">
                                    <span class="ml-2 text-green-500">&#10003;</span>
                                </template>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-2 border-b text-center">
                            <template x-if="attendances[child.id] && attendances[child.id].time">
                                <span x-text="new Date(attendances[child.id].time).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })"></span>
                            </template>
                            <template x-if="!attendances[child.id] || !attendances[child.id].time">
                                <span>-</span>
                            </template>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <div x-data="{ saving: false, saved: false }">
                                @if($role === 'teacher')
                                <input type="text" class="border rounded-lg px-3 py-2 w-full bg-gray-50 focus:ring-2 focus:ring-blue-300 transition" :value="attendances[child.id] ? attendances[child.id].comment || '' : ''"
                                    @input.debounce.500ms="
                                        attendances[child.id] = attendances[child.id] || { status: '', comment: '', confirmed: false };
                                        attendances[child.id].comment = $event.target.value;
                                        saving = true;
                                        saved = false;
                                        // Optionally, send AJAX to save comment here
                                        setTimeout(() => { saving = false; saved = true; }, 1000);
                                    "
                                    placeholder="Add comment..."
                                >
                                <template x-if="saving">
                                    <span class="ml-2 text-gray-400">...</span>
                                </template>
                                <template x-if="saved">
                                    <span class="ml-2 text-green-500">&#10003;</span>
                                </template>
                                @else
                                <span x-text="attendances[child.id] && attendances[child.id].comment ? attendances[child.id].comment : '-'" class="block"></span>
                                @endif
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div class="flex justify-center items-center gap-4 mt-6">
        <button @click="prevPage" :disabled="page === 1" class="px-6 py-2 rounded-lg bg-gray-100 text-gray-400 font-semibold" :class="{'opacity-50': page === 1}">Previous</button>
        <span class="font-semibold">Page <span x-text="page"></span> of <span x-text="totalPages"></span></span>
        <button @click="nextPage" :disabled="page === totalPages" class="px-6 py-2 rounded-lg bg-gray-100 text-gray-600 font-semibold" :class="{'opacity-50': page === totalPages}">Next</button>
    </div>
</div>
@endsection 