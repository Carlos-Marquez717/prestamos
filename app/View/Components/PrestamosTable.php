<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PrestamosTable extends Component
{
    public $prestamosPorDia;
    public $prestamosPorSemana;
    public $prestamosPorMes;
    public $prestamosPorAnio;
    public $totalPrestamos;

    public function __construct($prestamosPorDia, $prestamosPorSemana, $prestamosPorMes, $prestamosPorAnio, $totalPrestamos)
    {
        $this->prestamosPorDia = $prestamosPorDia;
        $this->prestamosPorSemana = $prestamosPorSemana;
        $this->prestamosPorMes = $prestamosPorMes;
        $this->prestamosPorAnio = $prestamosPorAnio;
        $this->totalPrestamos = $totalPrestamos;
    }

    public function render()
    {
        return view('components.prestamos-table');
    }
}
