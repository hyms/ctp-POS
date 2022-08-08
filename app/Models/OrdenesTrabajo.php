<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\This;

//use Kutia\Larafirebase\Facades\Larafirebase;

class OrdenesTrabajo extends Model
{
    protected $table = 'ordenesTrabajo';
    public static string $tables = 'ordenesTrabajo';
    protected $guarded = [];
    use SoftDeletes;

    static public function estadoCTP($id = null)
    {
        $estado = collect([
            ['value'=> '-1' ,'text'=> 'Anulado'],
            ['value'=> '0' ,'text'=> 'Pagado'],
            ['value'=> '1' ,'text'=> 'En Proceso'],
            ['value'=> '2' ,'text'=> 'Deuda'],
            ['value'=> '5' ,'text'=> 'Quemado'],
            ['value'=> '10' ,'text'=> 'Reposicion'],
        ]);

        if ($id === null) {
            return $estado;
        }

        $first = $estado->firstWhere('value', '=', $id);
        return $first['text']??'';

    }

    public static function  getAll(int $sucursal, int $usuario = null, int $tipo = null, Collection $report = null): Builder
    {
        $isEmpty = $report->isEmpty();
        $ordenes = new Generic(self::$tables);
        $ordenes->onlyBuild = true;
        $report->put('sucursal', $sucursal);
        if ($usuario != null) {
            $report->put('userDiseñador', $usuario);
        }
        if ($tipo != null) {
            $report->put('tipoOrden', $tipo);
        }
        return $ordenes->getAll(filters:$report->all(), limit:(($isEmpty) ? 500 : null));
    }

    public static function newOrden(array $orden, array $productos, int $id = null, bool $reposicion = false)
    {
        $ordenes = DB::table(self::$tables);
        if ($reposicion) {
            $orden['tipoOrden'] = "0";
        }
        $orden['updated_at'] = Carbon::now();
        if (isset($id)) {
            $ordenes
                ->where('id', $id)
                ->update($orden);
        } else {
            $id = DB::transaction(function () use ($orden, $reposicion) {
                $tipo = (object)array();
                if ($reposicion) {
                    $tipo->codigo = 'R';
                } else {
                    $tipo = DB::table(TipoProductos::$tables)->where('id', $orden['tipoOrden'])->get()->first();
                }

                $correlativo = 1;
                $ot = DB::table(self::$tables)
                    ->where('sucursal', $orden['sucursal'])
                    ->where('tipoOrden', $orden['tipoOrden'])
                    ->orderBy('correlativo', 'desc')
                    ->limit(1);
                if ($ot->count() > 0) {
                    $correlativo = $ot->get()->first()->correlativo + 1;
                }
                $orden['correlativo'] = $correlativo;
                $orden['codigoServicio'] = $tipo->codigo . '-' . $correlativo;
                $orden['created_at'] = Carbon::now();
                return DB::table(self::$tables)->insertGetId($orden);
            });
        }
        if (!empty($id)) {
            DetallesOrden::newOrdenDetalle($productos, $id);
        }
        return $id;
    }

    public static function getReport(string $fechaI, string $fechaF, string $sucursal, string $tipo = null)
    {
        $ordenes = new Generic(self::$tables);
        $ordenes->filterDate = 'updated_at';
        $ordenes->onlyBuild = true;
        $ordenes->orderBy = 'created_at';
        $filter = collect([
            'fechaI' => $fechaI,
            'fechaF' => $fechaF,
            'sucursal' => $sucursal
        ]);
        if (!empty($tipo)) {
            $filter->add(['tipoOrden' => $tipo]);
        }
        $ordenes = $ordenes->getAll($filter->all())
            ->whereIn('estado', [0, 2, 5, -1, 10]);

        return $ordenes->get();
    }

    public static function venta(array $orden)
    {
        $ordenes = DB::table(self::$tables);
        $orden['updated_at'] = Carbon::now();
        $realized = $ordenes
            ->where('id', $orden['id'])
            ->update($orden);
        if ($realized) {
            DetallesOrden::sell($orden['id']);
            $item = DB::table(self::$tables)->where('id', $orden['id'])->get()->first();
            Cajas::sell([
                'sucursal' => $item->sucursal,
                'montoVenta' => $item->montoVenta,
                'ordenTrabajo' => $item->id,
            ]);
            return $orden['id'];
        }
        return null;
    }

    public static function deuda(array $orden, float $saldo, float $monto)
    {
        $ordenes = DB::table(self::$tables);
        $orden['updated_at'] = Carbon::now();
        $realized = $ordenes
            ->where('id', $orden['id'])
            ->update($orden);
        if ($realized) {
            $item = DB::table(self::$tables)->where('id', $orden['id'])->get()->first();
            $values = [
                'codigo' => '',
                'detalle' => 'pago de deuda de orden ' . (($item->tipoOrden == null) ? "#{$item->correlativo}" : $item->codigoServicio),
                'nombre' => $item->responsable,
                'ciNit' => '',
                'codigoVenta' => $item->correlativo,
                'orden' => $item->id,
                'saldo' => $saldo,
                'monto' => $monto,
                'acuenta' => ($saldo - $monto),
                'tipo' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'sucursal' => $item->sucursal,
                'userVenta' => Auth::id(),
            ];
            $caja = Cajas::sell([
                'sucursal' => $item->sucursal,
                'montoVenta' => $item->montoVenta,
                'ordenTrabajo' => $item->id,
            ], false);
            Recibo::guardarDeuda($values, $caja->get()->first()->id, $item->id);
        }
    }

    public static function getTotal(Collection $ordenes)
    {
        $pagado = 0;
        $total = 0;
        foreach ($ordenes as $orden) {
            if ($orden->estado == 1) {
                continue;
            }
            $detalle = collect($orden->detallesOrden);
            $total += $detalle->sum('total');
          /*  foreach ($detalle as $item) {
                $total += $item->total;
            }*/
            $movimientos = DB::table(MovimientoCaja::$tables)
                ->where('ordenTrabajo', $orden->id)
                ->get();
            $pagado += $movimientos->sum('monto');
            /*foreach ($movimientos as $movimiento) {
                $pagado += $movimiento->monto;
            }*/
        }
        return ($total - $pagado) == 0
            ? $total
            : ($total - $pagado);
    }

//    public static function notifyNewOrden($idOrden)
//    {
//        try {
//            $orden = DB::table(self::$tables)->where('id', $idOrden)->first();
//            $fcmTokens = DB::table(User::$tables)
//                ->where('id', '!=', Auth::id())
//                ->select('tokenpush')
//                ->groupBy('tokenpush')
//                ->pluck('tokenpush')->toArray();
//            Larafirebase::fromRaw([
//                'registration_ids' => $fcmTokens,
//                'data' => [
//                    'newOrden' => true,
//                    'orden' => $orden->id
//                ],
//                'notification' => [
//                    'title' => "Nueva Orden",
//                    'body' => "Orden " . $orden->codigoServicio
//                ],
//            ])->send();
//        } catch (Exception $error) {
//            Log::error($error->getMessage());
//        }
//    }
}
