@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<div x-data="{
    search: '',
    page: 1,
    pageSize: 10,
    classFilter: '',
    children: @js($children),
    get filteredChildren() {
        let filtered = this.children;
        if (this.classFilter) {
            filtered = filtered.filter(child => child.class && child.class.name === this.classFilter);
        }
        if (this.search) {
            filtered = filtered.filter(child => child.name.toLowerCase().includes(this.search.toLowerCase()));
        }
        return filtered;
    },
    get paginatedChildren() {
        const start = (this.page - 1) * this.pageSize;
        return this.filteredChildren.slice(start, start + this.pageSize);
    },
    get totalPages() {
        return Math.ceil(this.filteredChildren.length / this.pageSize) || 1;
    },
    nextPage() {
        if (this.page < this.totalPages) this.page++;
    },
    prevPage() {
        if (this.page > 1) this.page--;
    },
    resetPage() {
        this.page = 1;
    }
}"
    x-effect="search; classFilter; resetPage()"
>
    <div class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold font-sans mb-4">Children in Year {{ $year }}</h1>
        <a href="{{ route('child.index') }}" class="mb-4 inline-block text-blue-600 hover:underline">&larr; Back to All Children</a>
        <div style="margin-bottom:1rem;display:flex;gap:1rem;align-items:center;">
            <div class="flex gap-2">
                <button @click="classFilter = ''" :class="classFilter === '' ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none">All Classes</button>
                @foreach($classrooms as $classroom)
                    <button @click="classFilter = '{{ $classroom->name }}'" :class="classFilter === '{{ $classroom->name }}' ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black'" class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none">{{ $classroom->name }}</button>
                @endforeach
            </div>
            <input type="text" x-model="search" placeholder="Search by name..." style="width:220px;padding:8px;border:1px solid #ccc;border-radius:4px;">
        </div>
        <div style="max-height:400px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f7fafc;">
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;">No</th>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;">Name</th>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;">Class</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(child, idx) in paginatedChildren" :key="child.id">
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:center;">
                                <span x-text="(page - 1) * pageSize + idx + 1"></span>
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0;background:#f9f9fb;border-radius:6px;">
                                <div style="padding:6px 10px;background:#fff;border:1px solid #e2e8f0;border-radius:6px;box-shadow:0 1px 2px #0001;white-space:nowrap;overflow-x:auto;max-width:220px;">
                                    <span x-text="child.name"></span>
                                </div>
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0;background:#f9f9fb;border-radius:6px;">
                                <div style="padding:6px 10px;background:#fff;border:1px solid #e2e8f0;border-radius:6px;box-shadow:0 1px 2px #0001;white-space:nowrap;overflow-x:auto;max-width:220px;">
                                    <span x-text="child.class ? child.class.name : '-' "></span>
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
</div>
@endsection 