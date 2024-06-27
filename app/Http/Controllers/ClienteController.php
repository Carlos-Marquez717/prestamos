<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Services\PDF; // Importa la clase PDF desde el namespace correcto
use Carbon\Carbon;
use TCPDF;


class ClienteController extends Controller
{
    public function index()
    {
        // Obtener todos los clientes junto con sus préstamos y abonos
        $clientes = Cliente::with(['prestamos.abonos'])->get();

        $clientes = Cliente::paginate(5);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clientes,nombre',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|regex:/^\d{11}$/|unique:clientes,telefono',
            'email' => 'required|email|unique:clientes,email',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'El nombre ya está registrado.',
            'direccion.required' => 'La dirección es obligatoria.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe tener 11 dígitos.',
            'telefono.unique' => 'El teléfono ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
        ]);
    
        Cliente::create($request->all());
    
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }
    


    public function showPrestamos(Request $request, Cliente $cliente)
    {
        // Validar el request
        $request->validate([
            'fecha' => 'nullable|date_format:d-m-Y',
            'cantidad' => 'nullable|numeric',
            'estado' => 'nullable|string|in:pagado,proceso',
            'restante' => 'nullable|numeric'
        ]);

        $fecha = $request->input('fecha');
        $cantidad = $request->input('cantidad');
        $estado = $request->input('estado');
        $restante = $request->input('restante');

        $query = $cliente->prestamos()->with('abonos');

        if ($fecha) {
            // Convertir la fecha del request a 'Y-m-d' para la consulta
            try {
                $fecha = Carbon::createFromFormat('d-m-Y', $fecha)->format('Y-m-d');
                $query->whereDate('fecha', $fecha);
            } catch (\Exception $e) {
                // Manejar el error en caso de que la fecha no tenga el formato esperado
            }
        }
        if ($cantidad) {
            $query->where('cantidad_prestamo', $cantidad);
        }
        if ($estado) {
            if ($estado == 'pagado') {
                $query->whereRaw('cantidad_prestamo - (select coalesce(sum(monto), 0) from abonos where prestamo_id = prestamos.id) = 0');
            } elseif ($estado == 'proceso') {
                $query->whereRaw('cantidad_prestamo - (select coalesce(sum(monto), 0) from abonos where prestamo_id = prestamos.id) > 0');
            }
        }
        if ($restante) {
            $query->havingRaw('cantidad_prestamo - sum(abonos.monto) = ?', [$restante]);
        }

        $prestamos = $query->paginate(5);

        // Formatear las fechas de los préstamos en el resultado a 'd-m-Y'
        foreach ($prestamos as $prestamo) {
            $prestamo->fecha = Carbon::parse($prestamo->fecha)->format('d-m-Y');
        }

        return view('clientes.prestamos', compact('cliente', 'prestamos'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'required|email', // Usa 'email'
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }



    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }


    public function generarBoleta(Cliente $cliente)
    {
        try {
            // Crea una instancia de tu clase PDF personalizada
            $pdf = new PDF();
    
            // Lógica para obtener los préstamos y abonos del cliente
            $prestamos = $cliente->prestamos()->with('abonos')->get();
    
            // Genera el contenido HTML para la boleta
            $html = '<h1>HISTORIAL DEL CLIENTE: ' . $cliente->nombre . '</h1>';
    
            foreach ($prestamos as $prestamo) {
                // Calcular el saldo restante por pagar
                $saldoRestante = $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto');
                
                // Determinar el estado del préstamo
                $estadoPrestamo = ($saldoRestante <= 0) ? 'PAGADO' : 'EN PROCESO';
    
                
                $html .= '<p style="text-align:center; background-color: black; color: white;">VENTA: ' . $prestamo->cantidad_prestamo . '</p>';
                $html .= '<p style="text-align:center; background-color: black; color: white;">FECHA VENTA: ' . \Carbon\Carbon::parse($prestamo->fecha)->format('d-m-Y') . '</p>';
                $html .= '<p style="text-align:center; background-color: white; color: black;">ESTADO: ' . $estadoPrestamo . '</p>';
    
                $html .= '<h3 style="text-align:center; background-color: black; color: white;">ABONOS:</h3>';
                $html .= '<table border="1" cellpadding="5" cellspacing="0">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th style="text-align:center; background-color: blue; color: white;">FECHA</th>';
                $html .= '<th style="text-align:center; background-color: blue; color: white;">MONTO</th>';
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
                
                $html .= '<p>Pendiente por Pagar: ' . $saldoRestante . '</p>';
            }
    
            // Agrega el contenido al PDF
            $pdf->addContent($html);
    
            // Descarga el PDF al navegador con un nombre de archivo específico
            $pdf->download('boleta_' . $cliente->id . '.pdf');
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            dd($e->getMessage()); // Por ejemplo, muestra el mensaje de error
        }
    }
    


        public function generarBoleta1(Cliente $cliente)
    {
        // Cargar el historial de préstamos del cliente con sus abonos
        $cliente->load('prestamos.abonos');

        // Generar el contenido HTML de la boleta
        $html = view('pdf.boleta_cliente', compact('cliente'))->render();

        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF();

        // Establecer el formato del documento
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Añadir el logo
        $logo = public_path('images/Banco.svg');
        $pdf->ImageSVG($logo, $x=15, $y=15, $w=30, $h=30);
        $pdf->SetXY(50, 15);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 15, '', 0, 1, 'C');

        // Escribir el contenido HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF generado
        $filename = 'boleta_cliente_' . $cliente->id . '.pdf';

        // Guardar el PDF en el servidor y devolverlo como descarga
        $pdf->Output(public_path('pdf/' . $filename), 'F');
        return response()->download(public_path('pdf/' . $filename))->deleteFileAfterSend(true);
    }


    public function generarBoletaGeneral()
    {
        // Fetch all clients with their loans and abonos
        $clientes = Cliente::with(['prestamos.abonos'])->get();

        // Generate the content for the PDF
        $html = view('pdf.boleta_general', compact('clientes'))->render();

        // Create a new TCPDF instance
        $pdf = new TCPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Company');
        $pdf->SetTitle('Boleta General de Prestamos');
        $pdf->SetSubject('Boleta General de Prestamos');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage();

        // Add the logo
        $logo = public_path('images/Banco.svg');
        $pdf->ImageSVG($logo, $x = 15, $y = 10, $w = 30, $h = 30);

        // Add some space below the logo
        $pdf->SetY(45);

        // Set content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF document
        $filename = 'historial_general_prestamos.pdf';
        $pdf->Output($filename, 'D'); // 'I' for inline display in browser, 'D' for download, 'F' for save on server, 'S' for returning as string

        // Optional: Save the PDF on the server and return it as a download
        // $pdf->Output(public_path('pdf/' . $filename), 'F');
        // return response()->download(public_path('pdf/' . $filename))->deleteFileAfterSend(true);
    }
}
