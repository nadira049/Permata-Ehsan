@extends('layouts.app')
@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Child</h1>
    <form action="{{ route('child.update', ['child' => $child->id]) }}" method="POST" class="max-w-md mx-auto bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block font-semibold mb-2">Name</label>
            <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" value="{{ $child->name }}" required>
        </div>
        <div class="mb-4">
            <label for="year" class="block font-semibold mb-2">Year</label>
            <select name="year" id="year" class="w-full border px-3 py-2 rounded" required>
                <option value="">-- Select Year --</option>
                @foreach(['Year 4', 'Year 5', 'Year 6'] as $yearOption)
                    <option value="{{ $yearOption }}" @if($child->year == $yearOption) selected @endif>{{ $yearOption }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        <a href="{{ route('child.index') }}" class="ml-2 text-gray-600">Cancel</a>
    </form>
@endsection 