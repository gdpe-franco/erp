<?php

namespace App\Http\Controllers;

use App\Models\ContabilidadFinanzas\Periodo;
use App\Http\Requests\ContabilidadFinanzas\StorePeriodoRequest;
use App\Http\Requests\ContabilidadFinanzas\UpdatePeriodoRequest;
use Carbon\Carbon;
use Error;
use Inertia\Inertia;

class PeriodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('ContabilidadFinanzas/Periodos/Index', [
            'periodos' => Periodo::get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeriodoRequest $request)
    {
        $periodo = $request->validated();
        $periodo['fecha_inicio'] = Carbon::parse($periodo['fecha_inicio'])->toDateString();
        $periodo['fecha_fin'] = Carbon::parse($periodo['fecha_fin'])->toDateString();
        
        try {
            Periodo::create($periodo);
            return back()->with([
                'backgroundNotification' => 'success',
                'titleNotification' => '¡Éxito!',
                'messageNotification' => 'Periodo creado correctamente.',
                'lifeNotification' => 5000
            ]);
        } catch (Error $e) {
            dd($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Periodo $periodo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Periodo $periodo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeriodoRequest $request, Periodo $periodo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Periodo $periodo)
    {
        //
    }
}
