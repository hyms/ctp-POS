<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierresCajas extends Model
{
    protected $table = 'cajas';

    public function getCajaPadre()
    {
        return $this->hasOne(Cajas::class, 'idCaja', 'fk_idCaja');
    }

    public function getCajas()
    {
        return $this->hasMany(Cajas::class, 'fk_idCaja', 'idCaja');
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
