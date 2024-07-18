<?php

namespace App\View\Components;

use App\Models\Prestamo;
use App\Models\Abono;
use Carbon\Carbon;
use Illuminate\View\Component;

class DashboardTables extends Component
{
    public $prestamosPorDia;
    public $prestamosPorSemana;
    public $prestamosPorMes;
    public $prestamosPorAnio;
    public $totalPrestamos;
    public $abonosPorDia;
    public $abonosPorSemana;
    public $abonosPorMes;
    public $abonosPorAnio;
    public $totalAbonos;

    public function __construct()
    {
        $this->prestamosPorDia = $this->getPrestamosPorDia();
        $this->prestamosPorSemana = $this->getPrestamosPorSemana();
        $this->prestamosPorMes = $this->getPrestamosPorMes();
        $this->prestamosPorAnio = $this->getPrestamosPorAnio();
        $this->totalPrestamos = $this->getTotalPrestamos();

        $this->abonosPorDia = $this->getAbonosPorDia();
        $this->abonosPorSemana = $this->getAbonosPorSemana();
        $this->abonosPorMes = $this->getAbonosPorMes();
        $this->abonosPorAnio = $this->getAbonosPorAnio();
        $this->totalAbonos = $this->getTotalAbonos();
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

    public function render()
    {
        return view('components.dashboard-tables');
    }
}
