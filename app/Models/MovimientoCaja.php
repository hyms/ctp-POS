<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoCaja extends Model
{
    protected $table = 'movimientoCajas';
    public static $tables = 'movimientoCajas';
    protected $guarded = [];
    use SoftDeletes;
    //tipo de movimiento
    // 0 ordenes,
    // 1
    // 2 cajaChica
    // 3
    // 4 Recibos
    public static function getAllOrdenes(array $ordenes)
    {

    }
}
