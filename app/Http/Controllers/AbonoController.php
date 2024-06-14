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
            'fecha' => 'required|date',
        ]);

        // Formatear la fecha en formato 'Y-m-d'
        $fechaFormateada = Carbon::parse($request->fecha)->format('Y-m-d');

        // Almacenamiento del abono
        $abono = Abono::create([
            'prestamo_id' => $prestamo->id,
            'monto' => $request->monto,
            'fecha' => $fechaFormateada, // Guardar la fecha formateada
        ]);

        // Cargar las relaciones necesarias
        $abono->load('prestamo.cliente');

        // Verificar que el cliente y el préstamo estén cargados correctamente
        if (!$abono->prestamo || !$abono->prestamo->cliente) {
            abort(404, 'El préstamo o el cliente asociado no fueron encontrados.');
        }

        // Generar el contenido HTML de la boleta
        $html = view('pdf.boleta', compact('abono'))->render();

        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF();

        // Establecer el formato del documento
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Ajustar la posición del contenido HTML
        $pdf->SetY(15);

        // Añadir logo y título
        $logo = public_path('images/Banco.svg');
        $pdf->ImageSVG($logo, $x=15, $y=15, $w=30, $h=30);
        $pdf->SetXY(50, 15);
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 15, '', 0, 1, 'C');

        // Ajustar la posición del contenido HTML
        $pdf->SetY(15);

        // Escribir el contenido HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF generado
        $filename = 'boleta_' . $abono->id . '.pdf';

        // Guardar el PDF en el servidor
        $pdf->Output(public_path('pdf/' . $filename), 'F');

        // Eliminar el archivo después de enviarlo como descarga
        $response = response()->download(public_path('pdf/' . $filename))->deleteFileAfterSend(true);

        // Devolver la respuesta de descarga
        return $response;
    }
}
