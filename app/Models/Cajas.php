<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cajas extends Model
{
    protected $table = 'cajas';
    protected static string $tables = 'cajas';
    protected $guarded = [];

    public function Sucursales(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Sucursal::class,'id','sucursal');
    }

    public static function getAll(int $sucursal = null, int $caja_padre = null): Collection
    {
        $cajas = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $cajas = $cajas->where(self::$tables . ".enable", '1')
                ->where(self::$tables . '.sucursal', $sucursal);
        }
        if (!empty($caja_padre)) {
            $cajas = $cajas->where(self::$tables . '.dependeDe', $caja_padre);
        }
        $cajas = $cajas
            ->orderBy(self::$tables . '.updated_at', 'desc')
            ->whereNull(self::$tables . '.deleted_at')
            ->select(self::$tables . '.*', Sucursal::$tables . '.nombre as nombreSucursal')
            ->leftJoin(Sucursal::$tables, Sucursal::$tables . '.id', '=', self::$tables . '.sucursal');
        return $cajas->get();
    }


    public static function sell(array $request, bool $mov = true): Builder
    {
        $caja = DB::table(self::$tables)
            ->where('sucursal', $request['sucursal']);
//        $monto = 0;
//        if ($caja->count() > 0) {
//            $monto = $caja->get()->fisrt()->monto;
//        }
//        $monto += $request['montoVenta'];
//        $caja->updateOrInsert([
//            'sucursal' => $request['sucursal']
//        ], ['monto' => $monto]);

        if ($mov) {
            $movimiento = DB::table(MovimientoCaja::$tables)
                ->insertGetId([
                    'cajaOrigen' => null,
                    'cajaDestino' => $caja->get()->first()->id,
                    'tipo' => MovimientoCaja::$tipoMovimiento->ordenesVenta,
                    'monto' => $request['montoVenta'],
                    'observaciones' => "venta de insumos",
                    'ordenTrabajo' => !empty($request['ordenTrabajo']) ? $request['ordenTrabajo'] : "",
                    'user' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }
        return $caja;
    }

    static public function getSaldo($idCaja, $fechaMovimientosInicio, $fechaMovimientosFin, $array = false, $get = null, $admin = false): array
    {
        $deudas = $array || isset($get['deudas']) ? Collection::empty() : 0;
        $ventas = $array || isset($get['ventas']) ? Collection::empty() : 0;
        $recibos = $array || isset($get['recibos']) ? Collection::empty() : [0, 0];
        $cajas = $array || isset($get['cajas']) ? Collection::empty() : [0, 0];
        $movimientosAll = isset($get['movimientos']) ? Collection::empty() : null;

        $arqueos = Collection::empty();
        $movimientos = DB::table(MovimientoCaja::$tables)
            ->where(function ($query) use ($idCaja) {
                $query->where('cajaOrigen', '=', $idCaja)
                    ->orWhere('cajaDestino', '=', $idCaja);
            });

//         if (!$admin) {
//             if (isset($get['arqueo']))
//                 $movimientos->where('fechaCierre', 'like', $get['arqueo']);
//             else
//                 $movimientos->whereNull('fechaCierre');
//         }
        $fechaArqueoI = Carbon::parse($fechaMovimientosInicio);
        $fechaArqueoF = Carbon::parse($fechaMovimientosFin);
        $movimientos->where('created_at', '<=', $fechaArqueoF->endOfDay()->toDateTimeString());
//        if($admin) {
        $movimientos->where('created_at', '>=', $fechaArqueoI->startOfDay()->toDateTimeString());
//        }
        $movimientos = $movimientos->get();
        $total = 0;

        foreach ($movimientos as $key => $movimiento) {
            switch ($movimiento->tipo) {
                case 0:
                    if (isset($get['movimientos'])) {
                        $movimientosAll->add($movimiento);
                    }
                    if ($array || isset($get['deudas'])) {
                        $deudas->add( OrdenesTrabajo::find($movimiento->ordenTrabajo));
                    } else {
                        $deudas += $movimiento->monto;
                    }
                    $total += $movimiento->monto;
                    break;
                case 1:
                    if (!empty($movimiento->ordenTrabajo)) {
                        if (isset($get['movimientos'])) {
                            $movimientosAll->add($movimiento);
                        }
                        if ($array || isset($get['ventas'])) {
                            $ventas->add( OrdenesTrabajo::find($movimiento->ordenTrabajo));
                        } else {
                            $ventas += $movimiento->monto;
                        }
                        $total += $movimiento->monto;
                    }
                    break;
                case 2:
                    if (isset($get['movimientos'])) {
                        $movimientosAll->add($movimiento);
                    }
                    if ($array || isset($get['cajas'])) {
                        $cajas->add($movimiento);
                    } else {
                        if (isset($movimiento->cajaDestino)) {
                            $cajas[0] += $movimiento->monto;
                        } else {
                            $cajas[1] += $movimiento->monto;
                        }
                    }
                    $total += $movimiento->monto;
                    break;
                case 3:
                    $arqueos->add($movimiento);
                    $total += $movimiento->monto;
                    break;
                case 4:
                    $movimientoRecibo = DB::table(Recibo::$tables)
                        ->where('movimientoCaja', $movimiento->id);
                    if ($movimientoRecibo->count() > 0) {
                        if (isset($get['movimientos'])) {
                            $movimientosAll->add($movimiento);
                        }
                        if ($array || isset($get['recibos'])) {
                            $recibos->add($movimientoRecibo->get()->first());
                        } else {
                            if ($movimientoRecibo->get()->first()->tipo) {
                                $recibos[0] += $movimiento->monto;
                            } else {
                                $recibos[1] += $movimiento->monto;
                            }
                        }
                        $total += $movimiento->monto;
                    }
                    break;
            }
        }
        $saldos = DB::table(MovimientoCaja::$tables)
            ->where('tipo', '=', 3)
            ->where(function ($query) use ($idCaja) {
                $query->where('cajaOrigen', '=', $idCaja)
                    ->orWhere('cajaDestino', '=', $idCaja);
            })
            ->where('created_at', '<', $fechaArqueoF->toDateTimeString('minute'))
            ->orderBy('created_at', 'DESC');
        $saldo = 0;
        if ($saldos->count() > 0) {
            $saldo = $saldos->first()->cierre;
        }

        return array('ventas' => $ventas, 'deudas' => $deudas, 'recibos' => $recibos, 'cajas' => $cajas, 'saldo' => $saldo, 'movimientos' => $movimientosAll, 'arqueos' => $arqueos);
    }

}
