<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo; // Import the Prestamo model
use App\Models\Cliente;  // Import the Cliente model

class PrestamoController extends Controller
{
    public function index()
    {
        $prestamos = Prestamo::all();
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
            'nombre_cliente' => 'required|exists:clientes,nombre',
            'cantidad_prestamo' => 'required|numeric|min:0',
            'fecha' => 'required|date',
        ]);

        // Buscar el cliente por nombre
        $cliente = Cliente::where('nombre', $request->nombre_cliente)->firstOrFail();

        Prestamo::create([
            'cliente_id' => $cliente->id, // Asignar el ID del cliente
            'cantidad_prestamo' => $request->cantidad_prestamo, // Asegúrate de que los nombres de campo coincidan con tu tabla
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
}
