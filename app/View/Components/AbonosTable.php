<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AbonosTable extends Component
{
    public $abonosPorDia;
    public $abonosPorSemana;
    public $abonosPorMes;
    public $abonosPorAnio;
    public $totalAbonos;

    public function __construct($abonosPorDia, $abonosPorSemana, $abonosPorMes, $abonosPorAnio, $totalAbonos)
    {
        $this->abonosPorDia = $abonosPorDia;
        $this->abonosPorSemana = $abonosPorSemana;
        $this->abonosPorMes = $abonosPorMes;
        $this->abonosPorAnio = $abonosPorAnio;
        $this->totalAbonos = $totalAbonos;
    }

    public function render()
    {
        return view('components.abonos-table');
    }
}
