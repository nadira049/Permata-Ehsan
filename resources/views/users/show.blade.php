@extends('layouts.app')

@section('content')
    <h1>{{ $user->name }}</h1>
    <p>Email: {{ $user->email }}</p>
    <a href="{{ route('users.index') }}">Back to Users</a>
@endsection 