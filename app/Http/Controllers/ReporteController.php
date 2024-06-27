<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use TCPDF;

class ReporteController extends Controller
{
    public function generarReporte(Request $request)
    {
        // Lógica para obtener datos para el reporte (ejemplo)
        $clientes = Cliente::all();

        // Crear instancia de TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nombre del autor');
        $pdf->SetTitle('Título del reporte');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Contenido HTML del reporte
        $html = '<h1>Reporte de Clientes</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>ID</th>';
        $html .= '<th>Nombre</th>';
        $html .= '<th>Email</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($clientes as $cliente) {
            $html .= '<tr>';
            $html .= '<td>' . $cliente->id . '</td>';
            $html .= '<td>' . $cliente->nombre . '</td>';
            $html .= '<td>' . $cliente->email . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        // Agregar contenido al PDF
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        // Descargar el PDF
        $pdf->Output('reporte_clientes.pdf', 'D');
    }
}
