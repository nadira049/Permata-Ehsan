@extends('layouts.app')
@section('content')
    <h1 class="text-2xl font-bold mb-4">Add Child</h1>
    <form action="{{ route('child.store') }}" method="POST" class="max-w-md mx-auto bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label for="name" class="block font-semibold mb-2">Child Name</label>
            <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div class="mb-4">
            <label for="class_id" class="block font-semibold mb-2">Class</label>
            <select name="class_id" id="class_id" class="w-full border px-3 py-2 rounded" required>
                <option value="">-- Select Class --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->year }} {{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2" style="background-color:#22303a; color:white; font-weight:600; border-radius:0.5rem; box-shadow:0 1px 4px #0001; transition:background 0.2s;" onmouseover="this.style.backgroundColor='#34495e'" onmouseout="this.style.backgroundColor='#22303a'">Save</button>
        <a href="{{ route('child.index') }}" class="ml-2 text-gray-600">Cancel</a>
    </form>
@endsection 