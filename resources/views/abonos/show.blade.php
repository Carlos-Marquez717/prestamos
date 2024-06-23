@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="flex justify-center">
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4 text-center">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-black p-4 rounded-lg shadow-md">
            <h1 class="text-xl font-bold mb-4 text-center text-white">Detalles del Préstamo</h1>

            <div class="text-white">
                <p><strong>Cliente:</strong> {{ $prestamo->cliente->nombre }}</p>
                <p><strong>Cantidad del Préstamo:</strong> {{ $prestamo->cantidad_prestamo }}</p>
                <p><strong>Fecha del Préstamo:</strong> {{ \Carbon\Carbon::parse($prestamo->fecha)->format('d-m-Y') }}</p>
            </div>

            @if ($prestamo->abonos->isNotEmpty())
                <h3 class="text-lg font-bold mb-4 text-center text-white">Abonos</h3>
                <table class="table-auto w-full bg-white text-black rounded-md shadow-md">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2">Monto</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamo->abonos as $abono)
                            <tr>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($abono->fecha)->format('d-m-Y') }}</td>
                                <td class="border px-4 py-2">{{ $abono->monto }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('abonos.pdf', $abono->id) }}" class="text-indigo-600 hover:text-indigo-900">Descargar Boleta</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <p class="mt-4 text-white"><strong>Saldo Restante:</strong> {{ $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto') }}</p>

            <div class="flex justify-center mt-4">
                <a href="{{ route('abonos.create', $prestamo) }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Agregar Abono
                </a>
            </div>
        </div>
    </div>
@endsection
