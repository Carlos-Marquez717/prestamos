<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta del Préstamo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #111111;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0a0a0a;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Boleta del Préstamo</h1>
        <h2>Cliente: {{ $prestamo->cliente->nombre }}</h2>
    </div>
    <div class="section">
        <table style="margin-bottom: 20px">
            <tr>
                <th colspan="2" style="text-align: center; background-color: black; color: white;">INFORMACIÓN DEL PRÉSTAMO</th>
            </tr>
            <tr>
                <td>Cantidad Prestada:</td>
                <td>{{ $prestamo->cantidad_prestamo }}</td>
            </tr>
            <tr>
                <td>Fecha del Préstamo:</td>
                <td>{{ \Carbon\Carbon::parse($prestamo->fecha)->format('d-m-Y') }}</td>
            </tr>
        </table>
        <h2 style="text-align: center">Abonos</h2>
        <table style="margin-bottom: 20px">
            <thead>
                <tr>
                    <th style="text-align: center; background-color: black; color: white;">Fecha</th>
                    <th style="text-align: center; background-color: black; color: white;">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamo->abonos as $abono)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($abono->fecha)->format('d-m-Y') }}</td>
                        <td>{{ $abono->monto }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="text-align: center; background-color: black; color: white;">PENDIENTE POR PAGAR:</td>
                    <td style="text-align: center; background-color: black; color: white;">
                        {{ $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
