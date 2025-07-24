@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<div x-data="{
    showEdit: false,
    editChild: null,
    showCreate: false,
    showDelete: false,
    deleteChildId: null,
    search: '',
    year: '',
    page: 1,
    pageSize: 10,
    children: @js($children),
    get filteredChildren() {
        let filtered = this.children;
        if (this.year && this.year !== 'All') {
            filtered = filtered.filter(child => child.year === this.year);
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
    },
    openEdit(child) {
        this.editChild = {...child};
        this.showEdit = true;
    },
    openDelete(childId) {
        this.deleteChildId = childId;
        this.showDelete = true;
    }
}"
    x-effect="search; year; resetPage()"
>
    <div class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold font-sans mb-4">Children</h1>
        @php $role = Auth::user()->role ?? ''; @endphp
        @if ($role !== 'teacher')
            <button @click="showCreate = true" style="padding:8px 16px;background:#2d3748;color:#fff;border:none;border-radius:4px;text-decoration:none;margin-bottom:1rem;display:inline-block;">Create Child</button>
        @endif
        <div style="margin-bottom:1rem;display:flex;gap:1rem;align-items:center;">
            <input type="text" x-model="search" placeholder="Search by name..." style="width:220px;padding:8px;border:1px solid #ccc;border-radius:4px;">
            <div class="flex gap-2 mb-4">
                <a href="{{ route('child.index') }}"
                   class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ request('year') ? 'bg-gray-200 text-black' : 'bg-blue-200 text-blue-900' }}">
                    All Years
                </a>
                <a href="{{ route('children.byYear', ['year' => 4]) }}"
                   class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ request()->is('children/year/4') ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
                    Year 4
                </a>
                <a href="{{ route('children.byYear', ['year' => 5]) }}"
                   class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ request()->is('children/year/5') ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
                    Year 5
                </a>
                <a href="{{ route('children.byYear', ['year' => 6]) }}"
                   class="px-4 py-2 rounded-lg font-bold text-lg transition focus:outline-none {{ request()->is('children/year/6') ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">
                    Year 6
                </a>
            </div>
        </div>
        <div style="max-height:400px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f7fafc;">
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;width:40px;">No</th>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;">Name</th>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;">Class</th>
                        @if ($role !== 'teacher')
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;">Actions</th>
                        @endif
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
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0;" x-text="(child.year ? child.year + ' ' : '') + (child.class_name ?? '')"></td>
                            @if ($role !== 'teacher')
                            <td style="padding:10px;border-bottom:1px solid #e2e8f0;">
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <button @click="openEdit(child)" style="color:#3182ce;padding:6px 14px;background:#e6f0fa;border-radius:4px;border:1px solid #bcdffb;text-decoration:none;display:inline-block;">Edit</button>
                                    <button @click="openDelete(child.id)" style="color:#e53e3e;background:#ffeaea;border:1px solid #fbb6b6;padding:6px 14px;border-radius:4px;cursor:pointer;display:inline-block;">Delete</button>
                                </div>
                            </td>
                            @endif
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
    @if ($role !== 'teacher')
    <!-- Create Modal -->
    <template x-if="showCreate">
        <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1100;display:flex;align-items:center;justify-content:center;" x-cloak>
            <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;width:400px;max-width:90vw;box-shadow:0 2px 16px #0003;position:relative;display:flex;flex-direction:column;align-items:center;">
                <h2 style="margin-bottom:1rem;text-align:center;">Create Child</h2>
                <form action="{{ route('child.store') }}" method="POST" style="width:100%;">
                    @csrf
                    <div style="margin-bottom:1rem;">
                        <label for="name">Name:</label><br>
                        <input type="text" name="name" id="name" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="year">Year:</label><br>
                        <select name="year" id="year" class="form-control" style="width:100%;padding:8px;" required>
                            <option value="">-- Select Year --</option>
                            @foreach($years as $yearOption)
                                <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex;justify-content:center;gap:10px;">
                        <button type="submit" style="padding:10px 20px;background:#2d3748;color:#fff;border:none;border-radius:4px;">Save</button>
                        <button type="button" @click="showCreate = false" style="padding:10px 20px;background:#e2e8f0;color:#2d3748;border:none;border-radius:4px;">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    <!-- Edit Modal -->
    <template x-if="showEdit && editChild">
        <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1200;display:flex;align-items:center;justify-content:center;" x-cloak>
            <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;width:400px;max-width:90vw;box-shadow:0 2px 16px #0003;position:relative;display:flex;flex-direction:column;align-items:center;">
                <h2 style="margin-bottom:1rem;text-align:center;">Edit Child</h2>
                <form :action="'/child/' + editChild.id" method="POST" style="width:100%;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <div style="margin-bottom:1rem;">
                        <label for="edit_name">Name:</label><br>
                        <input type="text" name="name" id="edit_name" class="form-control" style="width:100%;padding:8px;" x-model="editChild.name" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="edit_year">Year:</label><br>
                        <select name="year" id="edit_year" class="form-control" style="width:100%;padding:8px;" x-model="editChild.year" required>
                            <option value="">-- Select Year --</option>
                            <option value="Year 4">Year 4</option>
                            <option value="Year 5">Year 5</option>
                            <option value="Year 6">Year 6</option>
                        </select>
                    </div>
                    <div style="display:flex;justify-content:center;gap:10px;">
                        <button type="submit" style="padding:10px 20px;background:#2d3748;color:#fff;border:none;border-radius:4px;">Update</button>
                        <button type="button" @click="showEdit = false" style="padding:10px 20px;background:#e2e8f0;color:#2d3748;border:none;border-radius:4px;">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    <!-- Delete Modal -->
    <template x-if="showDelete">
        <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1300;display:flex;align-items:center;justify-content:center;" x-cloak>
            <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;width:350px;max-width:90vw;box-shadow:0 2px 16px #0003;position:relative;display:flex;flex-direction:column;align-items:center;">
                <h2 style="margin-bottom:1rem;text-align:center;">Delete Child</h2>
                <p style="margin-bottom:1.5rem;text-align:center;">Are you sure you want to delete this child?</p>
                <form :action="'/child/' + deleteChildId" method="POST" style="width:100%;display:flex;justify-content:center;gap:10px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" style="padding:10px 20px;background:#e53e3e;color:#fff;border:none;border-radius:4px;">Yes, Delete</button>
                    <button type="button" @click="showDelete = false" style="padding:10px 20px;background:#e2e8f0;color:#2d3748;border:none;border-radius:4px;">Cancel</button>
                </form>
            </div>
        </div>
    </template>
    @endif
    <!-- View Modal -->
    <template x-if="showView && viewChild">
        <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1000;display:flex;align-items:center;justify-content:center;" x-cloak>
            <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;width:400px;max-width:90vw;box-shadow:0 2px 16px #0003;position:relative;display:flex;flex-direction:column;align-items:center;">
                <h2 style="margin-bottom:1rem;text-align:center;">Child Details</h2>
                <div style="width:100%;margin-bottom:1rem;"><strong>Name:</strong> <span x-text="viewChild.name"></span></div>
                <div style="width:100%;margin-bottom:1rem;"><strong>Year:</strong> <span x-text="viewChild.year"></span></div>
                <div style="width:100%;margin-bottom:1rem;"><strong>Read:</strong> <span x-text="viewChild.read ? 'Yes' : 'No'"></span></div>
                <div style="width:100%;margin-bottom:1rem;"><strong>Comment:</strong> <span x-text="viewChild.comment"></span></div>
                <button type="button" @click="showView = false" style="padding:8px 18px;background:#e2e8f0;color:#2d3748;border:none;border-radius:4px;">Close</button>
            </div>
        </div>
    </template>
</div>
@endsection 