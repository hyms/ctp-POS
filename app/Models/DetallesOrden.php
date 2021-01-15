<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class detallesOrden extends Model
{
    protected $table = 'detallesOrden';

    public function getMovimientoStock()
    {
        return $this->hasOne(MovimientoStock::class, 'idMovimientoStock', 'fk_idMovimientoStock');
    }

    public function getProductoStock()
    {
        return $this->hasOne(ProductoStock::class, 'idProductoStock', 'fk_idProductoStock');
    }

    public function getOrden()
    {
        return $this->hasOne(OrdenesTrabajo::class, 'idOrdenCTP', 'fk_idOrden');
    }
}
