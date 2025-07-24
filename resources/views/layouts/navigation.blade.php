<nav x-data="{ open: false }" style="background:#fff !important;color:#232b33 !important;" class="border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
               
                <!-- Navigation Links -->
                <!-- Dashboard button removed -->
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if(Auth::check())
                    @php
                        $userRole = Auth::user()->role ?? '';
                        $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
                        $notifications = Auth::user()->unreadNotifications()->orderBy('created_at', 'desc')->take(5)->get();
                        $unreadNotifCount = Auth::user()->unreadNotifications()->count();
                    @endphp
                    <!-- Reminder Icon for Teachers -->
                    @if($userRole === 'teacher')
                        @php
                            $today = date('Y-m-d');
                            $pendingAttendance = \App\Models\Child::all()->filter(function($child) use ($today) {
                                return !\App\Models\Attendance::where('child_id', $child->id)->where('date', $today)->exists();
                            })->count();
                            $pendingProgress = \App\Models\Child::all()->filter(function($child) use ($today) {
                                return !\App\Models\Progress::where('child_id', $child->id)->where('date', $today)->exists();
                            })->count();
                            $hasPendingReminder = $pendingAttendance > 0 || $pendingProgress > 0;
                            $reminderUrl = $pendingAttendance > 0 ? route('attendance.index', ['date' => $today]) : route('progress.index', ['date' => $today]);
                        @endphp
                        @if($hasPendingReminder)
                            <a href="{{ $reminderUrl }}" class="relative mr-2" title="You have pending attendance/progress to complete. Click to view.">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.74-2.97l-7.07-12.25a2 2 0 00-3.48 0L3.19 16.03A2 2 0 004.93 19z" />
                                </svg>
                            </a>
                        @endif
                    @endif
                    <!-- Notification Bell Icon with Badge -->
                    <div x-data="notificationDropdown()" class="relative mr-3">
                        <button @click="openNotif = !openNotif; markRead()" class="relative inline-flex items-center justify-center px-2 py-2 rounded-full bg-white border border-gray-200 hover:bg-yellow-50 transition" title="Notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if($unreadNotifCount > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full" style="transform: translate(40%,-40%);">
                                    {{ $unreadNotifCount }}
                                </span>
                            @endif
                        </button>
                        <!-- Notification Dropdown -->
                        <div x-show="openNotif" @click.away="openNotif = false" class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50" style="display: none;">
                            <div class="p-4 border-b font-bold text-gray-700">Notifications</div>
                            <ul class="max-h-80 overflow-y-auto">
                                @forelse($notifications as $notif)
                                    <li class="px-4 py-3 border-b hover:bg-yellow-50 transition">
                                        <a href="{{ $notif->data['url'] ?? '#' }}" class="flex flex-col">
                                            <span class="font-semibold text-sm text-gray-800 capitalize">{{ $notif->data['type'] ?? '' }}</span>
                                            <span class="text-base text-gray-900 font-bold">{{ $notif->data['title'] ?? '' }}</span>
                                            <span class="text-xs text-gray-500">{{ $notif->created_at->diffForHumans() }}</span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-4 py-3 text-gray-500">No new notifications.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <!-- Standalone Chat Icon Button with Badge -->
                    <a href="{{ route('messages.index') }}" class="relative inline-flex items-center justify-center mr-3 px-2 py-2 rounded-full bg-white border border-gray-200 hover:bg-blue-50 transition" title="Messages">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8l-4 1 1-4A8.96 8.96 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full" style="transform: translate(40%,-40%);">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-[#232b33] hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->username }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Login</a>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Dashboard button removed -->
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    @if(Auth::check())
                        {{ Auth::user()->username }}
                    @else
                        Guest
                    @endif
                </div>
                <div class="font-medium text-sm text-gray-500">
                    @if(Auth::check())
                        {{ Auth::user()->email }}
                    @else
                        &nbsp;
                    @endif
                </div>
            </div>
            <div class="mt-3 space-y-1">
                @if(Auth::check())
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                @else
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                @endif
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationDropdown', () => ({
            openNotif: false,
            markRead() {
                if (this.openNotif) {
                    fetch("{{ route('notifications.markRead') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                    });
                }
            }
        }))
    })
</script>
