<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenCTP::class, 'fk_idCliente', 'idCliente');
    }

    public function getFkIdSucursal()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idSucursal');
    }
}
