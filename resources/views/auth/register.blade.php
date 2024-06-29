<x-guest-layout>
    <div class="bg-black text-white py-8 px-4 sm:px-6 lg:px-8">
        <a href="/">
            <img src="{{ asset('images/Banco.png') }}" alt="Logo de la empresa">
            <h1 style="text-align: center">REGISTRO DE USUARIOS</h1>
        </a>
    
        <form method="POST" action="{{ route('register') }}" class="bg-black text-white p-6 rounded-lg shadow-lg">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('NOMBRE')" class="text-white"/>
                <x-text-input id="name" class="block mt-1 w-full bg-gray-800 text-white border-gray-700" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('EMAIL')" class="text-white"/>
                <x-text-input id="email" class="block mt-1 w-full bg-gray-800 text-white border-gray-700" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('PASSWORD')" class="text-white"/>

                <x-text-input id="password" class="block mt-1 w-full bg-gray-800 text-white border-gray-700"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('CONFIRMAR PASSWORD')" class="text-white"/>

                <x-text-input id="password_confirmation" class="block mt-1 w-full bg-gray-800 text-white border-gray-700"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-400 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('¿Ya registrado?') }}
                </a>

                <x-primary-button class="ms-4 bg-gray-700 hover:bg-gray-600 text-white">
                    {{ __('Registrar') }}
                </x-primary-button>
            </div>
        </form>
    </div>    

</x-guest-layout>
