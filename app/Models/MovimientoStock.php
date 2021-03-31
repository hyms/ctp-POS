<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovimientoStock extends Model
{
    protected $table = 'movimientosStock';
    public static $tables = 'movimientosStock';
    protected $guarded = [];

    public static function gelAll()
    {
        $movimientos = DB::table(self::$tables);
        $movimientos = $movimientos
            ->leftJoin(User::$tables, 'user', '=', User::$tables . '.id')
            ->leftJoin(ProductoStock::$tables . ' as so', 'stockOrigen', '=', 'so.id')
            ->leftJoin(ProductoStock::$tables . ' as sd', 'stockDestino', '=', 'sd.id')
            ->select(self::$tables . '.*', 'so.sucursal as soSucursal', 'sd.sucursal as sdSucursal', User::$tables . '.nombre', User::$tables . '.apellido')
            ->orderBy(self::$tables . '.updated_at', 'DESC');
        return $movimientos->get();
    }
}
