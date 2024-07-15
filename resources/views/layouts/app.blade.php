<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">


        @yield('css')

        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon"/>


        <style>
            html, body {
                height: 100%;
                margin: 0;
            }
    
            body {
                display: flex;
                flex-direction: column;
            }
    
            main {
                flex: 1; /* Permite que el main tome el espacio restante */
                display: flex;
                flex-direction: column;
            }
    
            footer {
                background-color: black; /* Color del footer */
                color: white;
                padding: 1rem;
                text-align: center;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900" style="background-image: url('{{ asset('images/criptomonedas.png') }}'); background-size: cover;">
        <div class="min-h-screen dark:bg-gray-900 flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
            <footer>
                <p>&copy; {{ date('Y') }} PRESSTAPP. Todos los derechos reservados.</p>
                <div>
                    <a href="#" class="text-gray-400 hover:text-gray-300">Política de Privacidad</a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">Términos de Servicio</a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">Contacto</a>
                </div>
            </footer>
            
        </div>
        
    </body>
    


    @yield('js')
    </body>

</html>
