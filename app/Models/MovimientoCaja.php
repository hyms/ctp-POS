<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoCaja extends Model
{
    protected $table = 'movimientoCajas';
    public static string $tables = 'movimientoCajas';
    protected $guarded = [];
    use SoftDeletes;

    //tipo de movimiento
    // 0 ordenes,
    // 1
    // 2 cajaChica
    // 3
    // 4 Recibos

    public static object $tipoMovimiento=(object)[
        'ordenesVenta'=>0,
        'ordenesDevolucion'=>1,
        'cajaChicaI'=>2,
        'cajaChicaE'=>3,
        'recibos'=>4,
    ];

    public static function getAllOrdenes(array $ordenes)
    {

    }
}
