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
    protected $cuentasEstadoR, $cuentaUtilidad, $cuentaImpuestosDerechos;

    public function __construct()
    {
        $this->cuentasEstadoR = DB::table('cuentas')
            ->where('nombre', 'Ventas en el país')
            ->orWhere('nombre', 'Costos de ventas')
            ->orWhere('nombre', 'Gastos de operación')
            ->orWhere('nombre', 'Gastos de ventas')
            ->orWhere('nombre', 'Gastos de administración')
            ->pluck('id');

        $this->cuentaUtilidad = DB::table('cuentas')
            ->where('nombre', 'Utilidad (perdida) del ejercicio')->first();
        $this->cuentaImpuestosDerechos = DB::table('cuentas')
            ->where('nombre', 'Impuestos y derechos por pagar')->first();
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

    public function getEstadoCostos (int $periodo_id)
    {
        $periodo = Periodo::find($periodo_id);
        $cuentasProduccionVentas = array(6, 7, 8, 96);
        $estadoCostos = (object)[
            'Inventario inicial de materias primas' => 0,
            'Costo de materias primas recibidas' => 0,
            'Total positivo de materias primas' => 0,
            'Inventario final de materias' => 0,
            'Total de materias primas utilizadas' => 0,
            'Costo de materias primas indirectas utilizadas' => 0,
            'Costo de materias primas directas' => 0,
            'Mano de obra directa utilizada' => 0,
            'COSTO PRIMO' => 0,
            'Cargos indirectos' => 0,
            'Costo de producción procesada' => 0,
            'Inventario inicial de producción en proceso' => 0,
            'Producción en proceso disponible' => 0,
            'Inventario final de producción en proceso' => 0,
            'Costo de producción terminada' => 0,
            'Inventario inicial de producto terminado' => 0,
            'Productos terminados en disponibilidad' => 0,
            'Inventario final de producto terminado' => 0,
            'COSTO DE PRODUCTOS VENDIDOS' => 0
        ];
        $transaccionesPeriodo = $this->getTransaccionesPeriodo($periodo->fecha_inicio, $periodo->fecha_fin);
        $detalleTransacciones = DetalleTransaccion::select('id', 'cuenta_id', 'transaccion_id', 'monto')
            ->with([
                'cuenta:id,tipo_cuenta_id,nombre' => [
                    'tipoCuenta:id,name'
                ],
                'transaccion:id,detalles,fecha'
            ])
            ->whereIn('transaccion_id', $transaccionesPeriodo)
            ->whereIn('cuenta_id',  $cuentasProduccionVentas) 
            ->get();

        $cuentasTotales = collect([]);
        $cuentasTransacciones = $detalleTransacciones->groupBy('cuenta.id');
        foreach ($cuentasTransacciones as $transaccion) {
            [$positivos, $negativos] = $transaccion->partition(function ($detalle) {
                return $detalle->monto > 0;
            });

            $sumaPositivos = $positivos->sum('monto');
            $sumaNegativos = $negativos->sum('monto');
            $cuentasTotales->push([
                'totales' => (object)[
                    'total' => $transaccion->sum('monto'),
                    'totalPositivos' => $sumaPositivos,
                    'totalNegativos' => $sumaNegativos
                ], 
                'cuenta_id' => $transaccion[0]['cuenta_id']
            ]);
        }

        foreach($detalleTransacciones as $detalleTransaccion) {
            if($detalleTransaccion['cuenta_id'] == 6){
                if($detalleTransaccion['transaccion']['detalles'] == 'Saldo inicial') {
                    $estadoCostos->{'Inventario inicial de materias primas'} = $detalleTransaccion['monto'];
                } elseif($detalleTransaccion['monto'] > 0) {
                    $estadoCostos->{'Costo de materias primas recibidas'} += $detalleTransaccion['monto'];
                }
            } elseif($detalleTransaccion['cuenta_id'] == 96) {
                if($detalleTransaccion['monto'] > 0) {
                    if(str_contains($detalleTransaccion['transaccion']['detalles'], 'materia prima')) {
                        $estadoCostos->{'Costo de materias primas indirectas utilizadas'} += $detalleTransaccion['monto'];
                    }
                }
            } elseif($detalleTransaccion['cuenta_id'] == 7) {
                if($detalleTransaccion['transaccion']['detalles'] == 'Saldo inicial'){
                    $estadoCostos->{'Inventario inicial de producción en proceso'} = $detalleTransaccion['monto'];
                } elseif($detalleTransaccion['monto'] > 0) {
                    if(str_contains($detalleTransaccion['transaccion']['detalles'], 'materia prima')) {
                        $estadoCostos->{'Costo de materias primas directas'} += $detalleTransaccion['monto'];
                    } else if(str_contains($detalleTransaccion['transaccion']['detalles'], 'mano de obra')) {
                        $estadoCostos->{'Mano de obra directa utilizada'} += $detalleTransaccion['monto'];
                    }
                }
            } elseif($detalleTransaccion['cuenta_id'] == 8) {
                if($detalleTransaccion['transaccion']['detalles'] == 'Saldo inicial'){
                    $estadoCostos->{'Inventario inicial de producto terminado'} = $detalleTransaccion['monto'];
                }
            }
        }

        foreach($cuentasTotales as $cuenta) {
            if($cuenta['cuenta_id'] == 6){
                $estadoCostos->{'Total positivo de materias primas'} = $cuenta['totales']->{'totalPositivos'};
                $estadoCostos->{'Inventario final de materias'} = $cuenta['totales']->{'total'};
                $estadoCostos->{'Total de materias primas utilizadas'} = $cuenta['totales']->{'totalNegativos'};
            } elseif($cuenta['cuenta_id'] == 7){
                $estadoCostos->{'Inventario final de producción en proceso'} = $cuenta['totales']->{'total'};
                $estadoCostos->{'Producción en proceso disponible'} = $cuenta['totales']->{'totalPositivos'};
                $estadoCostos->{'Costo de producción terminada'} = $cuenta['totales']->{'totalNegativos'};
            } elseif ($cuenta['cuenta_id'] == 8) {
                $estadoCostos->{'Productos terminados en disponibilidad'} = $cuenta['totales']->{'totalPositivos'};
                $estadoCostos->{'Inventario final de producto terminado'} = $cuenta['totales']->{'total'};
            } elseif($cuenta['cuenta_id'] == 96) {
                $estadoCostos->{'Cargos indirectos'} = $cuenta['totales']->{'totalPositivos'};
            }
            
        }
        $estadoCostos->{'COSTO PRIMO'} = $estadoCostos->{'Costo de materias primas directas'} + $estadoCostos->{'Mano de obra directa utilizada'};
        $estadoCostos->{'Costo de producción procesada'} = $estadoCostos->{'COSTO PRIMO'} + $estadoCostos->{'Cargos indirectos'};
        $estadoCostos->{'COSTO DE PRODUCTOS VENDIDOS'} = $estadoCostos->{'Productos terminados en disponibilidad'} - $estadoCostos->{'Inventario final de producto terminado'};
        
        return Inertia::render('ContabilidadFinanzas/EstadoResultados', [
            'estadoResultados' => $estadoCostos,
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

        $sumaVentas = 0; $sumaCostoVentas = 0; $sumaGastos = 0;

        foreach($detalleTransacciones as $transaccion) { /* Falta considerar otras cuentas de ingresos y egresos */
            if($transaccion['cuenta_id'] == $this->cuentasEstadoR[0]) {
                $sumaVentas += $transaccion['monto'];
            } elseif ($transaccion['cuenta_id'] == $this->cuentasEstadoR[3]) {
                $sumaCostoVentas += $transaccion['monto'];
            } elseif ($transaccion['cuenta_id'] == $this->cuentasEstadoR[1] ||
                $transaccion['cuenta_id'] == $this->cuentasEstadoR[2] || 
                $transaccion['cuenta_id'] == $this->cuentasEstadoR[4]) {
                $sumaGastos += $transaccion['monto'];
            } else {
            }
        }

        $utilidadBruta = $sumaVentas + $sumaCostoVentas;
        $utilidadOperacion = $utilidadBruta + $sumaGastos;
        $otrosIngresos = 0;
        $otrosGastos = 0;
        $utilidadBImpuestos = $utilidadOperacion + $otrosIngresos - $otrosGastos;
        $ptu = $utilidadBImpuestos * 0.1;
        $isr = $utilidadBImpuestos * 0.3;
        $utilidadEjercicio = $utilidadBImpuestos - $ptu - $isr;
        
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
        
        $transaccionImpuestosDerechos = DetalleTransaccion::where('cuenta_id', $this->cuentaImpuestosDerechos->id)
            ->whereIn('transaccion_id', $transaccionesPeriodo)
            ->first();
        $transaccionImpuestosDerechos->monto = $ptu + $isr;
        $transaccionImpuestosDerechos->save();

        $estadoResultados = collect([
            'Ventas' => $sumaVentas,
            'Costo de Ventas' => $sumaCostoVentas,
            'Utilidad bruta' => $utilidadBruta,
            'Gastos de operación' => $sumaGastos,
            'Utilidad de operacion' => $utilidadOperacion,
            'Otros ingresos' => $otrosIngresos,
            'Otros gastos' => $otrosGastos,
            'Utilidad antes de impuestos' => $utilidadBImpuestos,
            'PTU' => $ptu,
            'IRS' => $isr,
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
