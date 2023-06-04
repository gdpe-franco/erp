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
        Schema::create('detalle_transacciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuenta_id');
            $table->unsignedBigInteger('transaccion_id');
            $table->double('monto', 8, 2);
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);

            $table->foreign('cuenta_id')->references('id')->on('cuentas');
            $table->foreign('transaccion_id')->references('id')->on('transacciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_transacciones');
    }
};
