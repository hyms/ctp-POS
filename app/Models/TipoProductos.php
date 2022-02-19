<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoProductos extends Model
{
    protected $table = 'tipoProducto';
    static public string $tables = 'tipoProducto';
    protected $guarded = [];

    public static function getAll()
    {
        return DB::table(self::$tables)
            ->whereNull('deleted_at')
            ->get();
    }

    public static function setTiposProducto($productos)
    {
        foreach ($productos as $key => $producto) {
            $productos[$key]->productoTipo = DB::table('productoTipo')
                ->where('producto', '=', $producto->id)
                ->get()->pluck('tipoProducto');
        }
        return $productos;
    }

    public static function saveTiposProducto(int $producto, array $Tipos)
    {
        DB::table('productoTipo')
            ->where('producto', '=', $producto)
            ->delete();
        foreach ($Tipos as $tipo) {
            DB::table('productoTipo')
                ->insert(['producto' => $producto, 'tipoProducto' => $tipo]);
        }
    }
}
