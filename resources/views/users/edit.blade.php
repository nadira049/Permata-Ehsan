@extends('layouts.app')

@section('content')
    <h1>Edit User</h1>
    <form action="{{ route('users.update', $user) }}" method="POST" style="max-width:400px;margin:auto;padding:2rem;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;">
        @csrf
        @method('PUT')
        <div style="margin-bottom:1rem;">
            <label for="name">Name:</label><br>
            <input type="text" name="name" id="name" value="{{ $user->name }}" class="form-control" style="width:100%;padding:8px;">
        </div>
        <div style="margin-bottom:1rem;">
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" value="{{ $user->email }}" class="form-control" style="width:100%;padding:8px;">
        </div>
        <div style="margin-bottom:1rem;">
            <label for="password">Password:</label> <small>(Leave blank to keep current password)</small><br>
            <input type="password" name="password" id="password" class="form-control" style="width:100%;padding:8px;">
        </div>
        <div style="margin-bottom:1rem;">
            <label for="role">Role:</label><br>
            <select name="role" id="role" class="form-control" style="width:100%;padding:8px;">
                <option value="parent" @if($user->role=='parent') selected @endif>Parent</option>
                <option value="teacher" @if($user->role=='teacher') selected @endif>Teacher</option>
                <option value="admin" @if($user->role=='admin') selected @endif>Admin</option>
            </select>
        </div>
        <button type="submit" style="padding:10px 20px;background:#2d3748;color:#fff;border:none;border-radius:4px;">Update</button>
    </form>
@endsection 