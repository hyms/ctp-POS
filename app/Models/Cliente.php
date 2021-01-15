<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenesTrabajo::class, 'fk_idCliente', 'idCliente');
    }

    public function getFkIdSucursal()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idSucursal');
    }
}
