<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DetallesOrden extends Model
{
    protected $table = 'detallesOrden';
    public static $tables = 'detallesOrden';
    protected $guarded = [];

    public static function newOrdenDetalle(array $detalle, int $ordenTrabajo)
    {
        $detalles = DB::table(self::$tables)->where('ordenTrabajo', '=', $ordenTrabajo);
        if ($detalles->count() > 0) {
            $detalles->delete();
        }
        foreach ($detalle as $item) {
            $id = DB::table(self::$tables)
                ->insertGetId([
                    'stock' => $item['stock'],
                    'cantidad' => $item['cantidad'],
                    'costo' => $item['costo'],
                    'total' => $item['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'ordenTrabajo' => $ordenTrabajo
                ]);

        }
    }

    public static function getAll(Collection $ordenes)
    {
        $ordenes = $ordenes->toArray();
        if (count($ordenes) > 0) {
            foreach ($ordenes as $key => $item) {
                $detalle = DB::table(self::$tables)
                    ->where('ordenTrabajo', '=', $item->id)
                    ->get()->toArray();
                $ordenes[$key]->detallesOrden = $detalle;
            }
        }
        return $ordenes;
    }

    public static function getOne($orden)
    {
        $detalle = DB::table(self::$tables)
            ->where('ordenTrabajo', '=', $orden->id)
            ->get()->toArray();
        $orden->detallesOrden = $detalle;
        return $orden;
    }

    public static function sell(int $idOrden)
    {
        $detalle = DB::table(self::$tables)
            ->where('ordenTrabajo', '=', $idOrden)
            ->leftJoin(ProductoStock::$tables, self::$tables . '.stock', '=', ProductoStock::$tables . '.id')
            ->select(self::$tables . '.*',
                ProductoStock::$tables . '.producto as producto',
                ProductoStock::$tables . '.sucursal as sucursal'
            );
        if ($detalle->count() > 0) {
            foreach ($detalle->get() as $item) {
                ProductoStock::sell([
                    'sucursal' => $item->sucursal,
                    'producto' => $item->producto,
                    'cantidad' => $item->cantidad,
                    'detalleOrden' => $item->id,
                ]);


            }
        }
    }

    public static function getTotal(int $idOrden, array $detalleOrden)
    {
        $total = 0;
        foreach ($detalleOrden as $item) {
            $total += $item['costo'] * $item['cantidad'];
            DB::table(self::$tables)
                ->where('ordenTrabajo', '=', $idOrden)
                ->where('stock', '=', $item['stock'])
                ->update([
                    'costo' => $item['costo'],
                    'total' => $item['costo'] * $item['cantidad'],
                ]);
        }
        return $total;
    }
}
