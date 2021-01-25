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
    public static function getTableAdmin($suscursales,$productos)
    {
        $stock = [];
        foreach ($suscursales as $sucursal)
        {
            $stockItem=[];
            foreach ($productos as $producto){
                $tmp = DB::table(self::$tables)->where([
                    ['sucursal','=',$sucursal->id],
                    ['producto','=',$producto->id]
                ]);
                if($tmp->count()>0) {
                    $stockItem[$producto->id] = $tmp->get()[0]['cantidad'];
                }
                else{
                    $stockItem[$producto->id] = null;
                }
            }
            $stock[$sucursal->id] = $stockItem;
        }
        return $stock;
    }
}
