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


        return view('dashboard', [
            'prestamosPorDia' => $this->getPrestamosPorDia(),
            'prestamosPorSemana' => $this->getPrestamosPorSemana(),
            'prestamosPorMes' => $this->getPrestamosPorMes(),
            'prestamosPorAnio' => $this->getPrestamosPorAnio(),
            'totalPrestamos' => $this->getTotalPrestamos(),
            'abonosPorDia' => $this->getAbonosPorDia(),
            'abonosPorSemana' => $this->getAbonosPorSemana(),
            'abonosPorMes' => $this->getAbonosPorMes(),
            'abonosPorAnio' => $this->getAbonosPorAnio(),
            'totalAbonos' => $this->getTotalAbonos(),
        ]);
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
