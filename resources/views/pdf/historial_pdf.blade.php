<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Historial de Préstamos de {{ $cliente->nombre }}</title>
</head>
<body>
    <h1>Historial de Préstamos de {{ $cliente->nombre }}</h1>
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cantidad de Préstamo</th>
                <th>Fecha de Préstamo</th>
                <th>Abonos</th>
                <!-- Agrega más columnas según necesites -->
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos as $prestamo)
            <tr>
                <td>{{ $prestamo->id }}</td>
                <td>{{ $prestamo->cantidad_prestamo }}</td>
                <td>{{ \Carbon\Carbon::parse($prestamo->fecha)->format('d/m/Y') }}</td>
                <td>
                    <ul>
                        @foreach ($prestamo->abonos as $abono)
                        <li>Fecha: {{ \Carbon\Carbon::parse($abono->fecha)->format('d/m/Y') }}, Monto: {{ $abono->monto }}</li>
                        @endforeach
                    </ul>
                </td>
                <!-- Agrega más columnas según necesites -->
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
