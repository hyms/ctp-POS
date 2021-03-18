<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected static $tables = 'clientes';
    protected $guarded = [];

    public static function getAll(int $sucursal = null)
    {
        $clientes = DB::table(self::$tables);
        if(!empty($sucursal)){
            $clientes = $clientes->where('sucursal','=',$sucursal);
        }
        $clientes = $clientes
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        return $clientes->get();
    }
}
