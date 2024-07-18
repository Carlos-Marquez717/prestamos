<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Abono;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Obtén los datos de préstamos y abonos
        $prestamosPorDia = $this->getPrestamosPorDia(); // Asegúrate de que esto devuelva un array
        $prestamosPorSemana = $this->getPrestamosPorSemana(); // Asegúrate de que esto devuelva un array
        $prestamosPorMes = $this->getPrestamosPorMes(); // Asegúrate de que esto devuelva un array
        $prestamosPorAnio = $this->getPrestamosPorAnio(); // Asegúrate de que esto devuelva un array
        
        $abonosPorDia = $this->getAbonosPorDia(); // Asegúrate de que esto devuelva un array
        $abonosPorSemana = $this->getAbonosPorSemana(); // Asegúrate de que esto devuelva un array
        $abonosPorMes = $this->getAbonosPorMes(); // Asegúrate de que esto devuelva un array
        $abonosPorAnio = $this->getAbonosPorAnio(); // Asegúrate de que esto devuelva un array

        // Calcula los totales, asegurándote de que cada valor sea numérico
        $totalPrestamosPorDia = is_array($prestamosPorDia) ? array_sum($prestamosPorDia) : $prestamosPorDia;
        $totalPrestamosPorSemana = is_array($prestamosPorSemana) ? array_sum($prestamosPorSemana) : $prestamosPorSemana;
        $totalPrestamosPorMes = is_array($prestamosPorMes) ? array_sum($prestamosPorMes) : $prestamosPorMes;
        $totalPrestamosPorAnio = is_array($prestamosPorAnio) ? array_sum($prestamosPorAnio) : $prestamosPorAnio;
        
        $totalAbonosPorDia = is_array($abonosPorDia) ? array_sum($abonosPorDia) : $abonosPorDia;
        $totalAbonosPorSemana = is_array($abonosPorSemana) ? array_sum($abonosPorSemana) : $abonosPorSemana;
        $totalAbonosPorMes = is_array($abonosPorMes) ? array_sum($abonosPorMes) : $abonosPorMes;
        $totalAbonosPorAnio = is_array($abonosPorAnio) ? array_sum($abonosPorAnio) : $abonosPorAnio;
        
        // Asegúrate de que `getTotalPrestamos()` y `getTotalAbonos()` devuelvan valores numéricos
        $totalPrestamos = $this->getTotalPrestamos(); // Debe devolver un valor numérico
        $totalAbonos = $this->getTotalAbonos(); // Debe devolver un valor numérico
        
        // Pasa los datos a la vista
        return view('dashboard', [
            'prestamosPorDia' => $totalPrestamosPorDia,
            'prestamosPorSemana' => $totalPrestamosPorSemana,
            'prestamosPorMes' => $totalPrestamosPorMes,
            'prestamosPorAnio' => $totalPrestamosPorAnio,
            'totalPrestamos' => $totalPrestamos,
            'abonosPorDia' => $totalAbonosPorDia,
            'abonosPorSemana' => $totalAbonosPorSemana,
            'abonosPorMes' => $totalAbonosPorMes,
            'abonosPorAnio' => $totalAbonosPorAnio,
            'totalAbonos' => $totalAbonos,
        ]);
    }

    

    public function index()
    {
        $prestamosPorDia = $this->getPrestamosPorDia();
        $prestamosPorSemana = $this->getPrestamosPorSemana();
        $prestamosPorMes = $this->getPrestamosPorMes();
        $prestamosPorAnio = $this->getPrestamosPorAnio();
        $totalPrestamos = $this->getTotalPrestamos();
    
        $abonosPorDia = $this->getAbonosPorDia();
        $abonosPorSemana = $this->getAbonosPorSemana();
        $abonosPorMes = $this->getAbonosPorMes();
        $abonosPorAnio = $this->getAbonosPorAnio();
        $totalAbonos = $this->getTotalAbonos();
    
        return view('dashboard')
            ->with('prestamosPorDia', $prestamosPorDia)
            ->with('prestamosPorSemana', $prestamosPorSemana)
            ->with('prestamosPorMes', $prestamosPorMes)
            ->with('prestamosPorAnio', $prestamosPorAnio)
            ->with('totalPrestamos', $totalPrestamos)
            ->with('abonosPorDia', $abonosPorDia)
            ->with('abonosPorSemana', $abonosPorSemana)
            ->with('abonosPorMes', $abonosPorMes)
            ->with('abonosPorAnio', $abonosPorAnio)
            ->with('totalAbonos', $totalAbonos);
    }
    
    


    private function getTotalPrestamos()
    {
        return Prestamo::sum('cantidad_prestamo');
    }

    private function getTotalAbonos()
    {
        return Abono::sum('monto');
    }

    private function getPrestamosPorDia()
    {
        return Prestamo::whereDate('created_at', Carbon::today())->sum('cantidad_prestamo');
    }

    private function getPrestamosPorSemana()
    {
        return Prestamo::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('cantidad_prestamo');

    }

    private function getPrestamosPorMes()
    {
        return Prestamo::whereMonth('created_at', Carbon::now()->month)->sum('cantidad_prestamo');
    }

    private function getPrestamosPorAnio()
    {
        return Prestamo::whereYear('created_at', Carbon::now()->year)->sum('cantidad_prestamo');
    }

    private function getAbonosPorDia()
    {
        return Abono::whereDate('created_at', Carbon::today())->sum('monto');
    }

    private function getAbonosPorSemana()
    {
        return Abono::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('monto');
    }

    private function getAbonosPorMes()
    {
        return Abono::whereMonth('created_at', Carbon::now()->month)->sum('monto');
    }

    private function getAbonosPorAnio()
    {
        return Abono::whereYear('created_at', Carbon::now()->year)->sum('monto');
    }

    private function calculateTotals($modelData, $period)
    {
        $totals = [];
    
        foreach ($modelData as $data) {
            $date = Carbon::parse($data->created_at);
            
            // Agrupa los datos según el período especificado
            switch ($period) {
                case 'day':
                    $periodKey = $date->format('Y-m-d'); // Agrupa por día
                    break;
                case 'week':
                    $periodKey = $date->startOfWeek()->format('Y-W'); // Agrupa por semana
                    break;
                case 'month':
                    $periodKey = $date->format('Y-m'); // Agrupa por mes
                    break;
                case 'year':
                    $periodKey = $date->format('Y'); // Agrupa por año
                    break;
                default:
                    $periodKey = $date->format('Y-m-d');
                    break;
            }
    
            if (!isset($totals[$periodKey])) {
                $totals[$periodKey] = 0;
            }
    
            // Suma el monto del préstamo o abono al total correspondiente
            $totals[$periodKey] += $data->cantidad_prestamo ?? $data->monto;
        }
    
        return $totals;
    }
    
    public function showPrestamosTable()
    {
        $data = [
            'prestamosPorDia' => $this->getPrestamosPorDia(),
            'prestamosPorSemana' => $this->getPrestamosPorSemana(),
            'prestamosPorMes' => $this->getPrestamosPorMes(),
            'prestamosPorAnio' => $this->getPrestamosPorAnio(),
            'totalPrestamos' => $this->getTotalPrestamos(),
        ];

        return view('components.PrestamosTable', $data);
    }
}
