<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientoCajas';

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenesTrabajo::class, 'fk_idMovimientoCaja', 'idMovimientoCaja');
    }

    public function getCajaDestino()
    {
        return $this->hasOne(Cajas::class, 'idCaja', 'fk_idCajaDestino');
    }

    public function getCajaOrigen()
    {
        return $this->hasOne(Cajas::class, 'idCaja', 'fk_idCajaOrigen');
    }

    public function getCajaPadre()
    {
        return $this->hasOne(MovimientoCaja::class, 'idMovimientoCaja', 'idParent');
    }

    public function getMovimientoCajas()
    {
        return $this->hasMany(MovimientoCaja::class, 'idParent', 'idMovimientoCaja');
    }

    public function getUsuario()
    {
        return $this->hasOne(User::class, 'idUser', 'fk_idUser');
    }

    public function getRecibos()
    {
        return $this->hasMany(Recibo::class, 'fk_idMovimientoCaja', 'idMovimientoCaja');
    }
}
