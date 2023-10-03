<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DetallesOrden extends Model
{
    protected $table = 'detallesOrden';
    public static string $tables = 'detallesOrden';
    protected $guarded = [];

    public static function newOrdenDetalle(array $detalle, int $ordenTrabajo)
    {
        DB::table(self::$tables)
            ->where('ordenTrabajo', $ordenTrabajo)
            ->delete();

        $detalles = Collection::empty();
        foreach ($detalle as $item) {
            $detalles->add([
                'stock' => $item['stock'],
                'cantidad' => $item['cantidad'],
                'costo' => $item['costo'],
                'total' => $item['total'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'ordenTrabajo' => $ordenTrabajo
            ]);

        }
        DB::table(self::$tables)
            ->insert($detalles->toArray());
    }

    public static function setAllDetalle(Collection $ordenes): Collection
    {
        return $ordenes->map(function ($item, $key) {
            $item->detallesOrden = DB::table(self::$tables)
                ->where('ordenTrabajo', $item->id)
                ->get()
                ->toArray();
            return $item;
        });
    }

//    public static function setOneDetalles($orden)
//    {
//        $detalle = DB::table(self::$tables)
//            ->where('ordenTrabajo',  $orden->id)
//            ->get()->toArray();
//        $orden->detallesOrden = $detalle;
//        return $orden;
//    }

    public static function sell(int $idOrden, bool $reposicion = false)
    {
        $detalle = DB::table(self::$tables)
            ->where('ordenTrabajo', $idOrden)
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
                ], true, $reposicion);
            }
        }
    }

    public static function getTotal(int $idOrden, array $detalleOrden): float|int
    {
        $total = 0;
        foreach ($detalleOrden as $item) {
            $totalParcial = round($item['costo'],2) * $item['cantidad'];
            DB::table(self::$tables)
                ->where('ordenTrabajo', $idOrden)
                ->where('stock', $item['stock'])
                ->update([
                    'costo' => $item['costo'],
                    'total' => $totalParcial,
                ]);
            $total += ($totalParcial);
        }
        return round($total,2);
    }
}
