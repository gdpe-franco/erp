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

    Route::get('EsquemasMayor/{periodo_id}', [ ContabilidadFinanzasController::class, 'getEsquemasMayor' ])->name('EsquemasMayor.index');
    Route::get('BalanzaComprobacion/{periodo_id}', [ ContabilidadFinanzasController::class, 'getBalanzaComprobacion' ])->name('BalanzaComprobacion.index');
    Route::get('EstadoResultados/{periodo_id}', [ ContabilidadFinanzasController::class, 'getEstadoResultados' ])->name('EstadoResultados.index');
    Route::get('BalanceGeneral/{periodo_id}', [ ContabilidadFinanzasController::class, 'getBalanceGeneral' ])->name('BalanceGeneral.index');
    Route::get('EstadoCostos/{periodo_id}', [ ContabilidadFinanzasController::class, 'getEstadoCostos' ])->name('EstadoCostos.index');
});

Route::middleware('auth:sanctum')->prefix('RecursosHumanos')->name('RecursosHumanos.')->group(function ()
{
    Route::get('Nomina', function(){
        return Inertia::render('RecursosHumanos/Nomina');
    })->name('Nomina.index');
});