@extends('layouts.app')

@section('content')
    <h1>Edit Event</h1>
    <form action="{{ route('events.update', $event) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Title: <input type="text" name="title" value="{{ $event->title }}"></label><br>
        <label>Description: <textarea name="description">{{ $event->description }}</textarea></label><br>
        <label>Date: <input type="date" name="date" value="{{ $event->date }}"></label><br>
        <label>Location: <input type="text" name="location" value="{{ $event->location }}"></label><br>
        <button type="submit">Update</button>
    </form>
@endsection 