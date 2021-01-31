<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    protected $table = 'movimientosStock';
    public static $tables = 'movimientosStock';

    public function getProducto()
    {
        return $this->hasOne(Producto::class, 'idProducto', 'fk_idProducto');
    }

    public function getStockOrigen()
    {
        return $this->hasOne(ProductoStock::class, 'idProductoStock', 'fk_idStockOrigen');
    }

    public function getStockDestino()
    {
        return $this->hasOne(ProductoStock::class, 'idProductoStock', 'fk_idStockDestino');
    }

    public function getUsuario()
    {
        return $this->hasOne(User::class, 'idUser', 'fk_idUser');
    }

    public function getOrdenDetalles()
    {
        return $this->hasMany(detallesOrden::class, 'fk_idMovimientoStock', 'idMovimientoStock');
    }
}
