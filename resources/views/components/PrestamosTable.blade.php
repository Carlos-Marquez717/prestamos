<div class="custom-card mx-auto">
    <h2 class="text-center text-white">PRESTAMOS</h2>
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
                    <td style="width: 20%;">{{ intval($prestamosPorDia) }}</td>
                    <td style="width: 20%;">{{ intval($prestamosPorSemana) }}</td>
                    <td style="width: 20%;">{{ intval($prestamosPorMes) }}</td>
                    <td style="width: 20%;">{{ intval($prestamosPorAnio) }}</td>
                    <td style="width: 20%;">{{ intval($totalPrestamos) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary dt-button"><span class="material-icons">picture_as_pdf</span>Generar</button>
    </div>
</div>
