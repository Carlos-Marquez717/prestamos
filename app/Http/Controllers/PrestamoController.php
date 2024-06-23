<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Cliente;
use TCPDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;

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
        ]);
    
        // Generar boleta después de crear el préstamo
        $this->generarBoleta($prestamo);
    
        // Redirigir a la vista show del préstamo recién creado
        return redirect()->route('prestamos.show', $prestamo)->with('success', 'Préstamo creado exitosamente y boleta generada.');
    }
    
        public function generarBoleta(Prestamo $prestamo)
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
        $html = '<h1 style="text-align:center; background-color: black; color: white;">' . $cliente->nombre . '</h1>';
        $html .= '<p style="text-align:center; background-color: black; color: white;"><strong>VENTA:</strong> ' . $prestamo->cantidad_prestamo . '</p>';
        $html .= '<p style="text-align:center; background-color: black; color: white;"><strong>FECHA:</strong> ' . Carbon::parse($prestamo->fecha)->format('d-m-Y') . '</p>';
        $html .= '<p style="text-align:center; background-color: black; color: white;"><strong>FECHA DE COMPROBANTE:</strong> ' . now()->format('d-m-Y') . '</p>';

        // Escribir el contenido HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Descargar el PDF directamente
        return $pdf->Output($filename, 'D');
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
        $html = '<h1 style="text-align:center; background-color: black; color: white;">' . $cliente->nombre . '</h1>';
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
