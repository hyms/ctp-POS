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

    public static function tipoMovimiento(string $tipo)
    {
        $tipoMovimiento = collect([
            'ordenesVenta' => 0,
            'ordenesDevolucion' => 1,
            'cajaChicaIngreso' => 2,
            'cajaChicaEgreso' => 3,
            'recibos' => 4,
        ]);
        return $tipoMovimiento->get($tipo);
    }

    public static function getAllOrdenes(array $ordenes)
    {

    }
}
