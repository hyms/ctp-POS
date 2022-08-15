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

    public static function getAll(bool $isAdm = False): Collection
    {
        $sucursales = new Generic(self::$tables);
        return !$isAdm
            ? $sucursales->getAll(['enable' => '1'])
            : $sucursales->getAll();
    }

}
