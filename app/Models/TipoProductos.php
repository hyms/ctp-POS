<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TipoProductos extends Model
{
    protected $table = 'tipoProducto';
    static public string $tables = 'tipoProducto';
    static public string $tablesAlter = 'productoTipo';
    protected $guarded = [];
    use SoftDeletes;

    public static function getAll(): Collection
    {
        return DB::table(self::$tables)
            ->whereNull('deleted_at')
            ->get();
    }

    public static function getTipoProductoxProducto(Collection $productos): Collection
    {
        $productos->transform(function($item,$key){
            $item->productoTipo = DB::table(self::$tablesAlter)
                ->where('producto', $item->id)
                ->get()
                ->pluck('tipoProducto');

            $item->productoTipoView = DB::table('tipoProducto')
                ->whereIn('id', $item->productoTipo)
                ->whereNull('deleted_at')
                ->get()
                ->implode('nombre', ', ');
            return $item;
        });

        return $productos;
    }

    public static function saveTiposProducto(int $producto, Collection $Tipos)
    {
        DB::table(self::$tablesAlter)
            ->where('producto', '=', $producto)
            ->delete();

        $inserts = $Tipos->map(function ($item, $key) use ($producto) {
            return ['producto' => $producto, 'tipoProducto' => $item];
        });
        if ($inserts->isNotEmpty()) {
            DB::table(self::$tablesAlter)
                ->insert($inserts->all());
        }
    }
}
