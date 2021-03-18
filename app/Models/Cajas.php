<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class Cajas extends Model
{
    protected $table = 'cajas';
    protected static $tables = 'cajas';
    protected $guarded = [];

    public static function getAll(int $sucursal = null, int $caja_padre = null)
    {
        $sucursales = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $sucursales = $sucursales->where('enable', '=', '1');
            $sucursales = $sucursales->where('sucursal', '=', $sucursal);
        }
        if (!empty($caja_padre)) {
            $sucursales = $sucursales->where('dependeDe', '=', $caja_padre);
        }
        $sucursales = $sucursales
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        return $sucursales->get();
    }
}
