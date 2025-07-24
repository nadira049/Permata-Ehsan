@extends('layouts.app')

@section('content')
    <h1>{{ $activity->name }}</h1>
    <p>{{ $activity->description }}</p>
    <p>Date: {{ $activity->date }}</p>
    <p>Status: {{ $activity->status }}</p>
    <a href="{{ route('activities.index') }}">Back to Activities</a>
@endsection 