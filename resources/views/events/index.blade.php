@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-extrabold mb-8 text-gray-800">Events</h1>
    @php
        $calendarEvents = [];
        foreach ($allEvents as $e) {
            $calendarEvents[] = [
                'id' => $e->id,
                'title' => $e->title,
                'start' => $e->start_date ?? $e->date,
                'end' => $e->end_date ?? $e->date,
            ];
        }
    @endphp
    @php $role = Auth::user()->role ?? ''; @endphp
    <div x-data="{ showCreate: false, showEdit: false, showDelete: false, editEvent: null, deleteEventId: null }">
        <div class="flex flex-col gap-8 items-center w-full px-2 md:px-8 mt-6">
            <!-- Calendar Card -->
            <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border border-gray-100">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Event Calendar</h2>
                <div id="calendar" class="w-full"></div>
            </div>
            <!-- Event List Card -->
            <div class="w-full max-w-5xl flex flex-col gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 w-full border border-gray-100">
                    <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
                        <h2 class="text-xl font-bold text-gray-700">Event List</h2>
                        @if($role === 'admin')
                            <button @click="showCreate = true" class="px-6 py-2" style="background-color:#2c3e50; color:white; font-weight:600; border-radius:0.5rem; box-shadow:0 1px 4px #0001; transition:background 0.2s;" onmouseover="this.style.backgroundColor='#34495e'" onmouseout="this.style.backgroundColor='#2c3e50'">+ Add Event</button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">No.</th>
                                    <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">Title</th>
                                    <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">Date</th>
                                    <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">Location</th>
                                    <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">Description</th>
                                    <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">Attachment</th>
                                    @if($role === 'admin')
                                        <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $index => $event)
                                    <tr class="hover:bg-blue-50 even:bg-gray-50 transition border-b border-gray-200">
                                        <td class="px-4 py-2 border-r border-gray-200 text-center">{{ ($events->currentPage() - 1) * $events->perPage() + $index + 1 }}</td>
                                        <td class="px-4 py-2 border-r border-gray-200 font-semibold max-w-[160px] truncate">{{ $event->title }}</td>
                                        <td class="px-4 py-2 border-r border-gray-200 whitespace-nowrap">
                                            {{ $event->start_date }}
                                            @if($event->end_date && $event->end_date !== $event->start_date)
                                                <br><span class="text-xs text-gray-500">to {{ $event->end_date }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border-r border-gray-200 max-w-[120px] truncate">{{ $event->location }}</td>
                                        <td class="px-4 py-2 border-r border-gray-200 text-xs text-gray-700 max-w-[180px] truncate">{{ $event->description }}</td>
                                        <td class="px-4 py-2 border-r border-gray-200">
                                            @if($event->attachment)
                                                <a href="{{ asset('storage/' . $event->attachment) }}" target="_blank" class="text-blue-600 underline">Download</a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        @if($role === 'admin')
                                            <td class="px-4 py-2">
                                                <div class="flex gap-2">
                                                    <button @click="showEdit = true; editEvent = {id: {{ $event->id }}, title: '{{ addslashes($event->title) }}', start_date: '{{ $event->start_date }}', end_date: '{{ $event->end_date }}', location: '{{ addslashes($event->location) }}', description: `{{ addslashes($event->description) }}`}" class="flex items-center gap-1 text-yellow-900 bg-yellow-200 hover:bg-yellow-300 px-3 py-1 rounded shadow-sm border border-yellow-300 font-semibold transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.828 1.172H7v-2a4 4 0 011.172-2.828z" /></svg>
                                                        Edit
                                                    </button>
                                                    <button @click="showDelete = true; deleteEventId = {{ $event->id }}" type="button" class="flex items-center gap-1 text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded shadow-sm border border-red-700 font-semibold transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="text-gray-400 text-sm">
                            Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} results
                        </div>
                        <div>
                            {{ $events->links('vendor.pagination.custom-dark') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modals: Add/Edit/Delete -->
        @if($role === 'admin')
        <!-- Add Event Modal -->
        <template x-if="showCreate">
            <div class="fixed inset-0 bg-black bg-opacity-30 z-50 flex items-center justify-center" x-cloak>
                <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md flex flex-col items-center border border-gray-100">
                    <h2 class="text-lg font-bold mb-4 text-gray-700">Add Event</h2>
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="w-full">
                        @csrf
                        <label class="block mb-1 font-medium">Date Start</label>
                        <input type="date" id="start_date" name="start_date" class="w-full mb-3 border rounded px-2 py-1" required>
                        <label class="block mb-1 font-medium">Date End</label>
                        <input type="date" id="end_date" name="end_date" class="w-full mb-3 border rounded px-2 py-1" required>
                        <label class="block mb-1 font-medium">Title</label>
                        <input type="text" id="title" name="title" class="w-full mb-3 border rounded px-2 py-1" required>
                        <label class="block mb-1 font-medium">Location</label>
                        <input type="text" id="location" name="location" class="w-full mb-3 border rounded px-2 py-1" required>
                        <label class="block mb-1 font-medium">Description</label>
                        <textarea id="description" name="description" class="w-full mb-3 border rounded px-2 py-1" required></textarea>
                        <label class="block mb-1 font-medium">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="w-full mb-4 border rounded px-2 py-1" required>
                        <div class="flex gap-2 mt-2">
                            <button type="submit" class="w-full" style="background-color:#2c3e50; color:white; font-weight:600; border-radius:0.375rem; padding:0.5rem 0; transition:background 0.2s;" onmouseover="this.style.backgroundColor='#34495e'" onmouseout="this.style.backgroundColor='#2c3e50'">Save</button>
                            <button type="button" @click="showCreate = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 rounded">Back</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        <!-- Edit Event Modal -->
        <template x-if="showEdit && editEvent">
            <div class="fixed inset-0 bg-black bg-opacity-30 z-50 flex items-center justify-center" x-cloak>
                <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md flex flex-col items-center border border-gray-100">
                    <h2 class="text-lg font-bold mb-4 text-gray-700">Edit Event</h2>
                    <form :action="'/events/' + editEvent.id" method="POST" enctype="multipart/form-data" class="w-full">
                        @csrf
                        @method('PUT')
                        <label class="block mb-1 font-medium">Date Start</label>
                        <input type="date" name="start_date" x-model="editEvent.start_date" class="w-full mb-3 border rounded px-2 py-1" required>
                        <label class="block mb-1 font-medium">Date End</label>
                        <input type="date" name="end_date" x-model="editEvent.end_date" class="w-full mb-3 border rounded px-2 py-1">
                        <label class="block mb-1 font-medium">Title</label>
                        <input type="text" name="title" x-model="editEvent.title" class="w-full mb-3 border rounded px-2 py-1" required>
                        <label class="block mb-1 font-medium">Location</label>
                        <input type="text" name="location" x-model="editEvent.location" class="w-full mb-3 border rounded px-2 py-1">
                        <label class="block mb-1 font-medium">Description</label>
                        <textarea name="description" x-model="editEvent.description" class="w-full mb-3 border rounded px-2 py-1"></textarea>
                        <label class="block mb-1 font-medium">Attachment</label>
                        <input type="file" name="attachment" class="w-full mb-4 border rounded px-2 py-1">
                        <div class="flex gap-2 mt-2">
                            <button type="submit" class="w-full" style="background-color:#2c3e50; color:white; font-weight:600; border-radius:0.375rem; padding:0.5rem 0; transition:background 0.2s;" onmouseover="this.style.backgroundColor='#34495e'" onmouseout="this.style.backgroundColor='#2c3e50'">Update</button>
                            <button type="button" @click="showEdit = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 rounded">Back</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        <!-- Delete Event Modal -->
        <template x-if="showDelete && deleteEventId">
            <div class="fixed inset-0 bg-black bg-opacity-30 z-50 flex items-center justify-center" x-cloak>
                <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-xs flex flex-col items-center border border-gray-100">
                    <h2 class="text-lg font-bold mb-4 text-gray-700">Delete Event</h2>
                    <p class="mb-4 text-center">Are you sure you want to delete this event?</p>
                    <form :action="'/events/' + deleteEventId" method="POST" class="w-full flex flex-col gap-2 items-center">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-bold flex items-center gap-1 mt-2">Yes, Delete</button>
                        <button type="button" @click="showDelete = false" class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded font-semibold">Cancel</button>
                    </form>
                </div>
            </div>
        </template>
        @endif
    </div>

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: @json($calendarEvents),
                dateClick: function(info) {
                    document.getElementById('start_date').value = info.dateStr;
                    document.getElementById('end_date').value = info.dateStr;
                    document.getElementById('title').focus();
                },
            });
            calendar.render();
        });
    </script>
@endsection 