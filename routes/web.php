<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\AbonoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ReportController;



// Password reset routes
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware(['auth', 'verified']);

    Route::get('/boleta/prestamos/{type}', [ReportController::class, 'generarBoletaPrestamos'])->name('boleta.prestamos');
    Route::get('/boleta/abonos/{type}', [ReportController::class, 'generarBoletaAbonos'])->name('boleta.abonos');


    Route::get('/reporte', [ReporteController::class, 'generarReporte'])->name('reporte.generar');
    
    Route::resource('clientes', ClienteController::class);
    Route::get('/clientes/{cliente}/prestamos', [ClienteController::class, 'showPrestamos'])->name('clientes.prestamos'); // Ruta correcta

    Route::resource('prestamos', PrestamoController::class)->except(['edit', 'destroy']);
    Route::get('prestamos/{prestamo}/pdf', [PrestamoController::class, 'generarBoleta'])->name('prestamos.pdf');
    
    Route::get('prestamos/create', [PrestamoController::class, 'create'])->name('prestamos.create');
    Route::get('prestamos/{prestamo}/edit', [PrestamoController::class, 'edit'])->name('prestamos.edit');
    Route::delete('prestamos/{prestamo}', [PrestamoController::class, 'destroy'])->name('prestamos.destroy');
    Route::post('prestamos', [PrestamoController::class, 'store'])->name('prestamos.store');
    Route::get('prestamos/{prestamo}', [PrestamoController::class, 'show'])->name('prestamos.show');
    Route::get('/prestamos/{prestamo}/boleta', [PrestamoController::class, 'showBoleta'])->name('prestamos.boleta');

    Route::get('prestamos/{prestamo}/abonos/create', [AbonoController::class, 'create'])->name('abonos.create');
    Route::post('prestamos/{prestamo}/abonos', [AbonoController::class, 'store'])->name('abonos.store');
    Route::get('abonos/{abono}/pdf', [AbonoController::class, 'generarBoleta'])->name('abonos.pdf');
    Route::get('clientes/{cliente}/boleta', [ClienteController::class, 'generarBoleta1'])->name('clientes.boleta');

    Route::get('/clientes/{cliente}/boleta', [ClienteController::class, 'generarBoleta'])->name('clientes.boleta');
    // Elimina la segunda ruta duplicada para generar boleta
    // Route::get('/clientes/{cliente}/boleta', [ClienteController::class, 'generarBoleta1'])->name('clientes.boleta');
    Route::get('/prestamos/{prestamo}/reporte', 'PrestamoController@generarReporte')->name('prestamos.reporte');

    Route::get('/prestamos/buscar', [PrestamoController::class, 'buscar'])->name('prestamos.buscar');
    Route::get('prestamos/{prestamo}', [PrestamoController::class, 'show'])->name('prestamos.show');

    Route::get('/clientes/boleta/general', [ClienteController::class, 'generarBoletaGeneral'])->name('clientes.boleta.general');
    


});

require __DIR__.'/auth.php';
