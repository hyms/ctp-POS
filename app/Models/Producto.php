<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    public function getMovimientoStocks()
    {
        return $this->hasMany(MovimientoStock::class, 'fk_idProducto', 'idProducto');
    }

    public function getProductoStocks()
    {
        return $this->hasMany(ProductoStock::class, 'fk_idProducto', 'idProducto');
    }
}
