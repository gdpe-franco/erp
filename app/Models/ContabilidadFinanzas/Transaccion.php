<?php

namespace App\Models\ContabilidadFinanzas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaccion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transacciones';

    protected $fillable = [
        'detalles',
        'fecha'
    ];

    public function detalleTransacciones ()
    {
        return $this->hasMany(DetalleTransaccion::class, 'transaccion_id');
    }
}
