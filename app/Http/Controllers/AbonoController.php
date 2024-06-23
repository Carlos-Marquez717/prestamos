<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use TCPDF;
use App\Models\Prestamo;
use App\Models\Abono;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbonoController extends Controller
{
    public function create(Prestamo $prestamo)
    {
        return view('abonos.create', compact('prestamo'));
    }

    public function store(Request $request, Prestamo $prestamo)
    {
        // Validación
        $request->validate([
            'monto' => 'required|numeric|min:0|max:' . ($prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto')),
            'fecha' => 'required|date_format:d-m-Y', // Validar en formato 'd-m-Y'
        ]);

        // Formatear la fecha en formato 'Y-m-d' para guardarla en la base de datos
        $fechaFormateada = Carbon::createFromFormat('d-m-Y', $request->fecha)->format('Y-m-d');

        // Almacenamiento del abono
        $abono = Abono::create([
            'prestamo_id' => $prestamo->id,
            'monto' => $request->monto,
            'fecha' => $fechaFormateada, // Guardar la fecha formateada
        ]);

        // Generar boleta después de crear el abono
        $this->generarBoleta($abono);

        // Redirigir a la vista show del préstamo
        return redirect()->route('prestamos.show', $prestamo)->with('success', 'Abono creado exitosamente y boleta generada.');
    }

    public function generarBoleta(Abono $abono)
    {
        // Cargar las relaciones necesarias
        $abono->load('prestamo.cliente');

        // Verificar que el cliente y el préstamo estén cargados correctamente
        if (!$abono->prestamo || !$abono->prestamo->cliente) {
            abort(404, 'El préstamo o el cliente asociado no fueron encontrados.');
        }

        $cliente = $abono->prestamo->cliente;
        $saldoRestante = $abono->prestamo->cantidad_prestamo - $abono->prestamo->abonos->sum('monto');
        $filename = 'Boleta_Abono_' . $cliente->nombre . '_' . $abono->id . '.pdf';

        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Tu Nombre');
        $pdf->SetTitle('BOLETA DE ABONO');
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        // Añadir logo y título centrados
        $logo = public_path('images/Banco.svg');
        $pdf->ImageSVG($logo, $x = 15, $y = 15, $w = 30, $h = 30);
        $pdf->SetXY(50, 15);
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 15, '', 0, 1, 'C');

        // Generar el contenido HTML de la boleta
        $html = '<h1 style="text-align:center; background-color: black; color: white;">' . $cliente->nombre . '</h1>';
        $html .= '<p style="text-align:center; background-color: black; color: white;"><strong>MONTO ABONADO:</strong> ' . $abono->monto . '</p>';
        $html .= '<p style="text-align:center; background-color: black; color: white;"><strong>FECHA DE ABONO:</strong> ' . Carbon::parse($abono->fecha)->format('d-m-Y') . '</p>';
        $html .= '<p style="text-align:center; background-color: black; color: white;"><strong>SALDO RESTANTE:</strong> ' . $saldoRestante . '</p>';
        $html .= '<p style="text-align:center; background-color: black; color: white;"><strong>FECHA DE COMPROBANTE:</strong> ' . now()->format('d-m-Y') . '</p>';

        // Escribir el contenido HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Guardar el PDF en el servidor
        $pdf->Output(public_path('pdf/' . $filename), 'F');

        // Puedes eliminar el archivo después de enviarlo como descarga si es necesario
        // response()->download(public_path('pdf/' . $filename))->deleteFileAfterSend(true);
    }
}
