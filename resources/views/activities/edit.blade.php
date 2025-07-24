@extends('layouts.app')

@section('content')
    <h1>Edit Activity</h1>
    <form action="{{ route('activities.update', $activity) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Name: <input type="text" name="name" value="{{ $activity->name }}"></label><br>
        <label>Description: <textarea name="description">{{ $activity->description }}</textarea></label><br>
        <label>Date: <input type="date" name="date" value="{{ $activity->date }}"></label><br>
        <label>Status: <input type="text" name="status" value="{{ $activity->status }}"></label><br>
        <button type="submit">Update</button>
    </form>
@endsection 