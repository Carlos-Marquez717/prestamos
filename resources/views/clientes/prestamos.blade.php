@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-black rounded-lg shadow-md px-6 py-4">
            <h1 class="text-2xl font-bold mb-4 text-center text-white">Préstamos de {{ $cliente->nombre }}</h1>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-black text-white">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="py-2 px-4 border-b border-gray-600 text-center">ID</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Cantidad</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach ($prestamos as $prestamo)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">{{ $prestamo->id }}</td>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">{{ $prestamo->cantidad_prestamo }}</td>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">{{ $prestamo->fecha }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-center">
                <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Volver</a>
            </div>
        </div>
    </div>
@endsection
