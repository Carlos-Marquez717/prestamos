@extends('layouts.app')


@section ('css')
<link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">

@endsection
@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-black rounded-lg shadow-md px-6 py-4">
            <!-- Mostrar el nombre del cliente -->
            <h1 class="text-2xl font-bold mb-4 text-center text-white">{{ $cliente->nombre }}</h1>

            <!-- Formulario de búsqueda -->
            <form action="{{ route('clientes.prestamos', $cliente) }}" method="GET" class="flex flex-wrap items-center justify-between mb-4">
                <div class="w-full md:w-auto mb-2 md:mb-0">
                    <label for="fecha" class="block text-white">Fecha:</label>
                    <input type="date" name="fecha" id="fecha" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                </div>
                <div class="w-full md:w-auto mb-2 md:mb-0">
                    <label for="cantidad" class="block text-white">Cantidad:</label>
                    <input type="number" name="cantidad" id="cantidad" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                </div>
                <div class="w-full md:w-auto mb-2 md:mb-0">
                    <label for="estado" class="block text-white">Estado:</label>
                    <select name="estado" id="estado" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                        <option value="">Todos</option>
                        <option value="pagado">Pagado</option>
                        <option value="proceso">En proceso</option>
                    </select>
                </div>
                <div class="w-full md:w-auto mb-2 md:mb-0">
                    <label for="restante" class="block text-white">Restante:</label>
                    <input type="number" name="restante" id="restante" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                </div>
                <div class="w-full md:w-auto">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Buscar</button>
                </div>
            </form>

            <!-- Botón Historial -->


            <div class="overflow-x-auto">
                <table  id="example" class="min-w-full bg-black text-white">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="py-2 px-4 border-b border-gray-600 text-center">ID</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Cantidad</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Fecha Prestamo</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Fechas de Abono</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Abonos</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Restante</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Estado</th>
                            <th class="py-2 px-4 border-b border-gray-600 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach ($prestamos as $prestamo)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">{{ $prestamo->id }}</td>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">{{ $prestamo->cantidad_prestamo }}</td>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">{{ \Carbon\Carbon::parse($prestamo->fecha)->format('d/m/y') }}</td>
                                <td class="py-2 px-4 border-b border-gray-600">
                                    <ul>
                                        @foreach ($prestamo->abonos as $abono)
                                            <li>{{ \Carbon\Carbon::parse($abono->fecha)->format('d/m/y') }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-2 px-4 border-b border-gray-600">
                                    <ul>
                                        @foreach ($prestamo->abonos as $abono)
                                            <li>{{ $abono->monto }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">
                                    {{ $prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto') }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">
                                    @if (($prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto')) == 0)
                                        <span class="text-green-500 font-bold">Pagado</span>
                                    @else
                                        <span class="text-yellow-500 font-bold">En proceso</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b border-gray-600 text-center">
                                    @if (($prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto')) > 0)
                                        <a href="{{ route('abonos.create', $prestamo) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Registrar Abono</a>
                                    @else
                                        <button disabled class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed">Registrar Abono</button>
                                    @endif
                                    <a href="{{ route('prestamos.pdf', $prestamo) }}" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Descargar Boleta</a>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-center">
                {{ $prestamos->appends(request()->except('page'))->links() }}
            </div>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Volver</a>
            </div>
        </div>
    </div>
@endsection


