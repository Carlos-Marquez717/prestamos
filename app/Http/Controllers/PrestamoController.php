<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Cliente;
use TCPDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;


class PrestamoController extends Controller
{
    public function index()
    {
        $prestamos = Prestamo::all();

        $prestamos->transform(function ($prestamo) {
            $prestamo->formatted_fecha = Carbon::parse($prestamo->fecha)->format('d F Y');
            return $prestamo;
        });

        return view('prestamos.index', compact('prestamos'));
    }

    public function show(Prestamo $prestamo)
    {
        return view('prestamos.show', compact('prestamo'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('prestamos.create', compact('clientes'));
    }



    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'cantidad_prestamo' => 'required|numeric|min:0',
            'fecha' => 'required|date',
        ]);

        // Crear el préstamo
        $prestamo = Prestamo::create([
            'cliente_id' => $request->cliente_id,
            'cantidad_prestamo' => $request->cantidad_prestamo,
            'fecha' => $request->fecha,
            'monto' => $request->monto ?? 0, // Asignar un valor predeterminado si no se proporciona
        ]);

        // Redirigir a la vista show del préstamo
        return redirect()->route('prestamos.show', $prestamo);
    }

    public function showBoleta(Prestamo $prestamo)
    {
        return view('prestamos.boleta', compact('prestamo'));
    }



    public function generarBoleta(Prestamo $prestamo)
    {
        try {
            // Cargar las relaciones necesarias
            $prestamo->load('cliente', 'abonos');
    
            // Verificar que el cliente y el préstamo estén cargados correctamente
            if (!$prestamo->cliente) {
                abort(404, 'El cliente asociado no fue encontrado.');
            }
    
            $cliente = $prestamo->cliente;
            $saldoRestante = $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto');
            $filename = 'Boleta_' . $cliente->nombre . '_' . $prestamo->id . '.pdf';
    
            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Tu Nombre');
            $pdf->SetTitle('BOLETA');
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
    
            // Generar el contenido HTML de la boleta con estilos integrados
            $html = '<h1 style="text-align:center; background-color: black; color: white; border: 1px solid white;">' . $cliente->nombre . '</h1>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>VENTA:</strong> ' . $prestamo->cantidad_prestamo . '</p>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>FECHA:</strong> ' . Carbon::parse($prestamo->fecha)->format('d-m-Y') . '</p>';
    
            // Agregar sección de abonos
            $html .= '<h3 style="text-align:center; background-color: black; color: white;">ABONOS:</h3>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">FECHA</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">MONTO</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
    
            foreach ($prestamo->abonos as $abono) {
                $html .= '<tr>';
                $html .= '<td>' . \Carbon\Carbon::parse($abono->fecha)->format('d-m-Y') . '</td>';
                $html .= '<td>' . $abono->monto . '</td>';
                $html .= '</tr>';
            }
    
            $html .= '</tbody>';
            $html .= '</table>';
    
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;"><strong>FECHA COMPROBANTE:</strong> ' . now()->format('d-m-Y') . ' <strong>HORA:</strong> ' . now()->format('H:i') . '</p>';
    
            // Mostrar saldo restante
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>SALDO RESTANTE:</strong> ' . $saldoRestante . '</p>';
    
            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');
    
            // Calcular la posición Y actual después del contenido
            $currentY = $pdf->GetY();
            $qrY = $currentY + 10;  // Ajusta este valor según la separación deseada
    
            // Generar el código QR con el identificador único (ID del préstamo)
            $qrCode = new EndroidQrCode(route('prestamos.show', $prestamo->id)); // Ruta de ejemplo para mostrar el préstamo
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
    
            // Guardar la imagen temporalmente
            $qrImagePath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
            $result->saveToFile($qrImagePath);
    
            // Agregar el código QR al PDF
            $pdf->Image($qrImagePath, 90, $qrY, 30, 30, 'PNG');
    
            // Agregar el título del QR
            $pdf->SetXY(90, $qrY + 32);  // Ajusta este valor según la posición deseada
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(30, 10, 'Verifica el código QR', 0, 1, 'C');
    
            // Eliminar la imagen temporal
            unlink($qrImagePath);
    
            // Generar el PDF como una respuesta HTTP para descargar
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    
            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();
    
            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    






    public function edit(Prestamo $prestamo)
    {
        // Lógica para mostrar el formulario de edición
    }

    public function destroy(Prestamo $prestamo)
    {
        $prestamo->delete();

        return redirect()->route('prestamos.index')->with('success', 'Préstamo eliminado correctamente.');
    }



    public function generarBoletaYDescargar(Prestamo $prestamo)
    {
        // Cargar las relaciones necesarias
        $prestamo->load('cliente', 'abonos');

        // Verificar que el cliente y el préstamo estén cargados correctamente
        if (!$prestamo->cliente) {
            abort(404, 'El cliente asociado no fue encontrado.');
        }

        $cliente = $prestamo->cliente;
        $saldoRestante = $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto');
        $filename = 'Boleta_' . $cliente->nombre . '_' . $prestamo->id . '.pdf';

        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Tu Nombre');
        $pdf->SetTitle('BOLETA');
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
        $html = '<h1 style="text-align:center; background-color: black; color: white; ">' . $cliente->nombre . '</h1>';
        $html .= '<p style="text-align:center;  background-color: black; color: white;"><strong>VENTA:</strong> ' . $prestamo->cantidad_prestamo . '</p>';
        $html .= '<p style="text-align:center ; background-color: black; color: white;"><strong>FECHA:</strong> ' . Carbon::parse($prestamo->fecha)->format('d-m-Y') . '</p>';



        $html .= '<p style="text-align:center;  background-color: black; color: white;"><strong>FECHA DE COMPROBANTE:</strong> ' . now()->format('d-m-Y') . '</p>';

        // Escribir el contenido HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Descargar el PDF
        $pdf->Output($filename, 'D');
        exit;
    }


    public function buscar(Request $request)
    {
        $request->validate([
            'cliente' => 'nullable|string',
            'cantidad' => 'nullable|string',
            'fecha' => 'nullable|date',
        ]);

        $prestamos = Prestamo::when($request->filled('cliente'), function ($query) use ($request) {
            $query->whereHas('cliente', function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->input('cliente') . '%');
            });
        })
            ->when($request->filled('cantidad'), function ($query) use ($request) {
                $query->where('cantidad_prestamo', 'like', '%' . $request->input('cantidad') . '%');
            })
            ->when($request->filled('fecha'), function ($query) use ($request) {
                $query->whereDate('fecha', $request->input('fecha'));
            })
            ->paginate(10);

        $view = View::make('partials.prestamos_table', compact('prestamos'))->render();

        return response()->json(['html' => $view]);
    }
}
