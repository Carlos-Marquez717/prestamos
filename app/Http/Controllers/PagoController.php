<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Prestamo;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('prestamo')->get();
        return view('pagos.index', compact('pagos'));
    }

    public function create()
    {
        $prestamos = Prestamo::all();
        return view('pagos.create', compact('prestamos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_prestamo' => 'required',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
        ]);

        $pago = Pago::create($request->all());

        // Actualizar el monto restante del préstamo
        $prestamo = Prestamo::find($request->id_prestamo);
        $prestamo->monto_restante -= $request->monto;
        $prestamo->save();

        return redirect()->route('pagos.index')
            ->with('success', 'Pago registrado exitosamente.');
    }

    // Métodos para show, edit, update, destroy...
}
