@extends('layouts.app')
@section('content')
    <h1 class="text-2xl font-bold mb-4">Child Details</h1>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <div class="mb-4">
            <strong>Name:</strong> {{ $child->child_name }}
        </div>
        <div class="mb-4">
            <strong>Class:</strong> {{ $child->child_class }}
        </div>
        <a href="{{ route('child.index') }}" class="text-blue-600">Back to List</a>
    </div>
@endsection 