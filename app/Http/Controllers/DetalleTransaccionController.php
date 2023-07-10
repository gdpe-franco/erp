<?php

namespace App\Http\Controllers;

use App\Models\ContabilidadFinanzas\DetalleTransaccion;
use App\Http\Requests\ContabilidadFinanzas\StoreDetalleTransaccionRequest;
use App\Http\Requests\ContabilidadFinanzas\UpdateDetalleTransaccionRequest;
use App\Models\ContabilidadFinanzas\Cuenta;
use App\Models\ContabilidadFinanzas\Transaccion;
use Carbon\Carbon;
use Error;
use Inertia\Inertia;

class DetalleTransaccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('ContabilidadFinanzas/DetalleTransacciones/Index', [
            'detalleTransacciones' => DetalleTransaccion::with([
                'cuenta' => ['tipoCuenta'],
                'transaccion'
            ])->get(),
            'cuentas' => Cuenta::get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
                
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDetalleTransaccionRequest $request)
    {
        $detallesTransaccion = $request->validated();
        $fechaFormateada =  Carbon::parse($detallesTransaccion['fecha'])->toDateString();

        try {
            $transaccion = Transaccion::create([
                'detalles' => $detallesTransaccion['detalles'],
                'fecha' => $fechaFormateada
            ]);
            
            DetalleTransaccion::create([
                'cuenta_id' => $detallesTransaccion['cuenta']['id'],
                'transaccion_id' => $transaccion['id'],
                'monto' => $detallesTransaccion['monto']
            ]);

            return back()->with(
                [
                    'backgroundNotification' => 'success',
                ]
            );

        } catch (Error $e) {
            dd($e);
        }

        
    }

    /**
     * Display the specified resource.
     */
    public function show(DetalleTransaccion $detalleTransaccion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DetalleTransaccion $detalleTransaccion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDetalleTransaccionRequest $request, DetalleTransaccion $detalleTransaccion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DetalleTransaccion $detalleTransaccion)
    {
        //
    }
}
