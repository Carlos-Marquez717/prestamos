@extends('layouts.app')

@section('css')
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">
    <style>
        /* Estilos adicionales para DataTables responsivo */
        @media screen and (max-width: 640px) {
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                font-size: 0.8rem;
            }
        }

        /* Estilo para los botones de DataTables */
        .dt-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .dt-button {
            padding: 8px 12px;
            font-size: 0.875rem;
            line-height: 1.25rem;
            border-radius: 0.375rem;
            background-color: #4b5563;
            color: white;
            border: 1px solid transparent;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        .dt-button:hover {
            background-color: #1e40af;
            border-color: #1e40af;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-black rounded-lg shadow-md px-6 py-4">
            <!-- Mostrar el nombre del cliente -->
            <h1 class="text-2xl font-bold mb-4 text-center text-white">{{ $cliente->nombre }}</h1>

            <!-- Formulario de búsqueda -->
            <form action="{{ route('clientes.prestamos', $cliente) }}" method="GET" class="flex flex-wrap items-center justify-between mb-4">
                <div class="w-full sm:w-48 mb-2 sm:mb-0">
                    <label for="fecha" class="block text-white">Fecha:</label>
                    <input type="date" name="fecha" id="fecha" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                </div>
                <div class="w-full sm:w-48 mb-2 sm:mb-0">
                    <label for="cantidad" class="block text-white">Cantidad:</label>
                    <input type="number" name="cantidad" id="cantidad" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                </div>
                <div class="w-full sm:w-48 mb-2 sm:mb-0">
                    <label for="estado" class="block text-white">Estado:</label>
                    <select name="estado" id="estado" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                        <option value="">Todos</option>
                        <option value="pagado">Pagado</option>
                        <option value="proceso">En proceso</option>
                    </select>
                </div>
                <div class="w-full sm:w-48 mb-2 sm:mb-0">
                    <label for="restante" class="block text-white">Restante:</label>
                    <input type="number" name="restante" id="restante" class="px-4 py-2 w-full bg-gray-800 text-white border border-gray-600 rounded-md">
                </div>
                <div class="w-full sm:w-auto">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Buscar</button>
                </div>
            </form>

            <!-- Tabla de préstamos -->
            <div class="overflow-x-auto">
                <table id="example" class="min-w-full bg-black text-white">
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
                                        <a href="{{ route('abonos.create', $prestamo) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"><span class="material-icons">
                                            paid
                                            </span></a>
                                    @else
                                        <button disabled class="px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed"><span class="material-icons">
                                            paid
                                            </span></button>
                                    @endif
                                    <a href="{{ route('prestamos.pdf', $prestamo) }}" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><span class="material-icons">
                                        request_quote
                                        </span></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4 flex justify-center">
                {{ $prestamos->appends(request()->except('page'))->links() }}
            </div>

            <!-- Botón Volver -->
            <div class="mt-4 flex justify-center">
                <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Volver</a>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 },
                ],
                order: [],
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        className: 'dt-button bg-green-600 hover:bg-green-700',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Exportar a PDF',
                        className: 'dt-button bg-blue-600 hover:bg-blue-700',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                ]
            });
        });
    </script>
@endsection
