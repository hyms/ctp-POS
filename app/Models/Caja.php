<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'caja';

    public function getCajaPadre()
    {
        return $this->hasOne(Caja::class, 'idCaja', 'fk_idCaja');
    }

    public function getCajas()
    {
        return $this->hasMany(Caja::class, 'fk_idCaja', 'idCaja');
    }

    public function getSucursal()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idSucursal');
    }

    public function getMovimientoCajas()
    {
        return $this->hasMany(MovimientoCaja::class, 'fk_idCajaOrigen', 'idCaja');
    }
}
