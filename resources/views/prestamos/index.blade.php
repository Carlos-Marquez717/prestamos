@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <div class="bg-gray-800 text-white shadow-md rounded my-6">
            <div class="px-6 py-4">
                <h1 class="text-2xl font-bold mb-4 text-center">Listado de Préstamos</h1>

                @if (session('success'))
                    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-800 text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 bg-gray-800 text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 bg-gray-800 text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Cantidad Préstamo</th>
                            <th class="px-6 py-3 bg-gray-800 text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 bg-gray-800 text-white text-left text-xs leading-4 font-medium uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($prestamos as $prestamo)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap text-black">{{ $prestamo->id }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-black">{{ $prestamo->nombre_cliente }}</td>
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
            </div>
        </div>
    </div>
@endsection
