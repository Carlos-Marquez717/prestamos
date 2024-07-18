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
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">DIA</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorDia, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">SEMANA</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorSemana, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">MES</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorMes, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">AÑO</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamosPorAnio, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">TOTAL</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($totalPrestamos, 2) }}</td>
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
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white uppercase tracking-wider">DIa</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorDia, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white uppercase tracking-wider">Semana</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorSemana, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white uppercase tracking-wider">Mes</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorMes, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white uppercase tracking-wider">Año</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($abonosPorAnio, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white uppercase tracking-wider">Total</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($totalAbonos, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
