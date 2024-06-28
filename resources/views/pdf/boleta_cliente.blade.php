<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta del Cliente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
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

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #000;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>HISTORIAL DEL CLIENTE: {{ $cliente->nombre }}</h1>
    </div>
    <div class="section">
        <table>
            <tr>
                <th colspan="2" style="text-align: center;">INFORMACIÓN DEL CLIENTE</th>
            </tr>
            <tr>
                <td>Nombre:</td>
                <td>{{ $cliente->nombre }}</td>
            </tr>
            <tr>
                <td>Dirección:</td>
                <td>{{ $cliente->direccion }}</td>
            </tr>
            <tr>
                <td>Teléfono:</td>
                <td>{{ $cliente->telefono }}</td>
            </tr>
            <tr>
                <td>Correo Electrónico:</td>
                <td>{{ $cliente->email }}</td>
            </tr>
        </table>

        <h2 style="text-align: center">Historial de Préstamos</h2>
        @foreach ($cliente->prestamos as $prestamo)
            <table>
                <thead>
                    <tr>
                        <th style="text-align: center;">Cantidad Prestada</th>
                        <th style="text-align: center;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center">{{ $prestamo->cantidad_prestamo }}</td>
                        <td style="text-align: center">{{ Carbon\Carbon::parse($prestamo->fecha)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align: center;">Abonos</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Fecha</th>
                        <th style="text-align: center;">Monto</th>
                    </tr>
                    @foreach ($prestamo->abonos as $abono)
                        <tr>
                            <td style="text-align: center;">{{ Carbon\Carbon::parse($abono->fecha)->format('d-m-Y') }}</td>
                            <td style="text-align: center;">{{ $abono->monto }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="text-align: center;">PENDIENTE POR PAGAR:</td>
                        <td style="text-align: center;">{{ $prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto') }}</td>
                    </tr>

                </tbody>
            </table>
            <br>
            <div class="qr-code">
                {!! $qrCode !!}
            </div>
            
        @endforeach
    </div>
</body>

</html>
