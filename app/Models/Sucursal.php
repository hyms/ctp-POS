<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    public static string $tables = 'sucursales';
    protected $guarded = [];

    public static function getAll(bool $all = False): Collection
    {
        $sucursales = new Generic(self::$tables);
        return !$all
            ? $sucursales->getAll(['enable' => '1'])
            : $sucursales->getAll();
    }

    public static function getSelect(bool $all = False): Collection
    {
        $sucursales = self::getAll($all);
        return $sucursales->map(function ($item, $key) {
            return ['value' => (string)$item->id, 'text' => $item->nombre];
        });
    }

}
