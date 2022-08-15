<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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
                'tipo' => MovimientoCaja::tipoMovimiento('recibos'),
                'monto' => $request['monto'],
                'observaciones' => "Pago de Deuda",
                'ordenTrabajo' => !empty($ordenTrabajo) ? $ordenTrabajo : "",
                'user' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        DB::transaction(function () use ($request, $idMovimiento) {
            $request['secuencia'] = self::getSecuencia($request['sucursal'], 0);
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
                'tipo' => MovimientoCaja::tipoMovimiento('recibos'),
                'monto' => $request['monto'],
                'observaciones' => $request['detalle'],
                'user' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        DB::transaction(function () use ($request, $idMovimiento, $tipo) {
            $request['secuencia'] = self::getSecuencia($request['sucursal'], $tipo);
            $request['movimientoCaja'] = $idMovimiento;
            DB::table(self::$tables)
                ->insertGetId($request);
        });
    }

    public static function getAll(int $sucursal, int $tipo, array $report = [])
    {
        $recibos = new Generic(self::$tables);
        if (!empty($report)) {
            $report['sucursal'] = $sucursal;
            $report['tipo'] = $tipo;
            return $recibos->getAll($report);
        }
        return $recibos->getAll(['sucursal' => $sucursal, 'tipo' => $tipo], false, 500);
    }

    public static function setRecibos(Collection $ordenes): Collection
    {
        $ordenes->transform(function ($item, $key) {
            $item->recibos = DB::table(self::$tables)
                ->where('orden', $item->id)
                ->get()->toArray();
            return $item;
        });

        return $ordenes;
    }

    private static function getSecuencia($sucursal, $tipo): int
    {
        $secuencia = 1;
        $ot = DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal)
            ->where('tipo', '=', $tipo)
            ->orderBy('secuencia', 'desc')
            ->limit(1);
        if ($ot->count() > 0) {
            $secuencia = $ot->get()->first()->secuencia + 1;
        }
        return $secuencia;
    }
}
