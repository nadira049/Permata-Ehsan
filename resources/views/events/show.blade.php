@extends('layouts.app')

@section('content')
    <h1>{{ $event->title }}</h1>
    <p>{{ $event->description }}</p>
    <p>Date: {{ $event->date }}</p>
    <p>Location: {{ $event->location }}</p>
    <a href="{{ route('events.index') }}">Back to Events</a>
@endsection 