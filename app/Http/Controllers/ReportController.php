<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Abono;
use TCPDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function generarBoletaPrestamos($type)
    {
        try {
            switch ($type) {
                case 'dia':
                    $data = $this->getPrestamosPorDia();
                    break;
                case 'semana':
                    $data = $this->getPrestamosPorSemana();
                    break;
                case 'mes':
                    $data = $this->getPrestamosPorMes();
                    break;
                case 'anio':
                    $data = $this->getPrestamosPorAnio();
                    break;
                case 'total':
                    $totalPrestamos = $this->getTotalPrestamos(); // Obtener el total de préstamos
                    break;
                default:
                    abort(404);
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Tu Nombre');
            $pdf->SetTitle('BOLETA DE PRÉSTAMOS');
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->AddPage();

            // Añadir logo y título centrados
            $logo = public_path('images/Banco.svg');
            $pdf->ImageSVG($logo, $x = 15, $y = 15, $w = 30, $h = 30);
            $pdf->SetXY(50, 15);
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Cell(0, 15, '', 0, 1, 'C');

            // Añadir contenido HTML a la boleta
            $html = '<h1 style="text-align:center; background-color: black; color: white; border: 1px solid white;">BOLETA DE PRÉSTAMOS</h1>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Tipo:</strong> ' . ucfirst($type) . '</p>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Fecha:</strong> ' . now()->format('d-m-Y') . '</p>';

            if ($type === 'total') {
                $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Total:</strong> $ ' . number_format($totalPrestamos, 2) . '</p>';
            } else {
                $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Datos:</strong></p>';
                $html .= '<table border="1" cellpadding="5" cellspacing="0" style="margin:auto; border-collapse: collapse;">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th style="text-align:center; background-color: black; color: white; border: 1px solid white;">Fecha</th>';
                $html .= '<th style="text-align:center; background-color: black; color: white; border: 1px solid white;">Monto</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';

                foreach ($data as $item) {
                    $html .= '<tr>';
                    $html .= '<td style="text-align:center; background-color: black; color: white; border: 1px solid white;">' . $item['fecha'] . '</td>';
                    $html .= '<td style="text-align:center; background-color: black; color: white; border: 1px solid white;">$ ' . number_format($item['monto'], 2) . '</td>';
                }

                $html .= '</tbody>';
                $html .= '</table>';
            }

            // Escribir el HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Descargar el PDF
            $pdf->Output('boleta_prestamos_' . $type . '.pdf', 'D');
            exit;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }




    public function generarBoletaAbonos($type)
    {
        try {
            switch ($type) {
                case 'dia':
                    $data = $this->getAbonosPorDia();
                    break;
                case 'semana':
                    $data = $this->getAbonosPorSemana();
                    break;
                case 'mes':
                    $data = $this->getAbonosPorMes();
                    break;
                case 'anio':
                    $data = $this->getAbonosPorAnio();
                    break;
                case 'total':
                    $data = $this->getTotalAbonos();
                    break;
                default:
                    abort(404);
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Tu Nombre');
            $pdf->SetTitle('BOLETA DE ABONOS');
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->AddPage();

            // Añadir logo y título centrados
            $logo = public_path('images/Banco.svg');
            $pdf->ImageSVG($logo, $x = 15, $y = 15, $w = 30, $h = 30);
            $pdf->SetXY(50, 15);
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Cell(0, 15, '', 0, 1, 'C');

            // Añadir contenido HTML a la boleta
            $html = '<h1 style="text-align:center; background-color: black; color: white; border: 1px solid white;">REPORTE DE ABONOS</h1>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Tipo:</strong> ' . ucfirst($type) . '</p>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Fecha:</strong> ' . now()->format('d-m-Y') . '</p>';

            if ($type === 'total') {
                $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Total:</strong> ' . $data . '</p>';
            } else {
                $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>Datos:</strong></p>';
                $html .= '<table border="1" cellpadding="5" cellspacing="0" style="margin:auto; border-collapse: collapse;">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th style="text-align:center; background-color: black; color: white; border: 1px solid white;">Fecha</th>';
                $html .= '<th style="text-align:center; background-color: black; color: white; border: 1px solid white;">Monto</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';

                foreach ($data as $item) {
                    $html .= '<tr>';
                    $html .= '<td style="text-align:center; background-color: black; color: white; border: 1px solid white;">' . $item['fecha'] . '</td>';
                    $html .= '<td style="text-align:center; background-color: black; color: white; border: 1px solid white;">' . $item['monto'] . '</td>';
                    $html .= '</tr>';
                }

                $html .= '</tbody>';
                $html .= '</table>';
            }

            // Escribir el HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Descargar el PDF
            $pdf->Output('boleta_abonos_' . $type . '.pdf', 'D');
            exit;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }



    private function getTotalPrestamos()
    {
        return Prestamo::sum('monto');
    }

    private function getTotalAbonos()
    {
        return Abono::sum('monto');
    }

    private function getPrestamosPorDia()
    {
        return Prestamo::selectRaw('DATE(created_at) as fecha, SUM(monto) as total_prestamos')
            ->whereDate('created_at', Carbon::today())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->toArray();
    }

    public function reportePrestamosPorDia()
    {
        $prestamosPorDia = $this->getPrestamosPorDia();
        return view('reporte.prestamos', [
            'prestamosPorDia' => $prestamosPorDia,
        ]);
    }
    

    private function getPrestamosPorSemana()
    {
        return Prestamo::selectRaw('WEEK(created_at) as semana, SUM(monto) as monto')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->groupBy('semana')
            ->get()
            ->toArray();
    }

    private function getPrestamosPorMes()
    {
        return Prestamo::selectRaw('MONTH(created_at) as mes, SUM(monto) as monto')
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('mes')
            ->get()
            ->toArray();
    }

    private function getPrestamosPorAnio()
    {
        return Prestamo::selectRaw('YEAR(created_at) as anio, SUM(monto) as monto')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('anio')
            ->get()
            ->toArray();
    }

    private function getAbonosPorDia()
    {
        return Abono::selectRaw('DATE(created_at) as fecha, SUM(monto) as monto')
            ->whereDate('created_at', Carbon::today())
            ->groupBy('fecha')
            ->get()
            ->toArray();
    }

    private function getAbonosPorSemana()
    {
        return Abono::selectRaw('WEEK(created_at) as semana, SUM(monto) as monto')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->groupBy('semana')
            ->get()
            ->toArray();
    }

    private function getAbonosPorMes()
    {
        return Abono::selectRaw('MONTH(created_at) as mes, SUM(monto) as monto')
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('mes')
            ->get()
            ->toArray();
    }

    private function getAbonosPorAnio()
    {
        return Abono::selectRaw('YEAR(created_at) as anio, SUM(monto) as monto')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('anio')
            ->get()
            ->toArray();
    }
}
