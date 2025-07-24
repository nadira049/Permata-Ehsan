@extends('layouts.app')

@section('content')
    <h1>Create User</h1>
    <form action="{{ route('users.store') }}" method="POST" style="max-width:400px;margin:auto;padding:2rem;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;">
        @csrf
        <div style="margin-bottom:1rem;">
            <label for="name">Name:</label><br>
            <input type="text" name="name" id="name" class="form-control" style="width:100%;padding:8px;" required>
        </div>
        <div style="margin-bottom:1rem;">
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" class="form-control" style="width:100%;padding:8px;" required>
        </div>
        <div style="margin-bottom:1rem;">
            <label for="password">Password:</label><br>
            <input type="password" name="password" id="password" class="form-control" style="width:100%;padding:8px;" required>
        </div>
        <div style="margin-bottom:1rem;">
            <label for="address">Address:</label><br>
            <input type="text" name="address" id="address" class="form-control" style="width:100%;padding:8px;" required>
        </div>
        <div style="margin-bottom:1rem;">
            <label for="phone">Phone Number:</label><br>
            <input type="tel" name="phone" id="phone" class="form-control" style="width:100%;padding:8px;" required>
        </div>
        <div style="margin-bottom:1rem;">
            <label for="profile_picture">Profile Picture:</label><br>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control" style="width:100%;padding:8px;" required>
        </div>
        <div style="margin-bottom:1rem;">
            <label for="role">Role:</label><br>
            <select name="role" id="role" class="form-control" style="width:100%;padding:8px;" required>
                <option value="parent">Parent</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" style="padding:10px 20px;background:#2d3748;color:#fff;border:none;border-radius:4px;">Save</button>
        <a href="{{ route('users.index') }}" style="margin-left:10px;padding:10px 20px;background:#e2e8f0;color:#2d3748;border:none;border-radius:4px;text-decoration:none;display:inline-block;">Back</a>
    </form>
@endsection 