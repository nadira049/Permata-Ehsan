<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Classroom;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();
        if (request('role')) {
            $query->where('role', request('role'));
        }
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%")
                  ->orWhereHas('children', function($childQ) use ($search) {
                      $childQ->where('name', 'like', "%$search%") ;
                  });
            });
        }
        $users = $query->paginate(10)->appends(request()->query());
        $classes = Classroom::orderBy('year')->orderBy('name')->get();
        return view('users.index', compact('users', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username',
            'full_name' => 'required',
            'address' => 'nullable',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,parent,teacher',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'username.unique' => 'This username is already taken. Please choose another.',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }
        User::create($validated);
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'full_name' => 'required',
            'address' => 'nullable',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,parent,teacher',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'username.unique' => 'This username is already taken. Please choose another.',
        ]);
        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }
        $user->username = $validated['username'];
        $user->full_name = $validated['full_name'];
        $user->address = $validated['address'];
        $user->phone = $validated['phone'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }
        $user->save();
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }

    public function addChild(Request $request, $userId)
    {
        $request->validate([
            'child_names' => 'required|array',
            'child_names.*' => 'required|string|max:255',
            'child_class_ids' => 'required|array',
            'child_class_ids.*' => 'required|exists:classes,id',
        ]);

        $user = \App\Models\User::findOrFail($userId);

        foreach ($request->child_names as $i => $name) {
            $class_id = $request->child_class_ids[$i] ?? null;
            if ($name && $class_id) {
                $user->children()->create([
                    'name' => $name,
                    'class_id' => $class_id,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Children added successfully!');
    }

    public function updateChild(Request $request, $userId, $childId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
        ]);

        $child = \App\Models\Child::findOrFail($childId);
        $child->update([
            'name' => $request->name,
            'class_id' => $request->class_id,
        ]);

        return redirect()->back()->with('success', 'Child updated successfully!');
    }

    public function deleteChild($userId, $childId)
    {
        $child = \App\Models\Child::findOrFail($childId);
        $child->delete();
        return redirect()->back()->with('success', 'Child deleted successfully!');
    }
}
