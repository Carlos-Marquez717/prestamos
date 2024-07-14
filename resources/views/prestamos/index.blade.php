@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">

    <style>
        /* Estilo personalizado para los botones de DataTables con Bootstrap */
        .dt-button {
            border-radius: 4px;
            margin-right: 10px; /* Espacio entre los botones */
        }
        .dt-button.yellow {
            background-color: #FBB829; /* Amarillo */
            color: black; /* Texto negro para contraste */
        }
        .dt-button.blue {
            background-color: #0D6EFD; /* Azul */
            color: white;
        }
        .dt-button.green {
            background-color: #198754; /* Verde */
            color: white;
        }
        .dt-button.red {
            background-color: #DC3545; /* Rojo */
            color: white;
        }
        table.dataTable thead th {
            background-color: rgb(32, 15, 109); /* Fondo negro */
            color: white; /* Texto blanco */
        }
        table.dataTable tbody td {
            background-color: rgb(26, 26, 27); /* Fondo negro */
            color: white; /* Texto blanco */
        }

        /* Estilo para el card negro */
        .custom-card {
            background-color: #1a1a1b; /* Negro */
            color: white; /* Texto blanco */
            border-radius: 0.5rem; /* Bordes redondeados */
            margin-top: 20px; /* Espacio superior */
            padding: 20px; /* Espaciado interno */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); /* Sombra */
        }

        /* Ajustes para la visualización del buscador */
        .dataTables_wrapper .dataTables_filter {
            display: block !important;
            float: none;
            text-align: center;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin: 0 auto;
            width: 100%;
            max-width: 300px; /* Ajustar el ancho máximo según sea necesario */
            padding: 8px;
            box-sizing: border-box;
        }

        /* Responsivo para tablas */
        @media (max-width: 768px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .table thead {
                display: none;
            }
            .table tr {
                display: block;
                margin-bottom: 10px;
            }
            .table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px;
                border-top: 1px solid #dee2e6;
            }
            .table td::before {
                content: attr(data-label);
                flex-basis: 50%;
                text-align: left;
                font-weight: bold;
                padding-right: 10px;
            }
        }
    </style>
@endsection

@section('content')
<br><br>
    <div class="container">
        <div class="custom-card">
            <h1 class="text-2xl font-bold mb-4 text-center">Listado de Préstamos</h1>

            <!-- Tabla de préstamos -->
            <div class="table-responsive">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Cantidad Préstamo</th>
                            <th>Fecha y Hora</th>
                            <th>Restante</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($prestamos as $prestamo)
                            <tr>
                                <td data-label="ID">{{ $prestamo->id }}</td>
                                <td data-label="Cliente">{{ $prestamo->cliente->nombre }}</td>
                                <td data-label="Cantidad Préstamo">{{ $prestamo->cantidad_prestamo }}</td>
                                <td data-label="Fecha y Hora">{{ \Carbon\Carbon::parse($prestamo->fecha)->format('d/m/Y H:i') }}</td>
                                <td data-label="Restante">{{ $prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto') }}</td>
                                <td data-label="Estado">
                                    @if ($prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto') == 0)
                                        <span class="text-success font-bold">Pagado</span>
                                    @else
                                        <span class="text-warning font-bold">En proceso</span>
                                    @endif
                                </td>
                                <td data-label="Acciones">
                                    <a href="{{ route('prestamos.edit', $prestamo->id) }}"
                                        class="btn btn-sm btn-warning dt-button yellow"><span class="material-icons">edit</span></a>
                                    <form action="{{ route('prestamos.destroy', $prestamo->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger dt-button"
                                            onclick="return confirm('¿Estás seguro de que quieres eliminar este préstamo?')"><span class="material-icons">delete_forever</span></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                dom: '<"d-flex justify-content-between align-items-center"Blf>rtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: 'Copiar',
                        title: 'PRESTAPP - REPORTE',
                        className: 'dt-button yellow'
                    },
                    {
                        extend: 'csv',
                        text: 'CSV',
                        title: 'PRESTAPP - Exportación CSV',
                        className: 'dt-button blue'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        title: 'PRESTAPP - Exportación PDF',
                        className: 'dt-button red',
                        customize: function(doc) {
                            // Ocultar la columna de acciones en el PDF
                            doc.content[1].table.body.forEach(function(row) {
                                row.splice(-1, 1);
                            });
                        }
                    },
                    'colvis'
                ],
                language: {
                    url: "{{ asset('datatables/i18n/Spanish.json') }}" // Ruta al archivo de traducción en español
                },
                columnDefs: [
                    { targets: -1, visible: true } // Asegúrate de que la columna de acciones esté visible en la tabla
                ],
                pagingType: 'full_numbers',
                lengthMenu: [10, 25, 50, 75, 100],
                pageLength: 10,
                responsive: true // Añadir soporte responsivo
            });
        });
    </script>
@endsection
