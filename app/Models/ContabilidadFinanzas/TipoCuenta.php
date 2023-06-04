<?php

namespace App\Models\ContabilidadFinanzas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCuenta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tipo_cuentas';

    protected $fillable = [
        'name',
    ];

    public function cuentas () {
        return $this->hasMany(Cuenta::class, 'tipo_cuenta_id');
    }
}
