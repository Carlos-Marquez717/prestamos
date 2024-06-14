@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <div class="bg-black text-white shadow-md rounded my-6">
            <div class="px-6 py-4">
                <h1 class="text-2xl font-bold mb-4 text-center">Listado de Préstamos</h1>

                @if (session('success'))
                    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Formulario de búsqueda -->
                <form action="{{ route('prestamos.buscar') }}" method="GET" class="mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-2">
                            <label for="cliente" class="block text-white">Cliente:</label>
                            <input type="text" id="cliente" name="cliente" class="w-full bg-gray-800 text-white border border-gray-600 rounded py-2 px-4 focus:outline-none focus:bg-white focus:text-gray-900" placeholder="Nombre del cliente">
                        </div>
                        <div class="flex-1 mr-2">
                            <label for="cantidad" class="block text-white">Cantidad:</label>
                            <input type="text" id="cantidad" name="cantidad" class="w-full bg-gray-800 text-white border border-gray-600 rounded py-2 px-4 focus:outline-none focus:bg-white focus:text-gray-900" placeholder="Cantidad del préstamo">
                        </div>
                        <div class="flex-1">
                            <label for="fecha" class="block text-white">Fecha:</label>
                            <input type="date" id="fecha" name="fecha" class="w-full bg-gray-800 text-white border border-gray-600 rounded py-2 px-4 focus:outline-none focus:bg-white focus:text-gray-900">
                        </div>
                        <button type="submit" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Buscar</button>
                    </div>
                </form>

                <!-- Tabla de préstamos -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-black text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 bg-black text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 bg-black text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Cantidad Préstamo</th>
                            <th class="px-6 py-3 bg-black text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 bg-black text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($prestamos as $prestamo)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap text-black">{{ $prestamo->id }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-black">{{ $prestamo->cliente->nombre }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-black">{{ $prestamo->cantidad_prestamo }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-black">{{ $prestamo->fecha }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="flex">
                                        <a href="{{ route('prestamos.edit', $prestamo->id) }}" class="ml-2 px-4 py-2 rounded-md bg-yellow-600 text-white hover:bg-yellow-700">Editar</a>
                                        <form action="{{ route('prestamos.destroy', $prestamo->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-2 px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Paginación -->
                <div class="mt-4 flex justify-center">
                    {{ $prestamos->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
