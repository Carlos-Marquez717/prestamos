@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center">Préstamos de la Semana</h1>
        <div class="table-responsive">
            <table class="table table-dark">
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
                            <td>{{ $prestamo->id }}</td>
                            <td>{{ $prestamo->cliente->nombre }}</td>
                            <td>{{ $prestamo->cantidad_prestamo }}</td>
                            <td>{{ \Carbon\Carbon::parse($prestamo->fecha)->format('d/m/Y H:i') }}</td>
                            <td>{{ $prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto') }}</td>
                            <td>
                                @if ($prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto') == 0)
                                    <span class="text-success">Pagado</span>
                                @else
                                    <span class="text-warning">En proceso</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('prestamos.destroy', $prestamo->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
