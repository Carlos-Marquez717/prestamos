@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-center">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold mb-4 text-black">CLIENTES</h1>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-black text-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-200">Nombre</th>
                                <th class="py-2 px-4 border-b border-gray-200">Dirección</th>
                                <th class="py-2 px-4 border-b border-gray-200">Teléfono</th>
                                <th class="py-2 px-4 border-b border-gray-200">Correo Electrónico</th>
                                <th class="py-2 px-4 border-b border-gray-200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $cliente->nombre }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $cliente->direccion }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $cliente->telefono }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $cliente->email }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <a href="{{ route('clientes.show', $cliente) }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Ver Prestamos</a>
                                        <a href="{{ route('clientes.edit', $cliente) }}" class="ml-2 px-4 py-2 rounded-md bg-yellow-600 text-white hover:bg-yellow-700">Editar</a>
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-2 px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

