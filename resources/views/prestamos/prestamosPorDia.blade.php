<!-- resources/views/prestamos/tabla_dia.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Prestamos por Día</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Total Prestado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestamos as $prestamo)
                    <tr>
                        <td>{{ $prestamo->fecha }}</td>
                        <td>{{ $prestamo->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
