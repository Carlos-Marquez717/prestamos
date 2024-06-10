<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Cliente;

class PrestamoController extends Controller
{
    public function index()
    {
        $prestamos = Prestamo::with('cliente')->get();
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
            'id_cliente' => 'required',
            'monto' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        $prestamo = Prestamo::create($request->all());

        // Calcular sanción (5% del monto)
        $prestamo->sancion = $prestamo->monto * 0.05;
        $prestamo->save();

        return redirect()->route('prestamos.index')
            ->with('success', 'Préstamo creado exitosamente.');
    }

    // Métodos para show, edit, update, destroy...
}
