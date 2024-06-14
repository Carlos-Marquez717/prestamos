<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Cliente;
use TCPDF;

class PrestamoController extends Controller
{
    public function index()
    {
        $prestamos = Prestamo::all();
        $prestamos = Prestamo::paginate(5);
        return view('prestamos.index', compact('prestamos'));
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
        // Obtener el cliente asociado al préstamo
        $cliente = $prestamo->cliente;

        // Calcular el saldo restante
        $saldoRestante = $prestamo->cantidad_prestamo - $prestamo->abonos()->sum('monto');

        // Definir el nombre del archivo PDF
        $filename = 'BoletaPago_' . $cliente->nombre . '_' . $prestamo->id . '.pdf';

        // Crear instancia de TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Tu Nombre');
        $pdf->SetTitle('Boleta de Pago');
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        // Contenido del PDF
        $html = '<h1>Boleta de Pago</h1>';
        $html .= '<p><strong>Cliente:</strong> ' . $cliente->nombre . '</p>';
        $html .= '<p><strong>Préstamo ID:</strong> ' . $prestamo->id . '</p>';
        $html .= '<p><strong>Abono:</strong> ' . $prestamo->abonos()->sum('monto') . '</p>';
        $html .= '<p><strong>Restante:</strong> ' . $saldoRestante . '</p>';
        $html .= '<p><strong>Fecha de Abono:</strong> ' . date('Y-m-d') . '</p>';

        // Escribir el contenido en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Generar el PDF y enviarlo al navegador
        $pdf->Output($filename, 'D');
        exit;
    }

    public function buscar(Request $request)
    {
        // Obtener los parámetros de búsqueda
        $cliente = $request->input('cliente');
        $cantidad = $request->input('cantidad');
        $fecha = $request->input('fecha');
    
        // Consulta base
        $query = Prestamo::with('cliente');
    
        // Aplicar filtros
        if ($cliente) {
            $query->whereHas('cliente', function ($query) use ($cliente) {
                $query->where('nombre', 'like', '%' . $cliente . '%');
            });
        }
        if ($cantidad) {
            $query->where('cantidad_prestamo', $cantidad);
        }
        if ($fecha) {
            $query->whereDate('fecha', $fecha);
        }
    
        // Obtener los resultados paginados
        $prestamos = $query->paginate(10);
    
        // Devolver la vista con los resultados de la búsqueda
        return view('prestamos.index', compact('prestamos'));
    }
    
}
