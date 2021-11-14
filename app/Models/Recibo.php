<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Recibo extends Model
{
    use HasFactory;

    protected $table = 'recibos';
    public static string $tables = 'recibos';
    protected $guarded = [];
    use SoftDeletes;

    //tipo recibo 0:ingreso, 1:egreso
    public static function guardarDeuda(array $request, int $idcaja, int $ordenTrabajo = null)
    {
        $idMovimiento = DB::table(MovimientoCaja::$tables)
            ->insertGetId([
                'cajaOrigen' => null,
                'cajaDestino' => $idcaja,
                'tipo' => 4,
                'monto' => $request['monto'],
                'observaciones' => "Pago de Deuda",
                'ordenTrabajo' => !empty($ordenTrabajo) ? $ordenTrabajo : "",
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        DB::transaction(function () use ($request, $idMovimiento) {
            $secuencia = 1;
            $ot = DB::table(self::$tables)
                ->where('sucursal', '=', $request['sucursal'])
                ->where('tipo', '=', 0)
                ->orderBy('secuencia', 'desc')
                ->limit(1);
            if ($ot->count() > 0) {
                $secuencia = $ot->get()->first()->secuencia + 1;
            }
            $request['secuencia'] = $secuencia;
            $request['movimientoCaja'] = $idMovimiento;
            DB::table(self::$tables)
                ->insertGetId($request);
        });
    }

    public static function guardar(array $request, int $idcaja, int $tipo)
    {
        $idMovimiento = DB::table(MovimientoCaja::$tables)
            ->insertGetId([
                'cajaOrigen' => ($tipo) ? $idcaja : null,
                'cajaDestino' => ($tipo) ? null : $idcaja,
                'tipo' => 4,
                'monto' => $request['monto'],
                'observaciones' => $request['detalle'],
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        DB::transaction(function () use ($request, $idMovimiento, $tipo) {
            $secuencia = 1;
            $ot = DB::table(self::$tables)
                ->where('sucursal', '=', $request['sucursal'])
                ->orderBy('secuencia', 'desc')
                ->where('tipo', '=', $tipo)
                ->limit(1);
            if ($ot->count() > 0) {
                $secuencia = $ot->get()->first()->secuencia + 1;
            }
            $request['secuencia'] = $secuencia;
            $request['movimientoCaja'] = $idMovimiento;
            DB::table(self::$tables)
                ->insertGetId($request);
        });
    }

    public static function getAll(int $sucursal, int $tipo, array $report = [])
    {
        $recibos = DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal)
            ->orderBy('created_at', 'desc')
            ->whereNull('deleted_at');
        if(isset($report)) {
            if (isset($report['fechaI']) && isset($report['fechaF'])) {
                $fechaI = Carbon::parse($report['fechaI']);
                $fechaF = Carbon::parse($report['fechaF']);
                $recibos = $recibos->whereBetween('created_at', [$fechaI->startOfDay()->toDateTimeString(), $fechaF->endOfDay()->toDateTimeString()]);
            }
            if (isset($report['detalle'])) {
                $recibos = $recibos->where('detalle', 'like', "%{$report['detalle']}%");
            }
            if (isset($report['secuencia'])) {
                $recibos = $recibos->where('secuencia', '=', $report['secuencia']);
            }
            if (isset($report['nombre'])) {
                $recibos = $recibos->where('nombre', '=', $report['nombre']);
            }
        }
        else{
            $recibos = $recibos->limit(500);
        }
        $recibos = $recibos->where('tipo', '=', $tipo);
        return $recibos->get();
    }

    public static function getAllOrdenes(array $ordenes)
    {
        if (!empty($ordenes)) {
            foreach ($ordenes as $key => $item) {
                $detalle = DB::table(self::$tables)
                    ->where('orden', '=', $item->id)
                    ->get()->toArray();
                $ordenes[$key]->recibos = $detalle;
            }
        }
        return $ordenes;
    }
}
