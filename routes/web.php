<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\AbonoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::resource('/clientes', ClienteController::class);
Route::get('/prestamos/create', [PrestamoController::class, 'create'])->name('prestamos.create');

Route::get('/clientes/{cliente}/prestamos', [ClienteController::class, 'showPrestamos'])->name('clientes.prestamos');
Route::get('/prestamos/{prestamo}/abonos/create', [AbonoController::class, 'create'])->name('abonos.create');
Route::post('/prestamos', [PrestamoController::class, 'store'])->name('prestamos.store');

Route::post('/prestamos/{prestamo}/abonos', [AbonoController::class, 'store'])->name('abonos.store');
Route::get('/prestamos/{prestamo}/pdf', [PrestamoController::class, 'generarBoleta'])->name('prestamos.pdf');
Route::get('/prestamos/buscar', 'PrestamoController@buscar')->name('prestamos.buscar');
Route::get('/prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
Route::get('/prestamos/{prestamo}', [PrestamoController::class, 'show'])->name('prestamos.show');
Route::get('/prestamos/{prestamo}/edit', 'PrestamoController@edit')->name('prestamos.edit');
Route::delete('/prestamos/{prestamo}', [PrestamoController::class, 'destroy'])->name('prestamos.destroy');


