@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<div x-data="{ showEdit: false, editUser: null, showCreate: false, showNoResults: {{ (request('search') && $users->isEmpty()) ? 'true' : 'false' }} }">
    @if ($errors->has('username'))
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-30 z-50" x-data="{ showUsernameError: true }" x-show="showUsernameError">
            <div class="bg-white p-6 rounded-lg shadow-lg min-w-[300px] max-w-[90vw] flex flex-col items-center">
                <p class="mb-6 text-center text-lg text-red-600 font-semibold">This username is already taken. Please enter a new username.</p>
                <button type="button" @click="showUsernameError = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Close</button>
            </div>
        </div>
    @endif
    <!-- No Results Popup -->
    <template x-if="showNoResults">
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-30 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg min-w-[300px] max-w-[90vw] flex flex-col items-center">
                <p class="mb-6 text-center text-lg text-red-600 font-semibold">No users found for your search.</p>
                <button type="button" @click="showNoResults = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Close</button>
            </div>
        </div>
    </template>
    <div class="w-full max-w-7xl mx-auto mt-8 p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold font-sans mb-4">Users</h1>
        <button @click="showCreate = true" class="px-6 py-2 mb-4 bg-gray-800 text-white rounded font-semibold hover:bg-gray-900 transition">Create User</button>
        <form method="GET" class="mb-4 flex items-center gap-2">
            <label for="role_filter" class="font-semibold">Filter by Role:</label>
            <select name="role" id="role_filter" class="border rounded px-2 py-1 w-36" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="admin"{{ request('role') == 'admin' ? ' selected' : '' }}>Admin</option>
                <option value="parent"{{ request('role') == 'parent' ? ' selected' : '' }}>Parent</option>
                <option value="teacher"{{ request('role') == 'teacher' ? ' selected' : '' }}>Teacher</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by username, full name, or child name..." class="border rounded px-2 py-1 w-80" />
            <button type="submit" class="px-4 py-1 bg-blue-600 text-white rounded">Search</button>
            <a href="{{ route('users.index') }}" class="px-4 py-1 bg-gray-300 text-gray-800 rounded ml-2">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow border border-gray-300">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">No.</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Profile Picture</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Username</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Full Name</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Address</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Email</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Role</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Phone</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $index => $user)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2 border-r border-gray-200 text-center">{{ ($users->firstItem() ?? 0) + $index }}</td>
                        <td class="px-4 py-2 border-r border-gray-200">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" width="40" height="40" class="rounded-full">
                            @else
                                <span>No Image</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-r border-gray-200">{{ $user->username }}</td>
                        <td class="px-4 py-2 border-r border-gray-200">{{ $user->full_name }}</td>
                        <td class="px-4 py-2 border-r border-gray-200">{{ $user->address }}</td>
                        <td class="px-4 py-2 border-r border-gray-200">{{ $user->email }}</td>
                        <td class="px-4 py-2 border-r border-gray-200">{{ ucfirst($user->role) }}</td>
                        <td class="px-4 py-2 border-r border-gray-200">{{ $user->phone }}</td>
                        <td class="px-4 py-2">
                            <div class="flex gap-2 items-center">
                                <button @click="showEdit = true; editUser = {id: {{ $user->id }}, username: '{{ $user->username }}', full_name: '{{ $user->full_name }}', address: '{{ $user->address }}', phone: '{{ $user->phone }}', email: '{{ $user->email }}', role: '{{ $user->role }}'}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 border border-blue-200">Edit</button>
                                <div x-data="{ showDelete: false }" class="inline">
                                    <button type="button" @click="showDelete = true" class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 border border-red-200">Delete</button>
                                    <template x-if="showDelete">
                                        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-30 z-50" x-cloak>
                                            <div class="bg-white p-6 rounded-lg shadow-lg min-w-[300px] max-w-[90vw] flex flex-col items-center">
                                                <p class="mb-6 text-center text-lg">Are you sure you want to delete this user?</p>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex gap-4">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Yes, Delete</button>
                                                    <button type="button" @click="showDelete = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Cancel</button>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-data="{ showMore: false, showDeleteChild: false, deleteChildId: null }" class="inline">
                                    <button type="button" @click="showMore = true" class="px-3 py-1 bg-gray-800 text-white rounded hover:bg-gray-900 border border-gray-800">More</button>
                                    <template x-if="showMore">
                                        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-30 z-50" x-cloak>
                                            <div class="bg-white p-6 rounded-lg shadow-lg min-w-[320px] max-w-[90vw] flex flex-col items-center">
                                                <h3 class="mb-4 text-lg font-semibold">Children</h3>
                                                @php $children = $user->children; @endphp
                                                @if($children && count($children) > 0)
                                                    <div class="flex gap-4 flex-wrap justify-center w-full mb-4">
                                                        @foreach($children as $child)
                                                            <form method="POST" action="{{ route('users.update-child', [$user->id, $child->id]) }}" class="bg-gray-100 p-3 rounded shadow flex flex-col items-start min-w-[220px] gap-2">
                                                                @csrf
                                                                @method('PUT')
                                                                <div>
                                                                    <label class="font-semibold">Name:</label>
                                                                    <input type="text" name="name" value="{{ $child->name }}" class="form-control w-full" required>
                                                                </div>
                                                                <div>
                                                                    <label class="font-semibold">Class:</label>
                                                                    <select name="class_id" class="form-control w-full" required>
                                                                        @foreach($classes as $class)
                                                                            <option value="{{ $class->id }}" @if($child->class_id == $class->id) selected @endif>{{ $class->year }} {{ $class->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="flex gap-2 mt-2">
                                                                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Update</button>
                                                                    <button type="button" @click="showDeleteChild = true; deleteChildId = {{ $child->id }}" class="px-3 py-1 bg-red-600 text-white rounded">Remove</button>
                                                                </div>
                                                            </form>
                                                            <!-- Delete Child Modal -->
                                                            <template x-if="showDeleteChild && deleteChildId === {{ $child->id }}">
                                                                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-30 z-50" x-cloak>
                                                                    <div class="bg-white p-6 rounded-lg shadow-lg min-w-[320px] max-w-[90vw] flex flex-col items-center">
                                                                        <h3 class="mb-4 text-lg font-semibold">Delete Child</h3>
                                                                        <p class="mb-6">Are you sure you want to delete this child?</p>
                                                                        <form method="POST" action="{{ route('users.delete-child', [$user->id, $child->id]) }}">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <div class="flex gap-4">
                                                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Yes, Delete</button>
                                                                                <button type="button" @click="showDeleteChild = false; deleteChildId = null" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Cancel</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                            </div>
                                                            </template>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="mb-4">No children</div>
                                                @endif
                                                <form method="POST" action="{{ route('users.add-child', $user->id) }}" class="w-full flex flex-col items-center mb-4" x-data="{ children: [{name: '', class_id: ''}] }">
                                                    @csrf
                                                    <div class="flex justify-end w-full mb-2">
                                                        <button type="button" @click="children.push({name: '', class_id: ''})" class="px-6 py-2 bg-gray-800 text-white rounded font-semibold hover:bg-gray-900 transition">Add Child</button>
                                                    </div>
                                                    <template x-for="(child, idx) in children" :key="idx">
                                                        <div class="flex gap-2 mb-2 w-full">
                                                            <input type="text" :name="'child_names[]'" x-model="child.name" class="form-control w-full" placeholder="Child Name" required>
                                                            <select :name="'child_class_ids[]'" x-model="child.class_id" class="form-control w-full" required>
                                                                <option value="">Select Class</option>
                                                                @foreach($classes as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->year }} {{ $class->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" @click="children.splice(idx, 1)" class="px-2 py-1 bg-red-200 text-red-700 rounded" x-show="children.length > 1">Remove</button>
                                                        </div>
                                                    </template>
                                                    <div class="flex justify-center gap-4 w-full mt-4">
                                                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded font-semibold hover:bg-gray-900 transition">Save</button>
                                                        <button type="button" @click="showMore = false" class="px-6 py-2 bg-gray-200 text-gray-800 rounded">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
    <!-- Edit Modal OUTSIDE the table -->
    <template x-if="showEdit && editUser">
        <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1000;display:flex;align-items:center;justify-content:center;" x-cloak>
            <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;width:400px;max-width:90vw;box-shadow:0 2px 16px #0003;position:relative;display:flex;flex-direction:column;align-items:center; max-height:80vh; overflow-y:auto;">
                <h2 style="margin-bottom:1rem;text-align:center;">Edit User</h2>
                <form :action="'/users/' + editUser.id" method="POST" enctype="multipart/form-data" style="width:100%;">
                    @csrf
                    @method('PUT')
                    <div style="margin-bottom:1rem;">
                        <label>Username:</label><br>
                        <input type="text" name="username" x-model="editUser.username" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label>Full Name:</label><br>
                        <input type="text" name="full_name" x-model="editUser.full_name" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label>Address:</label><br>
                        <input type="text" name="address" x-model="editUser.address" class="form-control" style="width:100%;padding:8px;">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label>Phone Number:</label><br>
                        <input type="tel" name="phone" x-model="editUser.phone" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label>Email:</label><br>
                        <input type="email" name="email" x-model="editUser.email" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label>Password:</label> <small>(Leave blank to keep current password)</small><br>
                        <input type="password" name="password" class="form-control" style="width:100%;padding:8px;">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label>Role:</label><br>
                        <select name="role" x-model="editUser.role" class="form-control" style="width:100%;padding:8px;">
                            <option value="parent">Parent</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label>Profile Picture:</label><br>
                        <input type="file" name="profile_picture" class="form-control" style="width:100%;padding:8px;">
                        <template x-if="editUser.profile_picture">
                            <div style="margin-top:8px;">
                                <img :src="'/storage/' + editUser.profile_picture" alt="Profile Picture" width="40" height="40" style="border-radius:50%;">
                            </div>
                        </template>
                    </div>
                    <div style="display:flex;justify-content:center;gap:10px;">
                        <button type="submit" style="padding:10px 20px;background:#2d3748;color:#fff;border:none;border-radius:4px;">Update</button>
                        <button type="button" @click="showEdit = false" style="padding:10px 20px;background:#e2e8f0;color:#2d3748;border:none;border-radius:4px;">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    <!-- Create Modal OUTSIDE the table -->
    <template x-if="showCreate">
        <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1100;display:flex;align-items:center;justify-content:center;" x-cloak>
            <div style="background:#fff;padding:2rem 1.5rem;border-radius:12px;width:400px;max-width:90vw;box-shadow:0 2px 16px #0003;position:relative;display:flex;flex-direction:column;align-items:center; max-height:80vh; overflow-y:auto;">
                <h2 style="margin-bottom:1rem;text-align:center;">Create User</h2>
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" style="width:100%;">
                    @csrf
                    <div style="margin-bottom:1rem;">
                        <label for="username">Username:</label><br>
                        <input type="text" name="username" id="username" class="form-control" style="width:100%;padding:8px;" required>
                        <small style="color:#888;">Username must be unique. Only one account per username.</small>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="full_name">Full Name:</label><br>
                        <input type="text" name="full_name" id="full_name" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="address">Address:</label><br>
                        <input type="text" name="address" id="address" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="phone">Phone Number:</label><br>
                        <input type="tel" name="phone" id="phone" class="form-control" style="width:100%;padding:8px;" required placeholder="019-XXXXXXX" pattern="0[0-9]{2}-[0-9]{6,8}">
                        <small style="color:#888;">Format: 019-XXXXXXX</small>
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
                        <label for="role">Role:</label><br>
                        <select name="role" id="role" class="form-control" style="width:100%;padding:8px;">
                            <option value="parent">Parent</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="profile_picture">Profile Picture:</label><br>
                        <input type="file" name="profile_picture" id="profile_picture" class="form-control" style="width:100%;padding:8px;" required>
                    </div>
                    <div style="display:flex;justify-content:center;gap:10px;">
                        <button type="submit" style="padding:10px 20px;background:#2d3748;color:#fff;border:none;border-radius:4px;">Save</button>
                        <button type="button" @click="showCreate = false" style="padding:10px 20px;background:#e2e8f0;color:#2d3748;border:none;border-radius:4px;">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection 