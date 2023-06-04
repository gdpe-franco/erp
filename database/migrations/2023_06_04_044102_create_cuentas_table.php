<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_cuenta_id');
            $table->integer('clave');
            $table->string('nombre');
            $table->double('saldo_inicial', 8, 2);
            $table->double('saldo_actual', 8, 2);
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            
            $table->foreign('tipo_cuenta_id')->references('id')->on('tipo_cuentas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
