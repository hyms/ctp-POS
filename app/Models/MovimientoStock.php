<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MovimientoStock extends Model
{
    protected $table = 'movimientosStock';
    public static string $tables = 'movimientosStock';
    protected $guarded = [];

    public static function gelAll(): Collection
    {
        $movimientos = DB::table(self::$tables)
            ->leftJoin(User::$tables, 'user',  User::$tables . '.id')
            ->leftJoin(ProductoStock::$tables . ' as so', 'stockOrigen',  '=','so.id')
            ->leftJoin(ProductoStock::$tables . ' as sd', 'stockDestino', '=', 'sd.id')
            ->select(self::$tables . '.*', 'so.sucursal as soSucursal', 'sd.sucursal as sdSucursal', User::$tables . '.nombre', User::$tables . '.apellido')
            ->orderBy(self::$tables . '.updated_at', 'DESC');
        return $movimientos->get();
    }

    public static function getAllTable(array $stock, bool $ingreso, array $request = []): Collection
    {
        $movimientos = new Generic(self::$tables);
        $movimientos->onlyBuild=true;
        $movimientos = isset($request)
            ? $movimientos->getAll($request)
            : $movimientos->getAll([], false, 500);

        $movimientos = $ingreso
            ? $movimientos->whereIn('stockDestino', $stock)
            : $movimientos->whereIn('stockOrigen', $stock);

        return $movimientos->get();
    }
}
