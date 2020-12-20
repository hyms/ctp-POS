<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCTP extends Model
{
    protected $table = 'OrdenCTP';

    public function getCliente()
    {
        return $this->hasOne(Cliente::class, 'idCliente', 'fk_idCliente');
    }

    public function getMovimientoCaja()
    {
        return $this->hasOne(MovimientoCaja::class, 'idMovimientoCaja', 'fk_idMovimientoCaja');
    }

    public function getOrdenPadre()
    {
        return $this->hasOne(OrdenCTP::class, 'idOrdenCTP', 'fk_idParent');
    }

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenCTP::class, 'fk_idParent', 'idOrdenCTP');
    }

    public function getUsuarioDiseño()
    {
        return $this->hasOne(User::class, 'idUser', 'fk_idUserD');
    }

    public function getUsuarioVenta()
    {
        return $this->hasOne(User::class, 'idUser', 'fk_idUserV');
    }

    public function getUsuarioDiseño2()
    {
        return $this->hasOne(User::class, 'idUser', 'fk_idUserD2');
    }

    public function getSucursal()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idSucursal');
    }

    public function getOrdenDetalle()
    {
        return $this->hasMany(OrdenDetalle::class, 'fk_idOrden', 'idOrdenCTP');
    }

    static public function estadoCTP($id = null)
    {
        $estado = [
            '-1' => 'Anulado',
            '0' => 'Cancelado',
            '1' => 'En Proceso',
            '2' => 'Deuda',
        ];
        if (is_null($id))
            return $estado;

        return $estado[$id];
    }
}
