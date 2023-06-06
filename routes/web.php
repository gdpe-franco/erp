<?php

use App\Http\Controllers\ContabilidadFinanzas\ContabilidadFinanzasController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\DetalleTransaccionController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\TipoCuentaController;
use App\Http\Controllers\TransaccionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

/* Contabilidad Finanzas */
Route::middleware('auth:sanctum')->prefix('ContabilidadFinanzas')->name('ContabilidadFinanzas.')->group(function ()
{
    Route::resource('Transacciones', TransaccionController::class);
    Route::resource('TipoCuentas', TipoCuentaController::class);
    Route::resource('Cuentas', CuentaController::class);
    Route::resource('DetalleTransacciones', DetalleTransaccionController::class);
    Route::resource('Periodos', PeriodoController::class);

    Route::get('EsquemasMayor', [ ContabilidadFinanzasController::class, 'getEsquemasMayor' ])->name('EsquemasMayor.index');
});