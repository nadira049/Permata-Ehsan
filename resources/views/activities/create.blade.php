@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded shadow p-6 mt-8">
    <h1 class="text-2xl font-bold mb-4">Create Activity</h1>
    <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Activity Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name') }}" required>
            @error('name')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            @error('description')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Date</label>
            <input type="date" name="date" class="w-full border rounded px-3 py-2" value="{{ old('date') }}" required>
            @error('date')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Class Group</label>
            <select name="year" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Select Class --</option>
                @foreach($classGroups as $group)
                    <option value="{{ $group }}" {{ old('year') == $group ? 'selected' : '' }}>{{ $group }}</option>
                @endforeach
            </select>
            @error('year')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Images <span class="text-xs text-gray-500">(min 3, jpg/png/gif, max 5MB each)</span></label>
            <input type="file" name="images[]" class="w-full border rounded px-3 py-2" accept="image/*" multiple required>
            @error('images')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            @error('images.*')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex gap-2 mt-6">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-bold px-6 py-2 rounded">Save</button>
            <a href="{{ route('activities.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-6 py-2 rounded">Back</a>
        </div>
    </form>
</div>
@endsection 