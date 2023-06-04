<?php

namespace App\Models\ContabilidadFinanzas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuenta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cuentas';

    protected $fillable = [
        'tipo_cuenta_id',
        'clave',
        'nombre',
        'saldo_inicial',
        'saldo_actual'
    ];

    public function tipoCuenta () 
    {
        return $this->belongsTo(TipoCuenta::class, 'tipo_cuenta_id');
    }

    public function detalleTransacciones ()
    {
        return $this->hasMany(DetalleTransaccion::class, 'cuenta_id');
    }
}
