<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar -->
        <aside class="w-64 shadow-md p-4 flex flex-col text-white" style="background:#232b33 !important;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 mb-1 mx-auto">
            <h2 class="text-lg font-bold mb-4 text-center">Learning Journey</h2>
            <hr class="border-gray-400 mb-2">
            @php $role = Auth::user()->role ?? ''; @endphp
            <nav class="flex flex-col gap-2 mt-2" style="background:#232b33 !important;">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33 !important;color:#fff !important;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 6h18M3 18h18" /></svg>
                    Dashboard
                </a>
                <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                @if ($role === 'admin')
                    <a href="{{ route('events.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Manage Event
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('activities.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        Manage Activities
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('learning-materials.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Upload Learning Material
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('attendance.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2" /></svg>
                        View Attendance
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('progress.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2" /></svg>
                        View Child Progress
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('users.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196zM15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Manage Users
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                @elseif ($role === 'teacher')
                    <a href="{{ route('events.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        View Event
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('activities.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        Manage Activities
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('child.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2" /></svg>
                        Child
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('progress.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Progress
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('learning-materials.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Upload Learning Material
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('attendance.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2" /></svg>
                        Record Attendance
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                @elseif ($role === 'parent')
                    <a href="{{ route('events.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        View Event
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('activities.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        View Activities
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('progress.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2" /></svg>
                        View Child Progress
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('learning-materials.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M12 4h9m-9 0a2 2 0 00-2 2v12a2 2 0 002 2m0-16a2 2 0 012 2v12a2 2 0 01-2 2m0-16H6a2 2 0 00-2 2v12a2 2 0 002 2h6" /></svg>
                        View Learning Material
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                    <a href="{{ route('attendance.index') }}" class="flex items-center gap-4 rounded px-2 py-1 my-2" style="background:#232b33;color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2" /></svg>
                        View Attendance
                    </a>
                    <hr class="border-gray-300 my-1" style="border-color:#37404a;">
                @endif
            </nav>
        </aside>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="shadow" style="background:#232b33;color:#fff;">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1 p-6 bg-white dark:bg-white">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
