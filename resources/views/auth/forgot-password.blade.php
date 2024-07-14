@extends('layouts.login-layout')

@section('content')
    <!-- Contenido actual de la página de login -->
    <div class="bg-black text-white py-8 px-4 sm:px-6 lg:px-8">
        <a href="/" class="text-center block mb-4">
            <img src="{{ asset('images/Banco.png') }}" alt="Logo de la empresa" class="mx-auto h-15 w-auto">
            <h1 class="text-xl font-bold text-white">BIENVENIDO</h1>
        </a>

        <div class="mb-4 text-sm text-white bg-black p-4 rounded">
            {{ __('¿Olvidaste tu contraseña? Ningún problema. Simplemente háganos saber su dirección de correo electrónico y le enviaremos un enlace para restablecer su contraseña que le permitirá elegir una nueva.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="bg-black p-4 rounded text-white">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-white" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-800 text-white border-gray-700" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-white" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="bg-gray-800 hover:bg-gray-700 text-white">
                    {{ __('Enviar enlace para restablecer contraseña') }}
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection
