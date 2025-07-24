@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Upload Learning Material</h1>
    <form action="{{ route('learning-materials.store') }}" method="POST" enctype="multipart/form-data" class="max-w-lg bg-white p-6 rounded shadow space-y-4">
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
            <label class="block font-semibold mb-1">File (optional)</label>
            <input type="file" name="file_path" class="w-full">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">Upload</button>
    </form>
@endsection 