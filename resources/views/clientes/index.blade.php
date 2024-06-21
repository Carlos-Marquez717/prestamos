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
                <h1 class="text-2xl font-bold mb-4 text-center text-white">CLIENTES</h1>
                
                <!-- Buscador -->
                <div class="mb-4 flex justify-end">
                    <input type="text" id="search" placeholder="Buscar por nombre, dirección, teléfono, correo electrónico" class="px-4 py-2 rounded-md w-full max-w-md bg-gray-800 text-white focus:outline-none focus:bg-gray-900">
                </div>
                
                <div class="overflow-x-auto">
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
                        <tbody id="table-body" class="bg-gray-800 divide-y divide-gray-700">
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->nombre }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->direccion }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->telefono }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">{{ $cliente->email }}</td>
                                    <td class="py-2 px-4 border-b border-gray-600">
                                        <a href="{{ route('clientes.prestamos', $cliente) }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Prestamos</a>
                                        <a href="{{ route('clientes.edit', $cliente) }}" class="ml-2 px-4 py-2 rounded-md bg-yellow-600 text-white hover:bg-yellow-700">Editar</a>
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-2 px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente?')">Eliminar</button>
                                        </form>
                                        <!-- Botón para generar boleta por cliente -->
                                        <a href="{{ route('clientes.boleta', $cliente) }}" class="ml-2 px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">HISTORIAL</a>
                                        <a href="{{ route('clientes.boleta.general') }}" class="ml-2 px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">GENERAL</a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
