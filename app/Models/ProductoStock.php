<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoStock extends Model
{
    protected $table = 'stock';

    public function getMovimientoStocks()
    {
        return $this->hasMany(MovimientoStock::class, 'fk_idStockDestino', 'idProductoStock');
    }

    public function getOrdenDetalles()
    {
        return $this->hasMany(detallesOrden::class, 'fk_idProductoStock', 'idProductoStock');
    }

    public function getProducto()
    {
        return $this->hasOne(Producto::class, 'idProducto', 'fk_idProducto');
    }

    public function getSucursal()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idSucursal');
    }
}
