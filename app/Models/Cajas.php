<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class Cajas extends Model
{
    protected $table = 'cajas';
    protected static $tables = 'cajas';
    protected $guarded = [];

    public static function getAll(int $sucursal = null, int $caja_padre = null)
    {
        $sucursales = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $sucursales = $sucursales->where('enable', '=', '1');
            $sucursales = $sucursales->where('sucursal', '=', $sucursal);
        }
        if (!empty($caja_padre)) {
            $sucursales = $sucursales->where('dependeDe', '=', $caja_padre);
        }
        $sucursales = $sucursales
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        return $sucursales->get();
    }
    public static function getOne(int $sucursal)
    {
        return DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal);
    }
    public static function sell(array $request, bool $mov = true)
    {
        $caja = self::getOne($request['sucursal']);
        $monto = 0;
        if ($caja->count() > 0) {
            $monto = $caja->get()[0]->monto;
        }
        $monto += $request['montoVenta'];
        $caja->updateOrInsert([
            'sucursal' => $request['sucursal']
        ], ['monto' => $monto]);

        if ($mov) {
            $movimiento = DB::table(MovimientoCaja::$tables);
            $movimiento->insert([
                'cajaOrigen' => null,
                'cajaDestino' => $caja->get()[0]->id,
                'tipo' => 0,
                'monto' => $request['montoVenta'],
                'observaciones' => "venta de insumos",
                'ordenTrabajo' => !empty($request['ordenTrabajo']) ? $request['ordenTrabajo'] : "",
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return $caja;
    }

}
