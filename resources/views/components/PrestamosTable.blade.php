<!-- resources/views/components/PrestamosTable.blade.php -->

<div class="custom-card mx-auto">
    <h2 class="text-center text-white">PRESTAMOS</h2>
    <div class="table-responsive">
        <table class="table table-sm table-dark text-center">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">DIA</th>
                    <th scope="col">SEMANA</th>
                    <th scope="col">MES</th>
                    <th scope="col">AÑO</th>
                    <th scope="col">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $prestamosPorDia }}</td>
                    <td>{{ $prestamosPorSemana }}</td>
                    <td>{{ $prestamosPorMes }}</td>
                    <td>{{ $prestamosPorAnio }}</td>
                    <td>{{ $totalPrestamos }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary dt-button"><span class="material-icons">picture_as_pdf</span>Generar</button>

    </div>
    <style>
        /* Estilo para el card negro */
        .custom-card {
            background-color: #070707;
            /* Negro */
            color: white;
            /* Texto blanco */
            border-radius: 0.5rem;
            /* Bordes redondeados */
            margin-top: 20px;
            /* Espacio superior */
            padding: 20px;
            /* Espaciado interno */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            /* Sombra */
        }

        .dt-button.red {
            background-color: #DC3545;
            /* Rojo */
            color: white;
        }

        .table-dark {
            background-color: #020202;
            /* Fondo negro */
            color: white;
            /* Texto blanco */
        }

        .table-dark th,
        .table-dark td {
            border-color: white;
            /* Color de borde blanco */
        }
    </style>
</div>
