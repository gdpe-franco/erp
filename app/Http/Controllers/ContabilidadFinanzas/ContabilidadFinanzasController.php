<?php

namespace App\Http\Controllers\ContabilidadFinanzas;

use App\Http\Controllers\Controller;
use App\Models\ContabilidadFinanzas\DetalleTransaccion;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class ContabilidadFinanzasController extends Controller
{
    public function getEsquemasMayor ()
    {
        $detalleTransacciones = DetalleTransaccion::select('id', 'cuenta_id', 'transaccion_id', 'monto')->with([
            'cuenta:id,tipo_cuenta_id,nombre' => [
                'tipoCuenta:id,name'
            ],
            'transaccion:id,detalles,fecha'
        ])->get();

        $cuentasTransacciones = $detalleTransacciones->groupBy('cuenta.nombre');
        $cuentas = $cuentasTransacciones->keys();
        $positivosNegativos = collect([]);
        $esquemasMayor = collect([]);


        foreach ($cuentasTransacciones as $transaccion) {
            [$positivos, $negativos] = $transaccion->partition(function ($detalle) {
                return $detalle->monto > 0;
            });

            $sumaPositivos = $positivos->sum('monto');
            $sumaNegativos = $negativos->sum('monto');
            $positivosNegativos->push(['positivos' => $positivos, 'negativos' => $negativos, 
                'totales' => (object)[
                    'total' => $transaccion->sum('monto'),
                    'totalPositivos' => $sumaPositivos,
                    'totalNegativos' => $sumaNegativos
                ], 
            ]);
        }

        $esquemasMayor = $cuentas->combine($positivosNegativos);

        return Inertia::render('ContabilidadFinanzas/EsquemasMayor', [
            'detalleTransacciones' => $esquemasMayor
        ]);
    }
}
