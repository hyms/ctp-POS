<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class ProductoStock extends Model
{
    protected $table = 'stock';
    protected static $tables = 'stock';

    public static function getAll(int $sucursal = null)
    {
        $stock = DB::table(self::$tables);
        if(!isNull())
        {
            $stock = $stock->where('sucursal','=',$sucursal);
        }
        $stock = $stock->whereNull('deleted_at');
        return $stock->get();
    }

}
