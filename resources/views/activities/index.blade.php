@extends('layouts.app')

@section('content')
<div x-data="{ showAdd: false, showEdit: false, showDelete: false, editActivity: null, lightboxOpen: false, lightboxImg: '', page: 1, perPage: 9, totalPages: 1 }" x-init='showAdd = false; showEdit = false; showDelete = false; editActivity = null' class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow">
    @php 
        $role = Auth::user()->role ?? ''; 
        $user = Auth::user();
        $years = ['Year 4', 'Year 5', 'Year 6'];
        $selectedYear = request('year', null);
        if ($role === 'parent') {
            $childYears = $user->children->pluck('year')->unique()->toArray();
            $filterYears = array_intersect($years, $childYears);
        } else {
            $filterYears = $years;
        }
    @endphp
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <h1 class="text-2xl font-bold font-sans">Activities</h1>
        @if($role === 'admin' || $role === 'teacher')
            <button @click="showAdd = true" class="px-6 py-2" style="background-color:#22303a; color:white; font-weight:600; border-radius:0.5rem; box-shadow:0 1px 4px #0001; transition:background 0.2s;" onmouseover="this.style.backgroundColor='#34495e'" onmouseout="this.style.backgroundColor='#22303a'">+ Add Activity</button>
        @endif
    </div>
    <!-- Filter Buttons -->
    <form method="GET" class="flex gap-4 justify-center mb-8 flex-wrap">
        <button type="submit" name="year" value="" class="px-6 py-2 rounded-lg font-bold text-lg transition bg-gray-200 hover:bg-blue-200 {{ $selectedYear === null ? 'ring-4 ring-blue-300 scale-105' : '' }}">All</button>
        @foreach($filterYears as $year)
            <button type="submit" name="year" value="{{ $year }}" class="px-6 py-2 rounded-lg font-bold text-lg transition bg-gray-200 hover:bg-blue-200 {{ $selectedYear === $year ? 'ring-4 ring-blue-300 scale-105' : '' }}">{{ $year }}</button>
        @endforeach
    </form>
    @if($role === 'admin' || $role === 'teacher')
    <!-- Add Activity Modal -->
        <div x-show="showAdd" x-cloak class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-30">
            <div @click.away="showAdd = false" class="bg-white rounded-xl shadow-lg mx-auto my-auto p-6 relative flex flex-col items-center w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Add Activity</h2>
                <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data" class="w-full space-y-3">
                @csrf
                    <div>
                <label class="block mb-1 text-sm font-medium">Activity Name</label>
                        <input type="text" name="name" class="border rounded px-2 py-1 w-full text-sm" required>
                        @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                <label class="block mb-1 text-sm font-medium">Date</label>
                        <input type="date" name="date" class="border rounded px-2 py-1 w-full text-sm" required>
                        @error('date')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">Year</label>
                        <select name="year" class="border rounded px-2 py-1 w-full text-sm" required>
                            <option value="">-- Select Year --</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                        @error('year')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                <label class="block mb-1 text-sm font-medium">Description</label>
                        <textarea name="description" class="border rounded px-2 py-1 w-full text-sm"></textarea>
                        @error('description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">Images <span class="text-xs text-gray-500">(min 1, jpg/png/gif, max 5MB each)</span></label>
                        <input type="file" name="images[]" class="border rounded px-2 py-1 w-full text-sm" accept="image/*" multiple required>
                        @error('images')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        @error('images.*')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex gap-2 mt-2 justify-center">
                    <button type="submit" class="bg-gray-800 text-white font-bold py-1 px-4 rounded text-sm hover:bg-gray-900">Save</button>
                    <button type="button" @click="showAdd = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-4 rounded text-sm">Back</button>
                </div>
            </form>
        </div>
    </div>
        <!-- Edit Activity Modal -->
        <template x-if="showEdit && editActivity">
            <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-30" x-cloak>
                <div class="bg-white rounded-xl shadow-lg mx-auto my-auto p-6 relative flex flex-col items-center w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4">Edit Activity</h2>
                    <form :action="'/activities/' + editActivity.id" method="POST" enctype="multipart/form-data" class="w-full space-y-3" x-data="{ removedImages: [], newImages: [] }">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block mb-1 text-sm font-medium">Activity Name</label>
                            <input type="text" name="name" x-model="editActivity.name" class="border rounded px-2 py-1 w-full text-sm" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Date</label>
                            <input type="date" name="date" x-model="editActivity.date" class="border rounded px-2 py-1 w-full text-sm" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Year</label>
                            <select name="year" x-model="editActivity.year" class="border rounded px-2 py-1 w-full text-sm" required>
                                <option value="">-- Select Year --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Description</label>
                            <textarea name="description" x-model="editActivity.description" class="border rounded px-2 py-1 w-full text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Current Images</label>
                            <div class="flex gap-2 flex-wrap mb-2">
                                <template x-for="img in editActivity.images || []" :key="img.id">
                                    <div class="relative">
                                        <img :src="img.url" class="h-16 w-16 object-cover rounded border">
                                        <button type="button" @click="removedImages.push(img.id); editActivity.images = editActivity.images.filter(i => i.id !== img.id)" class="absolute top-0 right-0 bg-red-600 text-white rounded-full px-1 text-xs">&times;</button>
                                        <template x-if="removedImages.includes(img.id)">
                                            <input type="hidden" name="remove_images[]" :value="img.id">
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!editActivity.images || editActivity.images.length === 0">
                                    <span class="text-gray-400 text-xs">No images</span>
                            </template>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Add New Images</label>
                            <input type="file" name="images[]" class="border rounded px-2 py-1 w-full text-sm" accept="image/*" multiple>
                        </div>
                        <div class="flex gap-2 mt-2 justify-center">
                            <button type="submit" class="bg-gray-800 text-white font-bold py-1 px-4 rounded text-sm hover:bg-gray-900">Update</button>
                            <button type="button" @click="showEdit = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-4 rounded text-sm">Back</button>
                        </div>
                            </form>
                        </div>
                    </div>
                </template>
        <!-- Delete Activity Modal -->
        <template x-if="showDelete && editActivity">
            <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-30" x-cloak>
                <div class="bg-white rounded-xl shadow-lg mx-auto my-auto p-6 relative flex flex-col items-center w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4">Delete Activity</h2>
                    <p class="mb-4">Are you sure you want to delete <span class="font-bold" x-text="editActivity.name"></span>?</p>
                    <form :action="'/activities/' + editActivity.id" method="POST" class="w-full flex flex-col items-center">
                        @csrf
                        @method('DELETE')
                        <div class="flex gap-2 mt-2 justify-center">
                            <button type="submit" class="bg-red-500 text-white font-bold py-1 px-4 rounded text-sm hover:bg-red-600">Yes, Delete</button>
                            <button type="button" @click="showDelete = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-4 rounded text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif
    <!-- Lightbox Modal -->
    <template x-if="lightboxOpen">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70" x-cloak @click.self="lightboxOpen = false">
            <div class="relative">
                <img :src="lightboxImg" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white">
                <button @click="lightboxOpen = false" class="absolute top-2 right-2 bg-white text-black rounded-full px-2 py-1 text-lg font-bold">&times;</button>
            </div>
        </div>
    </template>
    <!-- Activities List -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($activities as $activity)
            <div class="bg-white rounded shadow p-4 flex flex-col">
                <div class="flex gap-2 mb-2 overflow-x-auto">
                    @foreach($activity->images as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" class="h-32 w-32 object-cover rounded border cursor-pointer" @click="lightboxOpen = true; lightboxImg = '{{ asset('storage/' . $img->image_path) }}'">
                    @endforeach
                </div>
                <div class="font-bold text-lg mb-1">{{ $activity->name }}</div>
                <div class="text-sm text-gray-500 mb-1">{{ $activity->date }}</div>
                <div class="text-xs text-gray-700 mb-2">{{ $activity->description }}</div>
                <div class="text-xs text-gray-500 mb-2">Year: {{ $activity->year }}</div>
                <div class="flex gap-2 mt-auto">
                    @if($role === 'admin' || $role === 'teacher')
                        <button type="button"
                            @click="showEdit = true; editActivity = {{ json_encode([
                                'id' => $activity->id,
                                'name' => $activity->name,
                                'date' => $activity->date,
                                'year' => $activity->year,
                                'description' => $activity->description,
                                'images' => $activity->images->map(function($img) { return [ 'id' => $img->id, 'url' => asset('storage/' . $img->image_path) ]; })
                            ]) }}"
                            class="px-3 py-1 bg-yellow-200 text-yellow-900 rounded text-xs font-semibold hover:bg-yellow-300">
                            Edit
                        </button>
                        <button type="button"
                            @click="showDelete = true; editActivity = {{ json_encode([
                                'id' => $activity->id,
                                'name' => $activity->name
                            ]) }}"
                            class="px-3 py-1 bg-red-500 text-white rounded text-xs font-semibold hover:bg-red-600">
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6 flex items-center justify-between">
        <div class="text-gray-400 text-sm">
            Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} results
        </div>
        <div>
            {{ $activities->links('vendor.pagination.custom-dark') }}
        </div>
    </div>
</div>
@endsection 