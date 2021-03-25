<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetallesOrden extends Model
{
    protected $table = 'detallesOrden';
    public static $tables = 'detallesOrden';

   public static function newOrdenDetalle(array $detalle,int $ordenTrabajo)
   {
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
//           if (!empty($id)) {
//               ProductoStock::sell([
//                   'sucursal' => $item['sucursal'],
//                   'producto' => $item['producto'],
//                   'cantidad' => $item['cantidad'],
//                   'detalleOrden' => $id,
//               ]);
//           }
       }
   }

    public static function getAll(\Illuminate\Support\Collection $ordenes): array
    {
        $ordenes = $ordenes->toArray();
        foreach ($ordenes as $key => $item)
        {
            $detalle = DB::table(self::$tables)
                ->where('ordenTrabajo','=',$item->id)
                ->get()->toArray();
            $ordenes[$key]->detallesOrden=$detalle;
        }
        return $ordenes;
    }

    public static function getOne($orden)
    {
            $detalle = DB::table(self::$tables)
                ->where('ordenTrabajo','=',$orden->id)
                ->get()->toArray();
            $orden->detallesOrden=$detalle;
        return $orden;
    }
}
