<?php

namespace App\Http\Controllers\ContabilidadFinanzas;

use App\Http\Controllers\Controller;
use App\Models\ContabilidadFinanzas\Cuenta;
use App\Models\ContabilidadFinanzas\DetalleTransaccion;
use App\Models\ContabilidadFinanzas\Periodo;
use App\Models\ContabilidadFinanzas\Transaccion;
use Carbon\Carbon;
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

    function getTransaccionesPeriodo (string $fecha_inicio, string $fecha_fin) {
        return Transaccion::where(function($query) use ($fecha_inicio, $fecha_fin) {
            return $query
                ->whereDate('fecha', '>=', $fecha_inicio)
                ->whereDate('fecha', '<=', $fecha_fin);
        })
        ->pluck('id');
    }

    public function getEsquemasMayor (int $periodo_id) 
    {
        $periodo = Periodo::find($periodo_id);

        $transaccionesPeriodo = $this->getTransaccionesPeriodo($periodo->fecha_inicio, $periodo->fecha_fin);
        
        $detalleTransacciones = DetalleTransaccion::select('id', 'cuenta_id', 'transaccion_id', 'monto')
            ->with([
                'cuenta:id,tipo_cuenta_id,nombre' => [
                    'tipoCuenta:id,name'
                ],
                'transaccion:id,detalles,fecha'
            ])
            ->whereIn('transaccion_id', $transaccionesPeriodo)
            ->get();

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
            'detalleTransacciones' => $esquemasMayor,
            'periodo' => $periodo
        ]);
    }

    public function getBalanzaComprobacion (int $periodo_id) //:JsonResponse
    {
        $periodo = Periodo::find($periodo_id);

        $transaccionesPeriodo = $this->getTransaccionesPeriodo($periodo->fecha_inicio, $periodo->fecha_fin);

        $detalleTransacciones = DetalleTransaccion::select('id', 'cuenta_id', 'transaccion_id', 'monto')
            ->with([
                'cuenta:id,tipo_cuenta_id,nombre' => [
                    'tipoCuenta:id,name'
                ],
                'transaccion:id,detalles,fecha'
            ])
            ->where('cuenta_id', '!=', $this->cuentaUtilidad->id) //Utilidad perdida del ejercicio
            ->whereIn('transaccion_id', $transaccionesPeriodo)
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
            $totalPositivos = 0; $totalNegativos = 0;
            $total = $sumaPositivos + $sumaNegativos;
            ($total > 0) ? ($totalPositivos = $total) : ($totalNegativos = $total);

            $movimientos->push((object)[
                'sumaPositivos' => $sumaPositivos, 
                'sumaNegativos' => $sumaNegativos,
                'totalPositivos' => $totalPositivos,
                'totalNegativos' => $totalNegativos
            ]);
        }

        $balanza = $cuentas->combine($movimientos);

        return Inertia::render('ContabilidadFinanzas/BalanzaComprobacion', [
            'balanzas' => $balanza,
            'periodo' => $periodo
        ]);
    }

    public function getEstadoResultados (int $periodo_id) 
    {
        $periodo = Periodo::find($periodo_id);

        $transaccionesPeriodo = $this->getTransaccionesPeriodo($periodo->fecha_inicio, $periodo->fecha_fin);

        $detalleTransacciones = DetalleTransaccion::select('id', 'cuenta_id', 'transaccion_id', 'monto')
            ->with([
                'cuenta:id,tipo_cuenta_id,nombre' => [
                    'tipoCuenta:id,name'
                ],
                'transaccion:id,detalles,fecha'
            ])
            ->whereIn('transaccion_id', $transaccionesPeriodo) // La transacción se realizó en el periodo
            ->whereIn('cuenta_id', $this->cuentasEstadoR) // Ventas, costos de ventas, gastos de operacion
            ->get();

        $sumaVentas = 0; $sumaCostoVentas = 0; $sumaGastosOperacion = 0;

        foreach($detalleTransacciones as $transaccion) { /* Falta considerar otras cuentas de ingresos y egresos */
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
        
        $transaccionUtilidad = DetalleTransaccion::where('cuenta_id', $this->cuentaUtilidad->id)
            ->whereIn('transaccion_id', $transaccionesPeriodo)
            ->first();
            /* ->firstOr(function () use ($periodo) {
                $nombrePeriodo = $periodo['nombre'];

                $transaccion = Transaccion::create([
                    'detalles' => `Transacción de Utilidad del ejercicio de {$nombrePeriodo}`,
                    'fecha' => Carbon::now()->format('Y-m-d')
                ]);
                
                DetalleTransaccion::create([
                    'cuenta_id' => $this->cuentaUtilidad->id,
                    'transaccion_id' => $transaccion['id'],
                    'monto' => '0'
                ]);
            }); */
        $transaccionUtilidad->monto = $utilidadEjercicio;
        $transaccionUtilidad->save();
        
        $estadoResultados = collect([
            'Ventas' => $sumaVentas,
            'Costo de Ventas' => $sumaCostoVentas,
            'Utilidad bruta' => $utilidadBruta,
            'Gastos de operación' => $sumaGastosOperacion,
            'Utilidad del ejercicio' => $utilidadEjercicio
        ]);

        return Inertia::render('ContabilidadFinanzas/EstadoResultados', [
            'estadoResultados' => $estadoResultados,
            'periodo' => $periodo
        ]);
    }

    public function getBalanceGeneral (int $periodo_id) 
    {
        $periodo = Periodo::find($periodo_id);

        $transaccionesPeriodo = $this->getTransaccionesPeriodo($periodo->fecha_inicio, $periodo->fecha_fin);
        
        $detalleTransacciones = DetalleTransaccion::whereIn('transaccion_id', $transaccionesPeriodo)
            ->whereNotIn('cuenta_id', $this->cuentasEstadoR)
            ->pluck('cuenta_id');
        $cuentasId = $detalleTransacciones->unique()->values();

        $cuentas = Cuenta::select('id', 'tipo_cuenta_id', 'clave', 'nombre')
            ->whereIn('id', $cuentasId)
            ->with([
                'tipoCuenta:id,name',
                'detalleTransacciones' => function ($query) use ($transaccionesPeriodo) {
                    $query->whereIn('transaccion_id', $transaccionesPeriodo);
                }
            ])
            ->get();

        foreach($cuentas as $cuenta){
            $cuenta->total = 0;
            
            foreach($cuenta->detalleTransacciones as $detalleTransaccion) {
                $cuenta->total += $detalleTransaccion['monto'];
            }
        }

        return Inertia::render('ContabilidadFinanzas/BalanceGeneral', [
            'balanceGeneral' => $cuentas,
            'periodo' => $periodo
        ]); 
    }
}
