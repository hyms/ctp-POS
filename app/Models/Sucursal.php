<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    public static $tables = 'sucursales';
    protected $guarded = [];

    public static function getAll(bool $isAdm = False)
    {
        $sucursales = DB::table(self::$tables);
        if (!$isAdm) {
            $sucursales = $sucursales->where('enable', '=', '1');
        }
        $sucursales = $sucursales
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        return $sucursales->get();
    }

}
