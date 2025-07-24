<x-guest-layout>
    @if ($errors->has('role') && request()->routeIs('login'))
        <div id="role-error-modal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-auto text-center flex flex-col items-center">
                <div class="text-red-600 font-bold text-lg mb-4">Error</div>
                <div class="text-gray-800 mb-6">{{ $errors->first('role') }}</div>
                <button onclick="window.location.href='{{ route('login') }}'" class="px-8 py-3 bg-indigo-600 text-black font-bold text-lg rounded shadow-lg hover:bg-indigo-700 focus:outline-none transition-all duration-150 mt-2">OK</button>
            </div>
        </div>
    @endif
    @if ($errors->has('email') && request()->routeIs('login'))
        <div id="email-error-modal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-auto text-center flex flex-col items-center">
                <div class="text-red-600 font-bold text-lg mb-4">Error</div>
                <div class="text-gray-800 mb-6">{{ $errors->first('email') }}</div>
                <button onclick="window.location.href='{{ route('login') }}'" class="px-8 py-3 bg-indigo-600 text-black font-bold text-lg rounded shadow-lg hover:bg-indigo-700 focus:outline-none transition-all duration-150 mt-2">OK</button>
            </div>
        </div>
    @endif
    {{-- Username Field --}}
    @if ($errors->has('username') && request()->routeIs('login'))
        <div id="username-error-modal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-auto text-center flex flex-col items-center">
                <div class="text-red-600 font-bold text-lg mb-4">Error</div>
                <div class="text-gray-800 mb-6">{{ $errors->first('username') }}</div>
                <button onclick="window.location.href='{{ route('login') }}'" class="px-8 py-3 bg-indigo-600 text-black font-bold text-lg rounded shadow-lg hover:bg-indigo-700 focus:outline-none transition-all duration-150 mt-2">OK</button>
            </div>
        </div>
    @endif

    <!-- Logo -->
    <div class="flex flex-col items-center mb-6">
        <x-application-logo class="w-20 h-20 mx-auto" />
    </div>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Role Selection -->
        <div class="mb-6 text-center">
            <label class="block text-white font-semibold text-sm mb-6">Login as:</label>
            <div class="flex justify-center gap-6 text-white mt-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="admin" required class="form-radio text-indigo-600">
                    <span class="ml-2">Admin</span>
                </label>
                <span class="ml-2"</spa>
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="teacher" required class="form-radio text-indigo-600">
                    <span class="ml-2">Teacher</span>
                </label>
                <span class="ml-2"</spa>
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="parent" required class="form-radio text-indigo-600">
                    <span class="ml-2">Parent</span>
                </label>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Username -->
        <x-input-label for="username" :value="__('Username')" class="text-white" />
        <x-text-input id="username" class="block mt-1 w-full" type="text" name="username"
            :value="old('username')" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('username')" class="mt-2" />

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-white" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                          name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span class="ml-2 text-sm text-white">Remember me</span>
            </label>
        </div>

        <!-- Submit + Forgot Password -->
        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-white hover:text-gray-200"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ml-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
