<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="bg-black text-white py-8 px-4 sm:px-6 lg:px-8">
        <a href="/">
            <img src="{{ asset('images/Banco.png') }}" alt="Logo de la empresa">
            <h1 style="text-align: center">BIENVENIDO</h1>
        </a>

        <form method="POST" action="{{ route('login') }}" class="mt-8">
            @csrf

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"  class="block mt-1 w-full border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md text-black" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md text-black" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('RECORDAR') }}</span>
                </label>
            </div>

            <!-- Forgot Password Link -->
            <div class="block mt-4">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">{{ __('OLVIDASTE TU PASSWORD?') }}</a>
                @endif
            </div>

            <!-- Login Button -->
            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="bg-gray-800 hover:bg-gray-900">
                    {{ __('ENTRAR') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
