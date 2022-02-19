<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovimientoStock extends Model
{
    protected $table = 'movimientosStock';
    public static string $tables = 'movimientosStock';
    protected $guarded = [];

    public static function gelAll()
    {
        $movimientos = DB::table(self::$tables);
        $movimientos = $movimientos
            ->leftJoin(User::$tables, 'user', '=', User::$tables . '.id')
            ->leftJoin(ProductoStock::$tables . ' as so', 'stockOrigen', '=', 'so.id')
            ->leftJoin(ProductoStock::$tables . ' as sd', 'stockDestino', '=', 'sd.id')
            ->select(self::$tables . '.*', 'so.sucursal as soSucursal', 'sd.sucursal as sdSucursal', User::$tables . '.nombre', User::$tables . '.apellido')
            ->orderBy(self::$tables . '.updated_at', 'DESC');
        return $movimientos->get();
    }

    public static function getAllTable(array $stock, bool $ingreso,array $request=[])
    {
        $movimientos = DB::table(self::$tables);
        $movimientos = $ingreso
            ? $movimientos->whereIn('stockDestino', $stock)
            : $movimientos->whereIn('stockOrigen', $stock);
        if(isset($request))
        {
            if (isset($request['fechaI']) && isset($request['fechaF'])) {
                $fechaI = Carbon::parse($request['fechaI']);
                $fechaF = Carbon::parse($request['fechaF']);
                $movimientos = $movimientos->whereBetween('created_at', [$fechaI->startOfDay()->toDateTimeString(), $fechaF->endOfDay()->toDateTimeString()]);
            }
            if (isset($request['producto'])) {
                $movimientos = $movimientos->where('producto', '=', $request['producto']);
            }
            if (isset($request['observaciones'])) {
                $movimientos = $movimientos->where('observaciones', 'like', "%{$request['observaciones']}%");
            }
        }
        else{
            $movimientos = $movimientos->limit(500);
        }
        $movimientos = $movimientos->orderBy(self::$tables . '.updated_at', 'DESC');
        return $movimientos->get();
    }
}
