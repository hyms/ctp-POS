<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenesTrabajo extends Model
{
    protected $table = 'ordenesTrabajo';
    public static $tables = 'ordenesTrabajo';
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
        $ordenes = $ordenes
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc');

        if (!empty($report)) {
            if (isset($report['fecha'])) {
                $fecha = Carbon::parse($report['fecha']);
                $ordenes = $ordenes->whereBetween('created_at', [$fecha->startOfDay()->toDateTimeString(), $fecha->endOfDay()->toDateTimeString()]);
            }
            if (isset($report['orden'])) {
                $ordenes = $ordenes->where('correlativo', '=', $report['orden']);
            }
            if (isset($report['responsable'])) {
                $ordenes = $ordenes->where('responsable', '=', $report['responsable']);
            }
        } else {
            $ordenes->limit(100);
        }
        return $ordenes;
    }

    public static function newOrden(array $orden, array $productos, int $id = null)
    {
        $ordenes = DB::table(self::$tables);
        $orden['updated_at'] = now();
        if (isset($id)) {
            $ordenes
                ->where('id', '=', $id)
                ->update($orden);
        } else {
            $id = DB::transaction(function () use ($orden) {
                $tipo = DB::table(TipoProductos::$tables)->where('id', '=', $orden['tipoOrden'])->get()->first();
                $correlativo = 1;
                $ot = DB::table(self::$tables)
                    ->where('sucursal', '=', $orden['sucursal'])
                    ->where('tipoOrden', '=', $orden['tipoOrden'])
                    ->orderBy('correlativo', 'desc')
                    ->limit(1);
                if ($ot->count() > 0) {
                    $correlativo = $ot->get()->first()->correlativo + 1;
                }
                $orden['correlativo'] = $correlativo;
                $orden['codigoServicio'] = $tipo->codigo . '-' . $correlativo;
                $orden['created_at'] = now();
                return DB::table(self::$tables)->insertGetId($orden);
            });
        }
        if (!empty($id)) {
            DetallesOrden::newOrdenDetalle($productos, $id);
        }
    }

    public static function getReport(string $fechaI, string $fechaF, string $sucursal, string $tipo = null)
    {
        $fechaRI = Carbon::parse($fechaI);
        $fechaRF = Carbon::parse($fechaF);
        $ordenes = DB::table(self::$tables)
            ->whereBetween('updated_at', [$fechaRI->startOfDay()->toDateTimeString(), $fechaRF->endOfDay()->toDateTimeString()])
            ->where('sucursal', '=', $sucursal)
            ->whereIn('estado', [0, 2])
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
        if ($realized) {
            DetallesOrden::sell($orden['id']);
            $item = DB::table(self::$tables)->where('id', '=', $orden['id'])->get()->first();
            Cajas::sell([
                'sucursal' => $item->sucursal,
                'montoVenta' => $item->montoVenta,
                'ordenTrabajo' => $item->id,
            ]);
        }
    }

    public static function deuda(array $orden, float $saldo, float $monto)
    {
        $ordenes = DB::table(self::$tables);
        $realized = $ordenes
            ->where('id', $orden['id'])
            ->update($orden);
        if ($realized) {
            $item = DB::table(self::$tables)->where('id', '=', $orden['id'])->get()->first();
            $values = [
                'codigo' => '',
                'detalle' => 'pago de deuda de orden #' . $item->correlativo,
                'nombre' => $item->responsable,
                'ciNit' => '',
                'codigoVenta' => $item->correlativo,
                'saldo' => $saldo,
                'monto' => $monto,
                'acuenta' => $saldo - $monto,
                'tipo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
                'sucursal' => $item->sucursal,
                'userVenta' => Auth::id(),
            ];
            $caja = Cajas::sell([
                'sucursal' => $item->sucursal,
                'montoVenta' => $item->montoVenta,
                'ordenTrabajo' => $item->id,
            ], false);
            Recibo::guardarDeuda($values, $caja->get()->first()->id, $item->correlativo);
        }
    }

    public static function getDeuda(array $ordenes)
    {
        $pagado = 0;
        $total = 0;
        foreach ($ordenes as $orden) {
            $detalle = $orden->detallesOrden;
            foreach ($detalle as $item) {
                $total += $item->total;
            }
            $movimientos = DB::table(MovimientoCaja::$tables)
                ->where('ordenTrabajo', '=', $orden->id)
                ->get();
            foreach ($movimientos as $movimiento) {
                $pagado += $movimiento->monto;
            }
        }
        return $total - $pagado;
    }

}
