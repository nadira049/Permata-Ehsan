@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')
@section('content')
<style>
    .contact-card {
        background: linear-gradient(90deg, #f0f4ff 0%, #e0e7ff 100%);
        box-shadow: 0 2px 8px 0 rgba(60, 120, 240, 0.08);
        transition: background 0.3s, box-shadow 0.3s;
    }
    .contact-card:hover {
        background: linear-gradient(90deg, #e0e7ff 0%, #f0f4ff 100%);
        box-shadow: 0 4px 16px 0 rgba(60, 120, 240, 0.15);
    }
    .contact-active {
        background: linear-gradient(90deg, #c7d2fe 0%, #a5b4fc 100%);
        border-left: 6px solid #6366f1;
        color: #3730a3;
        font-weight: bold;
    }
    .chat-card {
        background: linear-gradient(120deg, #fdf6e3 0%, #f0fdfa 100%);
        border-radius: 1.5rem;
        box-shadow: 0 4px 24px 0 rgba(0,0,0,0.08);
        padding: 2rem;
    }
    .chat-bubble {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 1.25rem;
        margin-bottom: 0.25rem;
        max-width: 70%;
        word-break: break-word;
    }
    .chat-bubble.me {
        background: linear-gradient(90deg, #a7f3d0 0%, #6ee7b7 100%);
        color: #065f46;
        align-self: flex-end;
    }
    .chat-bubble.them {
        background: linear-gradient(90deg, #fca5a5 0%, #f87171 100%);
        color: #7f1d1d;
        align-self: flex-start;
    }
</style>
<div class="flex gap-8">
    <!-- Contacts List -->
    <div class="w-1/3 p-4 rounded-2xl bg-gradient-to-b from-blue-50 to-indigo-100 shadow-lg">
        <h2 class="font-bold mb-4 text-lg text-indigo-700">Contacts</h2>
        <form method="GET" class="flex flex-wrap items-center gap-2 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..." class="border rounded px-3 py-2 w-48" />
            <select name="role" class="border rounded px-3 py-2">
                <option value="">All Roles</option>
                <option value="parent" {{ request('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded font-semibold shadow hover:bg-blue-700 transition">Search</button>
            <a href="{{ route('messages.index') }}" class="px-5 py-2 bg-gray-200 text-gray-800 rounded font-semibold shadow hover:bg-gray-300 transition">Reset</a>
        </form>
        <ul>
            @foreach($contacts as $contact)
                @php
                    $isActive = $selectedUser && $selectedUser->id == $contact->id;
                    $lastMsg = \App\Models\Message::where(function($q) use ($contact, $user) {
                        $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
                    })->orWhere(function($q) use ($contact, $user) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
                    })->latest()->first();
                @endphp
                <li class="mb-3">
                    <a href="{{ route('messages.index', $contact->id) }}"
                       class="contact-card flex items-center gap-3 p-3 rounded-xl transition relative {{ $isActive ? 'contact-active' : '' }}">
                        <img src="{{ $contact->profile_picture ? asset('storage/' . $contact->profile_picture) : asset('images/default-profile.png') }}"
                             class="w-12 h-12 rounded-full object-cover border-2 border-indigo-200 shadow-sm" alt="avatar">
                        <div class="flex-1 min-w-0">
                            <div class="truncate text-base">{{ $contact->full_name }} <span class="text-xs text-gray-400">({{ ucfirst($contact->role) }})</span></div>
                            <div class="text-xs text-gray-500 truncate">
                                @if($lastMsg)
                                    {{ $lastMsg->sender_id == $user->id ? 'You: ' : '' }}{{ Str::limit($lastMsg->message, 30) }}
                                @else
                                    <span class="italic text-gray-300">No messages yet</span>
                                @endif
                            </div>
                        </div>
                        @if(isset($unreadFrom[$contact->id]) && $unreadFrom[$contact->id] > 0)
                            <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-pink-500 rounded-full shadow animate-bounce">
                                {{ $unreadFrom[$contact->id] }}
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <!-- Chat Area -->
    <div class="w-2/3">
        <div class="chat-card min-h-[400px] flex flex-col">
            @if($selectedUser)
                <h2 class="font-bold mb-4 text-xl text-indigo-800">Chat with {{ $selectedUser->full_name }}</h2>
                <div class="flex-1 flex flex-col gap-2 mb-4 overflow-y-auto" style="max-height:300px;">
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="chat-bubble {{ $msg->sender_id == Auth::id() ? 'me' : 'them' }}">
                                {{ $msg->message }}
                                <div class="text-xs text-gray-500 mt-1 text-right" style="font-size: 0.75rem;">
                                    {{ $msg->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('messages.store') }}" class="flex gap-2 mt-2">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                    <input type="text" name="message" class="flex-1 border-2 border-indigo-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="Type a message..." required>
                    <button class="px-6 py-2 bg-gradient-to-r from-indigo-400 to-blue-400 text-white font-bold rounded-lg shadow hover:from-blue-400 hover:to-indigo-400 transition">Send</button>
                </form>
            @else
                <div class="flex flex-col items-center justify-center h-full text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8l-4 1 1-4A8.96 8.96 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div class="text-2xl font-bold">Select a contact to start chatting.</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 