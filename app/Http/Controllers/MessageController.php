<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Auth;

class MessageController extends Controller
{
    public function index(Request $request, $userId = null)
    {
        $user = Auth::user();
        $contacts = User::where('id', '!=', $user->id)
            ->whereIn('role', ['teacher', 'admin', 'parent']);

        if ($request->filled('search')) {
            $contacts->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('role')) {
            $contacts->where('role', $request->role);
        }

        $contacts = $contacts->get();

        $selectedUser = $userId ? User::findOrFail($userId) : null;

        $messages = [];
        if ($selectedUser) {
            $messages = Message::where(function($q) use ($user, $selectedUser) {
                $q->where('sender_id', $user->id)->where('receiver_id', $selectedUser->id);
            })->orWhere(function($q) use ($user, $selectedUser) {
                $q->where('sender_id', $selectedUser->id)->where('receiver_id', $user->id);
            })->orderBy('created_at')->get();

            // Mark as read
            Message::where('sender_id', $selectedUser->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $unreadFrom = \App\Models\Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->selectRaw('sender_id, count(*) as unread_count')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');

        return view('messages.index', compact('contacts', 'selectedUser', 'messages', 'unreadFrom', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return back();
    }
} 