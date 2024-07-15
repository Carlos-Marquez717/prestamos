<!-- resources/views/components/AbonosTable.blade.php -->

<div class="custom-card mx-auto">
    <h2 class="text-center text-white">ABONOS</h2>
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
                    <td>{{ $abonosPorDia }}</td>
                    <td>{{ $abonosPorSemana }}</td>
                    <td>{{ $abonosPorMes }}</td>
                    <td>{{ $abonosPorAnio }}</td>
                    <td>{{ $totalAbonos }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary dt-button"><span class="material-icons">picture_as_pdf</span>Generar</button>
    </div>
</div>
