<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    protected $table = 'productos';
    public static string $tables = 'productos';
    protected $guarded = [];

    public static function getAll(): Collection
    {
        $productos = new Generic(self::$tables);
        return $productos->getAll();
    }
}
