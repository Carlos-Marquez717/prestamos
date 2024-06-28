<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HISTORIAL GENERAL DE PRESTAMOS</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center">HISTORIAL GENERAL DE PRESTAMOS</h1>
    @foreach ($clientes as $cliente)
        <h2 style="text-align: center; background-color: black; color: white; border: 1px solid white;">{{ $cliente->nombre }}</h2>
        <table>
            <thead>
                <tr>
                    
                    <th style="text-align: center; background-color: black; color: white;">Fecha</th>
                    <th style="text-align: center; background-color: black; color: white;">Cantidad</th>
                    <th style="text-align: center; background-color: black; color: white;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cliente->prestamos as $prestamo)
                    <tr>
                        
                        <td style="text-align: center">{{ \Carbon\Carbon::parse($prestamo->fecha)->format('d-m-Y') }}</td>
                        <td style="text-align: center">{{ $prestamo->cantidad_prestamo }}</td>
                        <td style="text-align: center">{{ $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto') == 0 ? 'Pagado' : 'Proceso' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    @endforeach
    <div>
        <h1></h1>
    </div>
</body>
</html>
