<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

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

        public function showPrestamos(Cliente $cliente)
    {   
        // Filtros de búsqueda
        $fecha = request('fecha');
        $cantidad = request('cantidad');
        $nombre = request('nombre');
        $estado = request('estado');
        $restante = request('restante');
        
        // Consulta base de préstamos del cliente
        $query = $cliente->prestamos()->with('abonos');
        
        // Aplicar filtros si están presentes
        if ($fecha) {
            $query->where('fecha', $fecha);
        }
        if ($cantidad) {
            $query->where('cantidad_prestamo', $cantidad);
        }
        if ($nombre) {
            $query->whereHas('cliente', function ($query) use ($nombre) {
                $query->where('nombre', 'like', '%' . $nombre . '%');
            });
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
        
        // Obtener los préstamos paginados
        $prestamos = $query->paginate(5);
        
        // Pasar los datos a la vista
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
}
