<div class="flex flex-col sm:flex-row gap-6 justify-center">

    <!-- Tabla de Resumen de Préstamos -->
    <div class="flex-1 max-w-3xl bg-black p-6 rounded-lg shadow-md">
        <!-- Gráfica de Préstamos -->
        <div class="mb-6 bg-black p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4 text-center text-white">PRÉSTAMOS</h2>
            <canvas id="prestamosChart" height="300"></canvas>
        </div>

        <h2 class="text-lg font-semibold mb-4 text-center text-white">RESUMEN DE PRESTAMOS</h2>
        <table class="w-full divide-y divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Período</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ver</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">DIA</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorDia, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.dia') }}">
                            <span class="material-icons">visibility</span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">SEMANA</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorSemana, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.semana') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">MES</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorMes, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.mes') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">AÑO</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorAnio, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.anio') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">TOTAL</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($totalPrestamos, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.todo') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
            </tbody>
        </table>
        

    </div>

    <!-- Tabla de Resumen de Abonos -->
    <div class="flex-1 max-w-3xl bg-black p-6 rounded-lg shadow-md">
        <!-- Gráfica de Abonos -->
        <div class="mb-6 bg-black p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4 text-center text-white">ABONOS</h2>
            <canvas id="abonosChart" height="300"></canvas>
        </div>

        <h2 class="text-lg font-semibold mb-4 text-center text-white">RESUMEN DE ABONOS</h2>
        <table class="w-full divide-y divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Período</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ver</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">Día</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorDia, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.abonodia') }}">
                            <span class="material-icons">visibility</span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">Semana</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorSemana, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.abonosemanal') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">Mes</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorMes, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.abonomes') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">Año</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorAnio, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.abonoanio') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">Total</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($totalAbonos, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        <a href="{{ route('generar.boleta.abonototal') }}">
                        <span class="material-icons">visibility</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="prestamosModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons text-blue-600">info</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Préstamos por Día</h3>
                            <div class="mt-2">
                                <table class="table w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Prestado</th>
                                        </tr>
                                    </thead>
                                 
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
