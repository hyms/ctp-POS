<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    protected $table = 'productos';
    protected static $tables = 'productos';
    protected $guarded = [];

    public static function getAll()
    {
        $productos =DB::table(self::$tables);
        $productos = $productos
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        return $productos->get();

    }

    public function getMovimientoStocks()
    {
        return $this->hasMany(MovimientoStock::class, 'fk_idProducto', 'idProducto');
    }

    public function getProductoStocks()
    {
        return $this->hasMany(ProductoStock::class, 'fk_idProducto', 'idProducto');
    }
}
