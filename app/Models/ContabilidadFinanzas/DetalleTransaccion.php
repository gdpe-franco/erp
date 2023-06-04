<?php

namespace App\Models\ContabilidadFinanzas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleTransaccion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'detalle_transacciones';

    protected $fillable = [
        'cuenta_id',
        'transaccion_id',
        'monto'
    ];

    public function cuenta ()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

    public function transaccion ()
    {
        return $this->belongsTo(Transaccion::class, 'transaccion_id');
    }
}
