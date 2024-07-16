<!-- resources/views/components/prestamos-table.blade.php -->

<div class="table-responsive">
    <table class="table table-sm table-dark text-center" style="width: 100%;">
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
                <td>{{ intval($prestamosPorDia) }}</td>
                <td>{{ intval($prestamosPorSemana) }}</td>
                <td>{{ intval($prestamosPorMes) }}</td>
                <td>{{ intval($prestamosPorAnio) }}</td>
                <td>{{ intval($totalPrestamos) }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="text-center mt-4">
    <button type="submit" class="btn btn-primary dt-button">
        <span class="material-icons">picture_as_pdf</span>Generar
    </button>
</div>
