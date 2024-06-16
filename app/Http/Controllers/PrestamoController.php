<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Cliente;
use TCPDF;
use Illuminate\Support\Facades\View;


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
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'cantidad_prestamo' => 'required|numeric|min:0',
            'fecha' => 'required|date',
        ]);

        Prestamo::create([
            'cliente_id' => $request->cliente_id,
            'cantidad_prestamo' => $request->cantidad_prestamo,
            'fecha' => $request->fecha,
        ]);

        return redirect()->route('prestamos.index')->with('success', 'Préstamo creado exitosamente.');
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
        $pdf->ImageSVG($logo, $x=15, $y=15, $w=30, $h=30);
        $pdf->SetXY(50, 15);
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 15, '', 0, 1, 'C');
    
        // Generar el contenido HTML de la boleta
        $html = '<h1 style="text-align:center">' . $cliente->nombre . '</h1>';
       
        $html .= '<p style="text-align:center"><strong>VENTAS:</strong> ' . $prestamo->cantidad_prestamo . '</p>';
    
        if ($prestamo->abonos->isNotEmpty()) {
            $html .= '<h3 style="text-align:center">ABONOS</h3>';
            $html .= '<table border="1" cellpadding="4" cellspacing="0" align="center">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>FECHA</th>';
            $html .= '<th>MONTO</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            foreach ($prestamo->abonos as $abono) {
                $fechaAbono = Carbon::parse($abono->fecha)->format('d-m-Y');
                $html .= '<tr>';
                $html .= '<td>' . $fechaAbono . '</td>';
                $html .= '<td>' . $abono->monto . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
    
        $html .= '<p style="text-align:center"><strong>ABONO TOTAL:</strong> ' . $prestamo->abonos->sum('monto') . '</p>';
        $html .= '<p style="text-align:center"><strong>SALDO RESTANTE:</strong> ' . $saldoRestante . '</p>';
        $html .= '<p style="text-align:center"><strong>FECHA DE COMPROBANTE:</strong> ' . now()->format('d-m-Y') . '</p>';
    
        // Escribir el contenido HTML
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Guardar el PDF en el servidor
        $pdf->Output(public_path('pdf/' . $filename), 'F');
    
        // Eliminar el archivo después de enviarlo como descarga
        $response = response()->download(public_path('pdf/' . $filename))->deleteFileAfterSend(true);
    
        // Devolver la respuesta de descarga
        return $response;
    }
    


    public function buscar(Request $request)
    {
        $request->validate([
            'cliente' => 'nullable|string',
            'cantidad' => 'nullable|string',
            'fecha' => 'nullable|date',
        ]);

        $prestamos = Prestamo::when($request->filled('cliente'), function ($query) use ($request) {
                        $query->where('cliente', 'like', '%' . $request->input('cliente') . '%');
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

    






    





