<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Notifications\NewEventOrActivityNotification;
use App\Models\User;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role;
            $action = $request->route()->getActionMethod();
            if ($role === 'teacher' && !in_array($action, ['index', 'show'])) {
                abort(403, 'Unauthorized');
            }
            if ($role !== 'admin' && in_array($action, ['create', 'store', 'edit', 'update', 'destroy'])) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::paginate(10); // For the table
        $allEvents = Event::all();     // For the calendar
        return view('events.index', compact('events', 'allEvents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120', // 5MB
        ]);
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('events', 'public');
        }
        $event = Event::create($validated);
        // Send notification to all users
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new NewEventOrActivityNotification(
                'event',
                $event->title,
                $event->description,
                route('events.index'), // Go to main list page
                $event->start_date
            ));
        }
        if ($request->expectsJson()) {
            return response()->json($event);
        }
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120', // 5MB
        ]);
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('events', 'public');
        }
        $event->update($validated);
        if ($request->expectsJson()) {
            return response()->json($event);
        }
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index');
    }
}
