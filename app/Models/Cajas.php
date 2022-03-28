<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cajas extends Model
{
    protected $table = 'cajas';
    protected static string $tables = 'cajas';
    protected $guarded = [];

    public static function getAll(int $sucursal = null, int $caja_padre = null)
    {
        $cajas = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $cajas = $cajas->where(self::$tables.'.enable', '=', '1');
            $cajas = $cajas->where(self::$tables.'.sucursal', '=', $sucursal);
        }
        if (!empty($caja_padre)) {
            $cajas = $cajas->where(self::$tables.'.dependeDe', '=', $caja_padre);
        }
        $cajas = $cajas
            ->orderBy(self::$tables.'.updated_at', 'desc')
        ->whereNull(self::$tables.'.deleted_at')
        ->select(self::$tables.'.*', Sucursal::$tables.'.nombre as nombreSucursal')
        ->leftJoin(Sucursal::$tables,Sucursal::$tables.'.id','=',self::$tables.'.sucursal');
        $cajas = $cajas->get();

        return $cajas;
    }

    public static function getOne(int $sucursal)
    {
        return DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal);
    }

    public static function sell(array $request, bool $mov = true)
    {
        $caja = self::getOne($request['sucursal']);
//        $monto = 0;
//        if ($caja->count() > 0) {
//            $monto = $caja->get()->fisrt()->monto;
//        }
//        $monto += $request['montoVenta'];
//        $caja->updateOrInsert([
//            'sucursal' => $request['sucursal']
//        ], ['monto' => $monto]);

        if ($mov) {
            $movimiento = DB::table(MovimientoCaja::$tables);
            $movimiento->insertGetId([
                'cajaOrigen' => null,
                'cajaDestino' => $caja->get()->first()->id,
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

    static public function getSaldo($idCaja, $fechaMovimientosInicio, $fechaMovimientosFin, $array = false, $get = null, $admin = false)
    {
        $deudas = $array || isset($get['deudas']) ? array() : 0;
        $ventas = $array || isset($get['ventas']) ? array() : 0;
        $recibos = $array || isset($get['recibos']) ? array() : [0, 0];
        $cajas = $array || isset($get['cajas']) ? array() : [0, 0];
        $movimientosAll = isset($get['movimientos']) ? array() : null;

        $arqueos = array();
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
                        array_push($movimientosAll, $movimiento);
                    }
                    if ($array || isset($get['deudas'])) {
                        if (isset($movimiento->idParent0)) {
                            if (isset($movimiento->idParent0->ordenCTPs[0]))
                                $orden = $movimiento->idParent0->ordenCTPs[0];
                            array_push($deudas, $orden);
                        }
                    } else {
                        $deudas += $movimiento->monto;
                    }
                    $total += $movimiento->monto;
                    break;
                case 1:
                    if (!empty($movimiento->ordenTrabajo)) {
                        if (isset($get['movimientos'])) {
                            array_push($movimientosAll, $movimiento);
                        }
                        if ($array || isset($get['ventas'])) {
                            array_push($ventas, OrdenesTrabajo::getOne($movimiento->ordenTrabajo));
                        } else {
                            $ventas += $movimiento->monto;
                        }
                    }
                    $total += $movimiento->monto;
                    break;
                case 2:
                    if (isset($get['movimientos'])) {
                        array_push($movimientosAll, $movimiento);
                    }
                    if ($array || isset($get['cajas'])) {
                        array_push($cajas, $movimiento);
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
                    array_push($arqueos, $movimiento);
                    $total += $movimiento->monto;
                    break;
                case 4:
                    $movimientoRecibo = DB::table(Recibo::$tables)
                        ->where('movimientoCaja', '=', $movimiento->id);
                    if ($movimientoRecibo->count() > 0) {
                        if (isset($get['movimientos'])) {
                            array_push($movimientosAll, $movimiento);
                        }
                        if ($array || isset($get['recibos'])) {
                            $tmp = $movimiento->recibos;
                            array_push($recibos, $movimiento->recibos[0]);
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
