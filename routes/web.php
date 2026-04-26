<?php

use App\Http\Controllers\AbonoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reporte', [ReporteController::class, 'generarReporte'])->name('reporte.generar');
    Route::get('/reporte/prestamos/{type}', [ReportController::class, 'generarBoletaPrestamos'])
        ->name('reporte.prestamos.generar');

    Route::get('/clientes/boleta/general', [ClienteController::class, 'generarBoletaGeneral'])
        ->name('clientes.boleta.general');
    Route::get('/clientes/{cliente}/prestamos', [ClienteController::class, 'showPrestamos'])
        ->name('clientes.prestamos');
    Route::get('/clientes/{cliente}/boleta', [ClienteController::class, 'generarBoleta'])
        ->name('clientes.boleta');
    Route::resource('clientes', ClienteController::class);

    Route::get('/prestamos/buscar', [PrestamoController::class, 'buscar'])->name('prestamos.buscar');
    Route::get('/prestamos/{prestamo}/pdf', [PrestamoController::class, 'generarBoleta'])->name('prestamos.pdf');
    Route::resource('prestamos', PrestamoController::class)->except(['edit', 'update', 'destroy']);
    Route::delete('/prestamos/{prestamo}', [PrestamoController::class, 'destroy'])->name('prestamos.destroy');

    Route::get('/prestamos/{prestamo}/abonos/create', [AbonoController::class, 'create'])->name('abonos.create');
    Route::post('/prestamos/{prestamo}/abonos', [AbonoController::class, 'store'])->name('abonos.store');
    Route::get('/abonos/{abono}/pdf', [AbonoController::class, 'generarBoleta'])->name('abonos.pdf');

    Route::get('/boleta/dia', [PrestamoController::class, 'generarBoletaDiaActual'])->name('generar.boleta.dia');
    Route::get('/generar-boleta-semana', [PrestamoController::class, 'generarBoletaSemanaActual'])->name('generar.boleta.semana');
    Route::get('/generar-boleta-mes', [PrestamoController::class, 'generarBoletaMesActual'])->name('generar.boleta.mes');
    Route::get('/generar-boleta-anio', [PrestamoController::class, 'generarBoletaAnioActual'])->name('generar.boleta.anio');
    Route::get('/generar-boleta-todo', [PrestamoController::class, 'generarBoletaTodo'])->name('generar.boleta.todo');
    Route::get('/generar-boleta-abonodia', [PrestamoController::class, 'generarBoletaAbonosDiaActual'])->name('generar.boleta.abonodia');
    Route::get('/generar-boleta-abonosemanal', [PrestamoController::class, 'generarBoletaAbonosSemanaActual'])->name('generar.boleta.abonosemanal');
    Route::get('/generar-boleta-abonomes', [PrestamoController::class, 'generarBoletaAbonosMesActual'])->name('generar.boleta.abonomes');
    Route::get('/generar-boleta-abonoanio', [PrestamoController::class, 'generarBoletaAbonosAnioActual'])->name('generar.boleta.abonoanio');
    Route::get('/generar-boleta-abonototal', [PrestamoController::class, 'generarBoletaTotalAbonado'])->name('generar.boleta.abonototal');
});

require __DIR__ . '/auth.php';
