<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenesTrabajo::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getCajas()
    {
        return $this->hasMany(Cajas::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getClientes()
    {
        return $this->hasMany(Cliente::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getNotas()
    {
        return $this->hasMany(Notas::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getProductoStocks()
    {
        return $this->hasMany(ProductoStock::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getRecibos()
    {
        return $this->hasMany(Recibo::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getPadre()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idParent');
    }

    public function getSucursals()
    {
        return $this->hasMany(Sucursal::class, 'fk_idParent', 'idSucursal');
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, 'fk_idSucursal', 'idSucursal');
    }
}
