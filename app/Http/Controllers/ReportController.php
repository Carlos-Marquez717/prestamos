<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Abono;
use App\Models\Cliente; // Asegúrate de tener este modelo importado
use TCPDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Calculando préstamos y abonos por día, semana, mes, año y total
        $prestamosPorDia = $this->calcularPrestamos('day');
        $prestamosPorSemana = $this->calcularPrestamos('week');
        $prestamosPorMes = $this->calcularPrestamos('month');
        $prestamosPorAnio = $this->calcularPrestamos('year');
        $totalPrestamos = $this->calcularPrestamos('total');

        $abonosPorDia = $this->calcularAbonos('day');
        $abonosPorSemana = $this->calcularAbonos('week');
        $abonosPorMes = $this->calcularAbonos('month');
        $abonosPorAnio = $this->calcularAbonos('year');
        $totalAbonos = $this->calcularAbonos('total');

        return view('prestamos', compact(
            'prestamosPorDia', 'prestamosPorSemana', 'prestamosPorMes', 'prestamosPorAnio', 'totalPrestamos',
            'abonosPorDia', 'abonosPorSemana', 'abonosPorMes', 'abonosPorAnio', 'totalAbonos'
        ));
    }

    public function generarBoletaPrestamos($type)
    {
        $prestamos = [];
        $abonos = [];
        $totalPrestado = 0;
        $totalAbonos = 0;

        switch ($type) {
            case 'dia':
                $prestamos = $this->getPrestamos('day');
                $abonos = $this->getAbonos('day');
                $totalPrestado = $this->calcularPrestamos('day');
                $totalAbonos = $this->calcularAbonos('day');
                break;
            case 'semana':
                $prestamos = $this->getPrestamos('week');
                $abonos = $this->getAbonos('week');
                $totalPrestado = $this->calcularPrestamos('week');
                $totalAbonos = $this->calcularAbonos('week');
                break;
            case 'mes':
                $prestamos = $this->getPrestamos('month');
                $abonos = $this->getAbonos('month');
                $totalPrestado = $this->calcularPrestamos('month');
                $totalAbonos = $this->calcularAbonos('month');
                break;
            case 'anio':
                $prestamos = $this->getPrestamos('year');
                $abonos = $this->getAbonos('year');
                $totalPrestado = $this->calcularPrestamos('year');
                $totalAbonos = $this->calcularAbonos('year');
                break;
            case 'total':
                $prestamos = $this->getPrestamos('total');
                $abonos = $this->getAbonos('total');
                $totalPrestado = $this->calcularPrestamos('total');
                $totalAbonos = $this->calcularAbonos('total');
                break;
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Reporte de Préstamos y Abonos');
        $pdf->SetSubject('Reporte de Préstamos y Abonos');
        $pdf->SetKeywords('TCPDF, PDF, reporte, préstamos, abonos');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        // Título
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 10, 'Reporte de Préstamos y Abonos', 0, 1, 'C');

        // Fecha
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Fecha: ' . Carbon::now()->format('d/m/Y H:i'), 0, 1, 'C');

        // Contenido
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Ln();

        // Préstamos
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Préstamos', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);

        foreach ($prestamos as $prestamo) {
            $pdf->Cell(0, 10, 'ID: ' . $prestamo->id, 0, 1, 'L');
            $pdf->Cell(0, 10, 'Cliente: ' . json_encode($prestamo->cliente), 0, 1, 'L');
            $pdf->Cell(0, 10, 'Monto: ' . $prestamo->monto, 0, 1, 'L');
            $pdf->Cell(0, 10, 'Fecha: ' . $prestamo->created_at->format('d/m/Y'), 0, 1, 'L');
            $pdf->Ln();
        }

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Total Prestado: ' . $totalPrestado, 0, 1, 'L');
        $pdf->Ln();

        // Abonos
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Abonos', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);

        foreach ($abonos as $abono) {
            $pdf->Cell(0, 10, 'ID: ' . $abono->id, 0, 1, 'L');
            $pdf->Cell(0, 10, 'Cliente: ' . json_encode($abono->cliente), 0, 1, 'L');
            $pdf->Cell(0, 10, 'Monto: ' . $abono->monto, 0, 1, 'L');
            $pdf->Cell(0, 10, 'Fecha: ' . $abono->created_at->format('d/m/Y'), 0, 1, 'L');
            $pdf->Ln();
        }

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Total Abonado: ' . $totalAbonos, 0, 1, 'L');
        $pdf->Ln();

        $pdf->Output('reporte.pdf', 'I');
        exit;
    }

    private function calcularPrestamos($interval)
    {
        if ($interval === 'total') {
            return Prestamo::sum('monto');
        } else {
            return Prestamo::whereBetween('created_at', [
                Carbon::now()->startOf($interval)->toDateTimeString(),
                Carbon::now()->endOf($interval)->toDateTimeString()
            ])->sum('monto');
        }
    }

    private function calcularAbonos($interval)
    {
        if ($interval === 'total') {
            return Abono::sum('monto');
        } else {
            return Abono::whereBetween('created_at', [
                Carbon::now()->startOf($interval)->toDateTimeString(),
                Carbon::now()->endOf($interval)->toDateTimeString()
            ])->sum('monto');
        }
    }

    private function getPrestamos($interval)
    {
        if ($interval === 'total') {
            return Prestamo::with('cliente')->get();
        } else {
            return Prestamo::with('cliente')->whereBetween('created_at', [
                Carbon::now()->startOf($interval)->toDateTimeString(),
                Carbon::now()->endOf($interval)->toDateTimeString()
            ])->get();
        }
    }

    private function getAbonos($interval)
    {
        if ($interval === 'total') {
            return Abono::with('cliente')->get();
        } else {
            return Abono::whereBetween('created_at', [
                Carbon::now()->startOf($interval)->toDateTimeString(),
                Carbon::now()->endOf($interval)->toDateTimeString()
            ])->get();
        }
    }
}
