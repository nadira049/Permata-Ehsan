@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow">
    <div x-data="{
        show: false,
        showEdit: false,
        editMaterial: null,
        showDelete: false,
        deleteMaterialId: null,
        openEdit(material) {
            this.editMaterial = {...material};
            this.showEdit = true;
        },
        openDelete(id) {
            this.deleteMaterialId = id;
            this.showDelete = true;
        },
        page: 1,
        pageSize: 10,
        get paginatedMaterials() {
            return this.filteredMaterials.slice((this.page-1)*this.pageSize, this.page*this.pageSize);
        },
        get totalPages() {
            return Math.ceil(this.filteredMaterials.length / this.pageSize) || 1;
        },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        prevPage() { if (this.page > 1) this.page--; },
    }">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Learning Materials</h1>
            @if(Auth::user() && in_array(Auth::user()->role, ['teacher', 'admin']))
            <button @click="show = true" type="button" class="bg-[#25313C] text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-800 transition">+ Add Material</button>
            @endif
        </div>
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
<div class="flex gap-4 mb-6">
    <a href="?" class="px-6 py-2 rounded-lg font-bold text-lg transition {{ empty($selectedYear) ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">All Years</a>
    <a href="?year=Year 4" class="px-6 py-2 rounded-lg font-bold text-lg transition {{ $selectedYear === 'Year 4' ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">Year 4</a>
    <a href="?year=Year 5" class="px-6 py-2 rounded-lg font-bold text-lg transition {{ $selectedYear === 'Year 5' ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">Year 5</a>
    <a href="?year=Year 6" class="px-6 py-2 rounded-lg font-bold text-lg transition {{ $selectedYear === 'Year 6' ? 'bg-blue-200 text-blue-900' : 'bg-gray-200 text-black' }}">Year 6</a>
</div>
        <div class="bg-white rounded shadow p-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left">No.</th>
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">Content</th>
                        <th class="px-4 py-2 text-left">Year</th>
                        <th class="px-4 py-2 text-left">File</th>
                        @if(Auth::user() && in_array(Auth::user()->role, ['teacher', 'admin']))
                        <th class="px-4 py-2 text-left">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($learningMaterials as $i => $material)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ ($learningMaterials->currentPage() - 1) * $learningMaterials->perPage() + $i + 1 }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $material->title }}</td>
                            <td class="px-4 py-2">{{ $material->content }}</td>
                            <td class="px-4 py-2">{{ $material->year }}</td>
                            <td class="px-4 py-2">
                                @if($material->file_path)
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-blue-600 underline">Download</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            @if(Auth::user() && in_array(Auth::user()->role, ['teacher', 'admin']))
                            <td class="px-4 py-2 flex gap-2">
                                <button @click="openEdit(@js($material))" class="px-4 py-2 rounded bg-blue-100 text-blue-700 font-semibold border border-blue-200 hover:bg-blue-200 transition">Edit</button>
                                <button @click="openDelete({{ $material->id }})" class="px-4 py-2 rounded bg-red-100 text-red-700 font-semibold border border-red-200 hover:bg-red-200 transition">Delete</button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">No learning materials for {{ $selectedYear }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex items-center justify-between">
            <div class="text-gray-400 text-sm">
                Showing {{ $learningMaterials->firstItem() }} to {{ $learningMaterials->lastItem() }} of {{ $learningMaterials->total() }} results
            </div>
            <div>
                {{ $learningMaterials->appends(request()->query())->links('vendor.pagination.custom-dark') }}
            </div>
        </div>

        <template x-if="show">
            <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
                <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg relative">
                    <button @click="show = false" class="absolute top-2 right-2 text-gray-500 hover:text-black text-2xl">&times;</button>
                    <h2 class="text-2xl font-bold mb-4">Upload Learning Material</h2>
                    <form action="{{ route('learning-materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block font-semibold mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Content</label>
                            <textarea name="content" class="w-full border rounded px-3 py-2" rows="4"></textarea>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Year <span class="text-red-500">*</span></label>
                            <select name="year" class="w-full border rounded px-3 py-2" required>
                                @php $selectedYear = request('year', 'Year 4'); @endphp
                                @foreach(['Year 4', 'Year 5', 'Year 6'] as $yearOption)
                                    <option value="{{ $yearOption }}" {{ $selectedYear === $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">File <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(PDF, DOC, DOCX, PPT, PPTX, MP4, JPEG, PNG only)</span></label>
                            <input type="file" name="file_path" class="w-full">
                        </div>
                        <div class="flex justify-center gap-2">
                            <button type="button" @click="show = false" class="px-4 py-2 rounded bg-[#e5e7eb] text-black font-semibold">Cancel</button>
                            <button type="submit" class="bg-[#25313C] hover:bg-gray-800 text-white font-bold py-2 px-6 rounded">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        <!-- Edit Modal -->
        <template x-if="showEdit && editMaterial">
            <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:10000;display:flex;align-items:center;justify-content:center;">
                <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg relative">
                    <button @click="showEdit = false" class="absolute top-2 right-2 text-gray-500 hover:text-black text-2xl">&times;</button>
                    <h2 class="text-2xl font-bold mb-4">Edit Learning Material</h2>
                    <form :action="'/learning-materials/' + editMaterial.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <div>
                            <label class="block font-semibold mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" class="w-full border rounded px-3 py-2" x-model="editMaterial.title" required>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Content</label>
                            <textarea name="content" class="w-full border rounded px-3 py-2" rows="4" x-model="editMaterial.content"></textarea>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Year <span class="text-red-500">*</span></label>
                            <select name="year" class="w-full border rounded px-3 py-2" x-model="editMaterial.year" required>
                                <option value="Year 4">Year 4</option>
                                <option value="Year 5">Year 5</option>
                                <option value="Year 6">Year 6</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">File (optional) <span class="text-xs text-gray-500">(PDF, DOC, DOCX, PPT, PPTX, MP4, JPEG, PNG only)</span></label>
                            <input type="file" name="file_path" class="w-full">
                            <template x-if="editMaterial.file_path">
                                <div class="mt-2">
                                    <a :href="'/storage/' + editMaterial.file_path" target="_blank" class="text-blue-600 underline">Current File</a>
                                </div>
                            </template>
                        </div>
                        <div class="flex justify-center gap-2">
                            <button type="button" @click="showEdit = false" class="px-4 py-2 rounded bg-[#e5e7eb] text-black font-semibold">Back</button>
                            <button type="submit" class="bg-[#25313C] hover:bg-gray-800 text-white font-bold py-2 px-6 rounded">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        <!-- Delete Modal -->
        <template x-if="showDelete">
            <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:11000;display:flex;align-items:center;justify-content:center;">
                <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative flex flex-col items-center">
                    <button @click="showDelete = false" class="absolute top-2 right-2 text-gray-500 hover:text-black text-2xl">&times;</button>
                    <h2 class="text-2xl font-bold mb-4">Delete Learning Material</h2>
                    <p class="mb-6 text-center">Are you sure you want to delete this learning material?</p>
                    <form :action="'/learning-materials/' + deleteMaterialId" method="POST" class="flex gap-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white font-bold hover:bg-red-700">Yes, Delete</button>
                        <button type="button" @click="showDelete = false" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
                </form>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection 