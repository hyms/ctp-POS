<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdenesTrabajo extends Model
{
    protected $table = 'ordenesTrabajo';
    protected static $tables = 'ordenesTrabajo';
    protected $guarded = [];
    use SoftDeletes;

    static public function estadoCTP($id = null)
    {
        $estado = [
            '-1' => 'Anulado',
            '0' => 'Cancelado',
            '1' => 'En Proceso',
            '2' => 'Deuda',
        ];
        if (is_null($id)) {
            return $estado;
        }

        return $estado[$id];
    }

    public static function getAll(int $sucursal = null, int $usuario = null, array $report = [])
    {
        $ordenes = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $ordenes = $ordenes->where('sucursal', '=', $sucursal);
        }
        if (!empty($usuario)) {
            $ordenes = $ordenes->where('userDiseñador', '=', $usuario);
        }
        $ordenes =  $ordenes
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        if (empty($report)) {
//             $ordenes = $ordenes->limit(10);
//              $ordenes = $ordenes->whereBetween('created_at', [Carbon::now()->toDateString().' 00:00:00',Carbon::now()->toDateString().' 23:59:59']);
        }
        else {
            if (isset($report['fecha'])) {
                $ordenes = $ordenes->whereBetween('created_at', [$report['fecha'].' 00:00:00',$report['fecha'].' 23:59:59']);
            }
            if (isset($report['orden'])) {
                $ordenes = $ordenes->where('id', '=', $report['orden']);
            }
        }
        return $ordenes;
    }

    public static function newOrden(array $orden, array $productos)
    {
        $ordenes = DB::table(self::$tables);
        $orden['created_at'] = now();
        $orden['updated_at'] = now();
        $id = $ordenes->insertGetId($orden);
        if (!empty($id)) {
            DetallesOrden::newOrdenDetalle($productos, $id);
        }
    }

    public static function getCorrelativo(int $sucursal)
    {
        $correlativo = 1;
        $ot = DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal)
            ->orderBy('correlativo', 'desc')
            ->limit(1);
        if ($ot->count() > 0) {
            $correlativo = $ot->get()[0]->correlativo + 1;
        }
        return $correlativo;
    }

    public static function getReport(string $fecha, string $sucursal, string $tipo = null)
    {
        $ordenes = DB::table(self::$tables)
            ->whereBetween('created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])
            ->where('sucursal', '=', $sucursal)
            ->whereNull('deleted_at');
        if (!empty($tipo)) {
            $ordenes = $ordenes->where('tipoOrden', '=', $tipo);
        }
        $ordenes = $ordenes->orderBy('created_at');
        return $ordenes->get();
    }

    public static function venta(array $orden)
    {
        $ordenes = DB::table(self::$tables);
        $orden['updated_at'] = now();
        $realized = $ordenes
            ->where('id', $orden['id'])
            ->update($orden);
        if($realized) {
            DetallesOrden::sell($orden['id']);
            $item=OrdenesTrabajo::find($orden['id']);
            Cajas::sell([
                'sucursal' => $item->sucursal,
                'montoVenta' => $item->montoVenta,
                'ordenTrabajo' => $item->id,
            ]);
        }
    }

}
