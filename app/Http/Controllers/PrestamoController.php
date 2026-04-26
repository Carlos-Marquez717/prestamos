<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Abono;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Cliente;
use TCPDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Http\Controllers\PDF;


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
            'amount' => $request->cantidad_prestamo,
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
            $pdf->SetAuthor('Sistema Prestamos');
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
            report($e);
            abort(500, 'No se pudo generar el documento.');
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
        $pdf->SetAuthor('Sistema Prestamos');
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

    // PrestamoController.php
    public function prestamosPorDia()
    {
        $prestamosPorDia = Prestamo::whereDate('created_at', Carbon::today())->get();
        return response()->json(['prestamosPorDia' => $prestamosPorDia]);
    }

    public function generarBoletaDiaActual()
    {
        try {
            // Obtener préstamos del día actual
            $prestamos = Prestamo::whereDate('fecha', now()->toDateString())->with('cliente')->get();

            if ($prestamos->isEmpty()) {
                abort(404, 'No hay préstamos para el día actual.');
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DEL DÍA');
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

            // Generar el contenido HTML para cada préstamo
            $totalPrestamos = 0;
            $html = '<h2 style="text-align:center; background-color: black; color: white;">PRESTAMOS DEL DIA</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Cliente</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Monto</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Fecha</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($prestamos as $prestamo) {
                $cliente = $prestamo->cliente;
                $totalPrestamos += $prestamo->cantidad_prestamo;

                $html .= '<tr>';
                $html .= '<td style="text-align:center;">' . $cliente->nombre . '</td>';
                $html .= '<td style="text-align:center;">' . number_format($prestamo->cantidad_prestamo, 2) . '</td>';
                $html .= '<td style="text-align:center;">' . Carbon::parse($prestamo->fecha)->format('d-m-Y') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de préstamos
            $html .= '<h3 style="text-align:center; background-color: black; color: white;">Total Préstamos: ' . number_format($totalPrestamos, 2) . '</h3>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Dia_' . now()->format('d-m-Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }

    public function generarBoletaMesActual()
    {
        try {
            // Obtener préstamos del mes actual
            $prestamos = Prestamo::whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->get();

            if ($prestamos->isEmpty()) {
                abort(404, 'No hay préstamos para el mes actual.');
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DEL MES');
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

            // Inicializar variables para el total
            $totalPrestamos = 0;

            // Generar el contenido HTML para cada préstamo
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">PRESTAMOS DEL MES</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Cliente</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Monto</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Fecha</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($prestamos as $prestamo) {
                $prestamo->load('cliente', 'abonos');
                $cliente = $prestamo->cliente;
                $saldoRestante = $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto');

                $html .= '<tr>';
                $html .= '<td>' . $cliente->nombre . '</td>';
                $html .= '<td>' . number_format($prestamo->cantidad_prestamo, 2) . '</td>';
                $html .= '<td>' . \Carbon\Carbon::parse($prestamo->fecha)->format('d-m-Y') . '</td>';
                $html .= '</tr>';

                // Sumar al total
                $totalPrestamos += $prestamo->cantidad_prestamo;
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de todos los préstamos del mes
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL PRESTADO DEL MES:</strong> ' . number_format($totalPrestamos, 2) . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Mes_' . now()->format('m-Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }

    public function generarBoletaSemanaActual()
    {
        try {
            // Obtener préstamos de la semana actual
            $inicioSemana = now()->startOfWeek()->toDateString();
            $finSemana = now()->endOfWeek()->toDateString();

            $prestamos = Prestamo::whereBetween('fecha', [$inicioSemana, $finSemana])->get();

            if ($prestamos->isEmpty()) {
                abort(404, 'No hay préstamos para la semana actual.');
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DE LA SEMANA');
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

            // Inicializar variables para el total
            $totalPrestamos = 0;

            // Generar el contenido HTML para cada préstamo
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">PRESTAMOS DE LA SEMANA</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Cliente</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Monto</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Fecha</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($prestamos as $prestamo) {
                $prestamo->load('cliente', 'abonos');
                $cliente = $prestamo->cliente;
                $saldoRestante = $prestamo->cantidad_prestamo - $prestamo->abonos->sum('monto');

                $html .= '<tr>';
                $html .= '<td>' . $cliente->nombre . '</td>';
                $html .= '<td>' . number_format($prestamo->cantidad_prestamo, 2) . '</td>';
                $html .= '<td>' . \Carbon\Carbon::parse($prestamo->fecha)->format('d-m-Y') . '</td>';
                $html .= '</tr>';

                // Sumar al total
                $totalPrestamos += $prestamo->cantidad_prestamo;
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de todos los préstamos de la semana
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL PRESTADO DE LA SEMANA:</strong> ' . number_format($totalPrestamos, 2) . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Semana_' . now()->format('W_Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }

    public function generarBoletaAnioActual()
    {
        try {
            // Obtener préstamos del año actual
            $inicioAnio = now()->startOfYear()->toDateString();
            $finAnio = now()->endOfYear()->toDateString();

            $prestamos = Prestamo::whereBetween('fecha', [$inicioAnio, $finAnio])->get();

            if ($prestamos->isEmpty()) {
                abort(404, 'No hay préstamos para el año actual.');
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DEL AÑO');
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

            // Inicializar variables para los totales
            $totalPrestamosPorMes = array_fill(1, 12, 0); // Inicializa un array para los totales de cada mes
            $totalAnual = 0; // Total de todos los préstamos del año
            $fechaHoraActual = now()->format('d-m-Y H:i');

            // Calcular el total de préstamos por mes
            foreach ($prestamos as $prestamo) {
                $mes = \Carbon\Carbon::parse($prestamo->fecha)->month;
                $totalPrestamosPorMes[$mes] += $prestamo->cantidad_prestamo;
            }

            // Calcular el total anual sumando los totales mensuales
            $totalAnual = array_sum($totalPrestamosPorMes);

            // Generar el contenido HTML para los totales de cada mes
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">RESUMEN DEL AÑO</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Mes</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Total</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Meses del año
            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];

            foreach ($meses as $mesNumero => $mesNombre) {
                $html .= '<tr>';
                $html .= '<td>' . $mesNombre . '</td>';
                $html .= '<td>' . number_format($totalPrestamosPorMes[$mesNumero], 2) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de todos los préstamos del año
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL DEL AÑO:</strong> ' . number_format($totalAnual, 2) . '</p>';

            // Mostrar la fecha y hora de generación
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>FECHA Y HORA:</strong> ' . $fechaHoraActual . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Anio_' . now()->format('Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }



    public function generarBoletaTodo()
    {
        try {
            // Obtener todos los préstamos ordenados por fecha
            $prestamos = Prestamo::orderBy('fecha')->get();

            if ($prestamos->isEmpty()) {
                abort(404, 'No hay préstamos registrados.');
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA TOTAL DE PRÉSTAMOS');
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

            // Inicializar variables para el total
            $totalPrestamos = $prestamos->sum('cantidad_prestamo');
            $fechaHoraActual = now()->format('d-m-Y H:i');

            // Generar el contenido HTML para el total y la fecha/hora
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">PRESTAMOS TOTAL</h2>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL PRESTADO:</strong> ' . number_format($totalPrestamos, 2) . '</p>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>FECHA:</strong> ' . $fechaHoraActual . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boleta_Total_Prestamos_' . now()->format('d-m-Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }

    public function generarBoletaAbonosDiaActual()
    {
        try {
            // Obtener la fecha actual
            $fechaActual = now()->toDateString();

            // Obtener abonos del día actual
            $abonos = Abono::whereDate('fecha', $fechaActual)->get();

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DE ABONOS DEL DÍA');
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

            // Inicializar variables para el total
            $totalAbonos = 0;
            $fechaHoraActual = now()->format('d-m-Y H:i');

            // Generar el contenido HTML para cada abono
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">ABONOS DEL DIA</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Cliente</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Monto</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Fecha</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Verificar si hay abonos para el día actual
            if ($abonos->isEmpty()) {
                $html .= '<tr>';
                $html .= '<td colspan="3" style="text-align:center;">No hay abonos para el día actual.</td>';
                $html .= '</tr>';
            } else {
                foreach ($abonos as $abono) {
                    $abono->load('prestamo.cliente');
                    $cliente = $abono->prestamo->cliente;

                    $html .= '<tr>';
                    $html .= '<td>' . $cliente->nombre . '</td>';
                    $html .= '<td>' . number_format($abono->monto, 2) . '</td>';
                    $html .= '<td>' . \Carbon\Carbon::parse($abono->fecha)->format('d-m-Y') . '</td>';
                    $html .= '</tr>';

                    // Sumar al total
                    $totalAbonos += $abono->monto;
                }
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de todos los abonos del día
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL ABONADO DEL DIA:</strong> ' . number_format($totalAbonos, 2) . '</p>';

            // Mostrar la fecha y hora de generación
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>FECHA Y HORA DE GENERACIÓN:</strong> ' . $fechaHoraActual . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Abonos_Dia_' . now()->format('d-m-Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }

    public function generarBoletaAbonosSemanaActual()
    {
        try {
            // Obtener el primer y último día de la semana actual
            $inicioSemana = now()->startOfWeek()->toDateString();
            $finSemana = now()->endOfWeek()->toDateString();

            // Obtener abonos de la semana actual
            $abonos = Abono::whereBetween('fecha', [$inicioSemana, $finSemana])->get();

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DE ABONOS DE LA SEMANA');
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

            // Inicializar variables para el total
            $totalAbonos = 0;
            $fechaHoraActual = now()->format('d-m-Y H:i');

            // Generar el contenido HTML para cada abono
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">ABONOS DE LA SEMANA</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Cliente</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Monto</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Fecha</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Verificar si hay abonos para la semana actual
            if ($abonos->isEmpty()) {
                $html .= '<tr>';
                $html .= '<td colspan="3" style="text-align:center;">No hay abonos para la semana actual.</td>';
                $html .= '</tr>';
            } else {
                foreach ($abonos as $abono) {
                    $abono->load('prestamo.cliente');
                    $cliente = $abono->prestamo->cliente;

                    $html .= '<tr>';
                    $html .= '<td>' . $cliente->nombre . '</td>';
                    $html .= '<td>' . number_format($abono->monto, 2) . '</td>';
                    $html .= '<td>' . \Carbon\Carbon::parse($abono->fecha)->format('d-m-Y') . '</td>';
                    $html .= '</tr>';

                    // Sumar al total
                    $totalAbonos += $abono->monto;
                }
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de todos los abonos de la semana
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL ABONADO DE LA SEMANA:</strong> ' . number_format($totalAbonos, 2) . '</p>';

            // Mostrar la fecha y hora de generación
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>FECHA Y HORA DE GENERACIÓN:</strong> ' . $fechaHoraActual . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Abonos_Semana_' . now()->format('W-Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }


    public function generarBoletaAbonosMesActual()
    {
        try {
            // Obtener el primer y último día del mes actual
            $inicioMes = now()->startOfMonth()->toDateString();
            $finMes = now()->endOfMonth()->toDateString();

            // Obtener abonos del mes actual
            $abonos = Abono::whereBetween('fecha', [$inicioMes, $finMes])->get();

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DE ABONOS DEL MES');
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

            // Inicializar variables para el total
            $totalAbonos = 0;
            $fechaHoraActual = now()->format('d-m-Y H:i');

            // Generar el contenido HTML para cada abono
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">ABONOS DEL MES</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Cliente</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Monto</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Fecha</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Verificar si hay abonos para el mes actual
            if ($abonos->isEmpty()) {
                $html .= '<tr>';
                $html .= '<td colspan="3" style="text-align:center;">No hay abonos para el mes actual.</td>';
                $html .= '</tr>';
            } else {
                foreach ($abonos as $abono) {
                    $abono->load('prestamo.cliente');
                    $cliente = $abono->prestamo->cliente;

                    $html .= '<tr>';
                    $html .= '<td>' . $cliente->nombre . '</td>';
                    $html .= '<td>' . number_format($abono->monto, 2) . '</td>';
                    $html .= '<td>' . \Carbon\Carbon::parse($abono->fecha)->format('d-m-Y') . '</td>';
                    $html .= '</tr>';

                    // Sumar al total
                    $totalAbonos += $abono->monto;
                }
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de todos los abonos del mes
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL ABONADO DEL MES:</strong> ' . number_format($totalAbonos, 2) . '</p>';

            // Mostrar la fecha y hora de generación
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>FECHA Y HORA DE GENERACIÓN:</strong> ' . $fechaHoraActual . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Abonos_Mes_' . now()->format('m-Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }

    public function generarBoletaAbonosAnioActual()
    {
        try {
            // Obtener abonos del año actual
            $inicioAnio = now()->startOfYear()->toDateString();
            $finAnio = now()->endOfYear()->toDateString();

            $abonos = Abono::whereBetween('fecha', [$inicioAnio, $finAnio])->get();

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DE ABONOS DEL AÑO');
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

            // Inicializar variables para los totales
            $totalesMensuales = array_fill(1, 12, 0);
            $totalAnual = 0;
            $fechaHoraActual = now()->format('d-m-Y H:i');

            // Nombres de los meses en español
            $mesesEspanol = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            ];

            // Generar el contenido HTML para los abonos mensuales
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">ABONOS DEL AÑO</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Mes</th>';
            $html .= '<th style="text-align:center; background-color: black; color: white;">Total Abonado</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Verificar si hay abonos para el año actual
            if ($abonos->isEmpty()) {
                $html .= '<tr>';
                $html .= '<td colspan="2" style="text-align:center;">No hay abonos para el año actual.</td>';
                $html .= '</tr>';
            } else {
                foreach ($abonos as $abono) {
                    $mes = \Carbon\Carbon::parse($abono->fecha)->month;
                    $totalesMensuales[$mes] += $abono->monto;
                }

                for ($i = 1; $i <= 12; $i++) {
                    $html .= '<tr>';
                    $html .= '<td>' . $mesesEspanol[$i] . '</td>';
                    $html .= '<td>' . number_format($totalesMensuales[$i], 2) . '</td>';
                    $html .= '</tr>';
                    $totalAnual += $totalesMensuales[$i];
                }
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Mostrar el total de todos los abonos del año
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL ABONADO DEL AÑO:</strong> ' . number_format($totalAnual, 2) . '</p>';

            // Mostrar la fecha y hora de generación
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>FECHA Y HORA:</strong> ' . $fechaHoraActual . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boletas_Abonos_Anio_' . now()->format('Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }


    public function generarBoletaTotalAbonado()
    {
        try {
            // Obtener todos los abonos
            $abonos = Abono::all();

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema Prestamos');
            $pdf->SetTitle('BOLETA DE TOTAL ABONADO');
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

            // Inicializar variables para el total
            $totalAbonado = 0;
            $fechaHoraActual = now()->format('d-m-Y H:i');

            // Calcular el total abonado
            foreach ($abonos as $abono) {
                $totalAbonado += $abono->monto;
            }

            // Generar el contenido HTML
            $html = '<h2 style="text-align:center; background-color: black; color: white; border: 1px solid white;">TOTAL ABONADO</h2>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>TOTAL ABONADO:</strong> ' . number_format($totalAbonado, 2) . '</p>';
            $html .= '<p style="text-align:center; background-color: black; color: white; border: 1px solid white;">';
            $html .= '<strong>FECHA Y HORA:</strong> ' . $fechaHoraActual . '</p>';

            // Escribir el contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Generar el PDF como una respuesta HTTP para descargar
            $filename = 'Boleta_Total_Abonado_' . now()->format('Y') . '.pdf';
            $response = Response::make($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

            // Importante: Limpiar el buffer de salida para asegurar que no se mezcle con otros datos de salida
            ob_clean();

            // Devolver la respuesta HTTP que contiene el PDF
            return $response;
        } catch (\Exception $e) {
            report($e);
            abort(500, 'No se pudo generar el documento.');
        }
    }
}
