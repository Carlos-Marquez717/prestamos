@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Alert -->
        <div class="flex justify-center mt-4">
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4 mx-auto">
                    {{ session('success') }}
                </div>
            @endif
        </div>
        <!-- End of Alert -->

        <div class="flex justify-center">
            <div class="bg-black p-4 rounded-lg shadow-md w-full">
                <h1 class="text-2xl font-bold mb-4 text-center text-white"><span class="material-icons text-white">person_pin</span>CLIENTES</h1>
                
                <!-- Buscador -->
                <div class="mb-4 flex justify-end">
                    <form action="{{ route('clientes.index') }}" method="GET" class="w-full max-w-md">
                        <input type="text" name="search" placeholder="Buscar por nombre, dirección, teléfono, correo electrónico" class="px-4 py-2 rounded-md w-full bg-gray-800 text-white focus:outline-none focus:bg-gray-900" value="{{ request('search') }}">
                    </form>
                </div>

                <!-- Tabla -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-blue-900 text-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-600">Nombre</th>
                                <th class="py-2 px-4 border-b border-gray-600">Dirección</th>
                                <th class="py-2 px-4 border-b border-gray-600">Teléfono</th>
                                <th class="py-2 px-4 border-b border-gray-600">Correo</th>
                                <th class="py-2 px-4 border-b border-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="bg-black divide-y divide-gray-700">
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->nombre }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->direccion }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->telefono }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->email }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">
                                        <form action="{{ route('clientes.prestamos', $cliente) }}" method="get" class="inline">
                                            <button type="submit" class="px-2 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                                                <span class="material-icons">monetization_on</span>VER
                                            </button>
                                        </form>
                                    
                                        <form action="{{ route('clientes.edit', $cliente) }}" method="get" class="inline">
                                            <button type="submit" class="ml-1 px-2 py-1 rounded-md bg-yellow-600 text-white hover:bg-yellow-700">
                                                <span class="material-icons">edit</span>
                                            </button>
                                        </form>
                                    
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-1 px-2 py-1 rounded-md bg-red-600 text-white hover:bg-red-700" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente?')">
                                                <span class="material-icons">delete_forever</span>
                                            </button>
                                        </form>
                                    
                                        <!-- Botón para generar boleta por cliente -->
                                        <form action="{{ route('clientes.boleta', $cliente) }}" method="get" class="inline">
                                            <button type="submit" class="ml-1 px-2 py-1 rounded-md bg-green-600 text-white hover:bg-blue-700"><span class="material-icons">
                                                history
                                                </span></button>
                                        </form>
                                    
                                        <form action="{{ route('clientes.boleta.general') }}" method="get" class="inline">
                                            <button type="submit" style="background-color: #AE0646;" class="ml-1 px-2 py-1 rounded-md  text-white hover:bg-blue-700"><span class="material-icons">
                                                download
                                                </span></button>
                                        </form>
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Lista para dispositivos móviles -->
                <div class="block md:hidden">
                    @foreach ($clientes as $cliente)
                        <div class="bg-gray-800 text-white p-4 mb-4 rounded-lg shadow-md">
                            <p><strong>Nombre:</strong> {{ $cliente->nombre }}</p>
                            <p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>
                            <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
                            <p><strong>Correo:</strong> {{ $cliente->email }}</p>
                            <div class="flex flex-wrap mt-2">
                                <a href="{{ route('clientes.prestamos', $cliente) }}" class="px-2 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 mb-1">Prestamos</a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="ml-1 px-2 py-1 rounded-md bg-yellow-600 text-white hover:bg-yellow-700 mb-1">Editar</a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline mb-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-1 px-2 py-1 rounded-md bg-red-600 text-white hover:bg-red-700" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente?')">Eliminar</button>
                                </form>
                                <!-- Botón para generar boleta por cliente -->
                                <a href="{{ route('clientes.boleta', $cliente) }}" class="ml-1 px-2 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700 mb-1">Historial</a>
                                <a href="{{ route('clientes.boleta.general') }}" class="ml-1 px-2 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700 mb-1">General</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-4 flex justify-center">
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                $('#table-body tr').each(function() {
                    var found = false;
                    $(this).each(function() {
                        if ($(this).text().toLowerCase().indexOf(searchText) !== -1) {
                            found = true;
                        }
                    });
                    found ? $(this).show() : $(this).hide();
                });
            });
        });
    </script>
@endsection
