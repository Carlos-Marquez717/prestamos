@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Prestamos por Día</h1>
        <table class="table w-full divide-y divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Prestado</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @foreach($prestamos as $prestamo)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $prestamo->fecha->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamo->cantidad_prestamo, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-bold">Total</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-bold">{{ number_format($totalPrestado, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
