<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoProductos extends Model
{
    protected $table='tipoProducto';
    static public $tables='tipoProducto';
    protected $guarded=[];

    public static function getAll()
    {
        return DB::table(self::$tables)
            ->whereNull('deleted_at')
            ->get();
    }

}
