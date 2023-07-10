<?php

namespace App\Http\Controllers\ContabilidadFinanzas;

use App\Http\Controllers\Controller;
use App\Models\ContabilidadFinanzas\Cuenta;
use App\Models\ContabilidadFinanzas\DetalleTransaccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ContabilidadFinanzasController extends Controller
{
    protected $cuentasEstadoR, $cuentaUtilidad;

    public function __construct()
    {
        $this->cuentasEstadoR = DB::table('cuentas')
            ->where('nombre', 'Ventas en el país')
            ->orWhere('nombre', 'Costos de ventas')
            ->orWhere('nombre', 'Gastos de operación')
            ->pluck('id');

        $this->cuentaUtilidad = DB::table('cuentas')
            ->where('nombre', 'Utilidad (perdida) del ejercicio')->first();
    }
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

    public function getBalanzaComprobacion () //:JsonResponse
    {
        $detalleTransacciones = DetalleTransaccion::select('id', 'cuenta_id', 'transaccion_id', 'monto')
            ->with([
                'cuenta:id,tipo_cuenta_id,nombre' => [
                    'tipoCuenta:id,name'
                ],
                'transaccion:id,detalles,fecha'
            ])
            ->where('cuenta_id', '!=', $this->cuentaUtilidad->id) //Utilidad perdida del ejercicio
            ->get();

        $cuentasTransacciones = $detalleTransacciones->groupBy('cuenta.nombre');
        $cuentas = $cuentasTransacciones->keys();
        $movimientos = collect([]);

        foreach ($cuentasTransacciones as $transaccion) {
            [$positivos, $negativos] = $transaccion->partition(function ($detalle) {
                return $detalle->monto > 0;
            });

            $sumaPositivos = $positivos->sum('monto');
            $sumaNegativos = $negativos->sum('monto');
            $totalPositivos = 0;
            $totalNegativos = 0;
            $total = $sumaPositivos + $sumaNegativos;
            if($total > 0) {
                $totalPositivos = $total;
            } else {
                $totalNegativos = $total;
            }

            $movimientos->push((object)[
                'sumaPositivos' => $sumaPositivos, 
                'sumaNegativos' => $sumaNegativos,
                'totalPositivos' => $totalPositivos,
                'totalNegativos' => $totalNegativos
            ]);
        }

        $balanza = $cuentas->combine($movimientos);

        //return response()->json($balanza);

        return Inertia::render('ContabilidadFinanzas/BalanzaComprobacion', [
            'balanzas' => $balanza
        ]);
    }

    public function getEstadoResultados () 
    {
        $detalleTransacciones = DetalleTransaccion::select('id', 'cuenta_id', 'transaccion_id', 'monto')->with([
            'cuenta:id,tipo_cuenta_id,nombre' => [
                'tipoCuenta:id,name'
            ],
            'transaccion:id,detalles,fecha'
        ])
        ->whereIn('cuenta_id', $this->cuentasEstadoR) //Ventas, costos de ventas, gastos de operacion
        ->get();

        $estadoResultados = collect([]);
        $sumaVentas = 0;
        $sumaCostoVentas = 0;
        $sumaGastosOperacion = 0;

        foreach($detalleTransacciones as $transaccion){
            if($transaccion['cuenta_id'] == $this->cuentasEstadoR[0]) {
                $sumaVentas += $transaccion['monto'];
            } elseif ($transaccion['cuenta_id'] == $this->cuentasEstadoR[1]) {
                $sumaCostoVentas += $transaccion['monto'];
            } elseif ($transaccion['cuenta_id'] == $this->cuentasEstadoR[2]) {
                $sumaGastosOperacion += $transaccion['monto'];
            } else {
            }
        }

        $utilidadBruta = $sumaVentas + $sumaCostoVentas;
        $utilidadEjercicio = $utilidadBruta + $sumaGastosOperacion;
        
        $transaccionUtilidad = DetalleTransaccion::where('cuenta_id', $this->cuentaUtilidad->id)->first();
        $transaccionUtilidad->monto = $utilidadEjercicio;
        $transaccionUtilidad->save();
        
        $estadoResultados->put('Ventas', $sumaVentas);
        $estadoResultados->put('Costo de Ventas', $sumaCostoVentas);
        $estadoResultados->put('Utilidad bruta', $utilidadBruta);
        $estadoResultados->put('Gastos de operación', $sumaGastosOperacion);
        $estadoResultados->put('Utilidad del ejercicio', $utilidadEjercicio);

        return Inertia::render('ContabilidadFinanzas/EstadoResultados', [
            'estadoResultados' => $estadoResultados
        ]);
    }

    public function getBalanceGeneral ()
    {
        $detalleTransacciones = DetalleTransaccion::whereNotIn('cuenta_id', $this->cuentasEstadoR)->pluck('cuenta_id');
        $cuentasId = $detalleTransacciones->unique()->values();

        $cuentas = Cuenta::with(['detalleTransacciones', 'tipoCuenta:id,name'])
            ->whereIn('id', $cuentasId)->get();
            
        foreach($cuentas as $cuenta){
            $cuenta->total = 0;
            
            foreach($cuenta->detalleTransacciones as $transaccion) {
                $cuenta->total += $transaccion['monto'];
            }
        }

        return Inertia::render('ContabilidadFinanzas/BalanceGeneral', [
            'balanceGeneral' => $cuentas
        ]); 
    }
}
