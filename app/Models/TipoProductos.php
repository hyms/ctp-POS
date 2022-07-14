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
        foreach ($productos as $key => $producto) {
            $productos[$key]->productoTipo = DB::table('productoTipo')
                ->where('producto', '=', $producto->id)
                ->get()
                ->pluck('tipoProducto');

            $productos[$key]->productoTipoView = DB::table('tipoProducto')
                ->whereIn('id', $productos[$key]->productoTipo)
                ->whereNull('deleted_at')
                ->get()
                ->implode('nombre', ', ');
        }
        return $productos;
    }

    public static function saveTiposProducto(int $producto, Collection $Tipos)
    {
        DB::table('productoTipo')
            ->where('producto', '=', $producto)
            ->delete();
//        $inserts = collect([]);
//        foreach ($Tipos as $tipo) {
//            $inserts->add();
//        }
        $inserts = $Tipos->map(function ($item, $key) use ($producto) {
            return ['producto' => $producto, 'tipoProducto' => $item];
        });
        if ($inserts->isNotEmpty()) {
            DB::table('productoTipo')
                ->insert($inserts->all());
        }
    }
}
