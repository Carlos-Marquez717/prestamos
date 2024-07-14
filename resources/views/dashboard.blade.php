@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between">
                <!-- Gráfica de Préstamos -->
                <div class="bg-black p-6 rounded-lg shadow-md mb-6 w-1/2 mr-4">
                    <h2 class="text-lg font-semibold mb-4 text-center">PRESTAMOS</h2>
                    <canvas id="prestamosChart" height="300"></canvas>
                    <button class="mt-4 bg-blue-500 text-white p-2 rounded" onclick="generateReport('prestamos')"><span class="material-icons">picture_as_pdf</span></button>
                </div>

                <!-- Gráfica de Abonos -->
                <div class="bg-black p-6 rounded-lg shadow-md mb-6 w-1/2">
                    <h2 class="text-lg font-semibold mb-4 text-center">ABONOS</h2>
                    <canvas id="abonosChart" height="300"></canvas>
                    <button class="mt-4 bg-blue-500 text-white p-2 rounded" onclick="generateReport('abonos')"><span class="material-icons">picture_as_pdf</span></button>
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
                            {{ $totalPrestamos ?? 0 }} // Total en monto
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.2)', // Azul
                            'rgba(75, 192, 192, 0.2)', // Verde
                            'rgba(255, 206, 86, 0.2)', // Amarillo
                            'rgba(153, 102, 255, 0.2)', // Morado
                            'rgba(255, 99, 132, 0.2)' // Color total
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)' // Color total
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
                            {{ $totalAbonos ?? 0 }} // Total en monto
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)', // Color diferente para abonos
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)' // Color total
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)' // Color total
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
