<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Services\PDF; // Importa la clase PDF desde el namespace correcto


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
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'required|email', // Usa 'email'
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function showPrestamos(Request $request, Cliente $cliente)
    {
        $fecha = $request->input('fecha');
        $cantidad = $request->input('cantidad');
        $estado = $request->input('estado');
        $restante = $request->input('restante');
        
        $query = $cliente->prestamos()->with('abonos');
        
        if ($fecha) {
            $query->where('fecha', $fecha);
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


        public function generarBoleta(Cliente $cliente)
    {
        try {
            // Crea una instancia de tu clase PDF personalizada
            $pdf = new PDF();

            // Lógica para generar la boleta específica del cliente
            $html = '<h1>Boleta para Cliente: ' . $cliente->nombre . '</h1>';
            // Añade más contenido según tu lógica específica para la boleta del cliente

            // Agrega el contenido al PDF
            $pdf->addContent($html);

            // Descarga el PDF al navegador con un nombre de archivo específico
            $pdf->download('boleta_' . $cliente->id . '.pdf');
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            dd($e->getMessage()); // Por ejemplo, muestra el mensaje de error
        }
    }

    public function generarBoletaGeneral()
    {
        try {
            // Crea una instancia de tu clase PDF personalizada
            $pdf = new PDF();

            // Lógica para generar la boleta general
            $html = '<h1>Boleta General</h1>';
            // Puedes agregar más contenido aquí según tu lógica para la boleta general

            // Agrega el contenido al PDF
            $pdf->addContent($html);

            // Descarga el PDF al navegador con un nombre de archivo específico
            $pdf->download('boleta_general.pdf');
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            dd($e->getMessage()); // Por ejemplo, muestra el mensaje de error
        }
    }


}
