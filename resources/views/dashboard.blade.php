@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <div class="bg-black p-4 rounded-lg shadow-md flex items-center justify-center">
                    <img src="{{ asset('images/banco.png') }}" alt="Logo Banco" class="h-16 w-16 mr-4">
                    <div>
                        <h2 class="text-lg font-semibold mb-2 text-white">BIENVENIDO A PRESSTAPP</h2>
                        <p class="text-sm text-white mb-1">SR(A): {{ Auth::user()->name }}</p>
                        <p class="text-sm text-white">Fecha y Hora: {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between">
                    <!-- Gráfica de Préstamos -->
                    <div class="bg-black p-6 rounded-lg shadow-md mb-6 w-full sm:w-1/2">
                        <h2 class="text-lg font-semibold mb-4 text-center">PRESTAMOS</h2>
                        <canvas id="prestamosChart" height="300"></canvas>
                        <div class="mt-4 flex justify-around">
                            <a href="{{ route('reporte.prestamos', 'dia') }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">today</span>
                                <span class="hidden sm:inline">Dia</span>
                            </a>
                            <a href="{{ route('reporte.prestamos', 'semana') }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">calendar_view_week</span>
                                <span class="hidden sm:inline">Semana</span>
                            </a>
                            <a href="{{ route('reporte.prestamos', 'mes') }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">calendar_month</span>
                                <span class="hidden sm:inline">Mes</span>
                            </a>
                            <a href="{{ route('reporte.prestamos', 'anio') }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">date_range</span>
                                <span class="hidden sm:inline">Año</span>
                            </a>
                            <a href="{{ route('reporte.prestamos', 'total') }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">domain_verification</span>
                                <span class="hidden sm:inline">Total</span>
                            </a>
                        </div>

                    </div>

                    <!-- Gráfica de Abonos -->
                    <div class="bg-black p-6 rounded-lg shadow-md mb-6 w-full sm:w-1/2 sm:ml-4">
                        <h2 class="text-lg font-semibold mb-4 text-center">ABONOS</h2>
                        <canvas id="abonosChart" height="300"></canvas>
                        <div class="mt-4 flex justify-around">
                            <a href="{{ route('reporte.abonos', 'dia') }}"
                                class="bg-green-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">today</span>
                                <span class="hidden sm:inline">Dia</span>
                            </a>
                            <a href="{{ route('reporte.abonos', 'semana') }}"
                                class="bg-green-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">calendar_view_week</span>
                                <span class="hidden sm:inline">Semana</span>
                            </a>
                            <a href="{{ route('reporte.abonos', 'mes') }}"
                                class="bg-green-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">calendar_month</span>
                                <span class="hidden sm:inline">Mes</span>
                            </a>
                            <a href="{{ route('reporte.abonos', 'anio') }}"
                                class="bg-green-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">date_range</span>
                                <span class="hidden sm:inline">Año</span>
                            </a>
                            <a href="{{ route('reporte.abonos', 'total') }}"
                                class="bg-green-500 text-white px-3 py-1 rounded flex items-center space-x-1">
                                <span class="material-icons text-xl">domain_verification</span>
                                <span class="hidden sm:inline">Total</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const prestamosCtx = document.getElementById('prestamosChart').getContext('2d');
                const prestamosChart = new Chart(prestamosCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Día', 'Semana', 'Mes', 'Año', 'Total'],
                        datasets: [{
                            label: 'Préstamos (Monto)',
                            data: [
                                {{ $prestamosPorDia ?? 0 }},
                                {{ $prestamosPorSemana ?? 0 }},
                                {{ $prestamosPorMes ?? 0 }},
                                {{ $prestamosPorAnio ?? 0 }},
                                {{ $totalPrestamos ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 99, 132, 0.2)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                const abonosCtx = document.getElementById('abonosChart').getContext('2d');
                const abonosChart = new Chart(abonosCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Día', 'Semana', 'Mes', 'Año', 'Total'],
                        datasets: [{
                            label: 'Abonos (Monto)',
                            data: [
                                {{ $abonosPorDia ?? 0 }},
                                {{ $abonosPorSemana ?? 0 }},
                                {{ $abonosPorMes ?? 0 }},
                                {{ $abonosPorAnio ?? 0 }},
                                {{ $totalAbonos ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

            function generateReport(type) {
                window.location.href = `/generate-report/${type}`;
            }
        </script>
    @endsection
