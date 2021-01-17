<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class Producto extends Model
{
    protected $table = 'productos';
    protected static $tables = 'productos';
    protected $guarded = [];

    public static function getAll(int $sucursal = null)
    {
        $productos =DB::table(self::$tables);
        if (!isNull($sucursal)) {
            $productos = $productos->where('sucursal', '=', $sucursal);
        }
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
