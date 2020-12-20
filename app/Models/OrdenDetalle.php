<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenDetalle extends Model
{
    protected $table = 'ordenDetalle';

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
        return $this->hasOne(OrdenCTP::class, 'idOrdenCTP', 'fk_idOrden');
    }
}
