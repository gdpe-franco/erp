<?php

namespace Database\Seeders;

use App\Models\ContabilidadFinanzas\DetalleTransaccion;
use App\Models\ContabilidadFinanzas\Transaccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContabilidadFinanzasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Transaccion::factory(38)->create(); */
        DetalleTransaccion::factory(38)->create();
    }
}