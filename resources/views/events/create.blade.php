@extends('layouts.app')

@section('content')
    <h1>Create Event</h1>
    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <label>Title: <input type="text" name="title"></label><br>
        <label>Description: <textarea name="description"></textarea></label><br>
        <label>Date: <input type="date" name="date"></label><br>
        <label>Location: <input type="text" name="location"></label><br>
        <button type="submit">Save</button>
    </form>
@endsection 