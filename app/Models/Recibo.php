<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Recibo extends Model
{
    use HasFactory;

    protected $table = 'recibos';
    public static $tables = 'recibos';
    protected $guarded = [];
    use SoftDeletes;

    //tipo recibo 0:ingreso, 1:egreso
    public static function getCodigo(int $sucursal)
    {
        $correlativo = 1;
        $ot = DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal)
            ->orderBy('secuencia', 'desc')
            ->limit(1);
        if ($ot->count() > 0) {
            $correlativo = $ot->get()[0]->secuencia + 1;
        }
        return $correlativo;
    }

    public static function guardarDeuda(array $request, int $idcaja, int $ordenTrabajo = null)
    {
        $secuencia = self::getCodigo($request['sucursal']);
        $idMovimiento = DB::table(MovimientoCaja::$tables)
            ->insertGetId([
                'cajaOrigen' => null,
                'cajaDestino' => $idcaja,
                'tipo' => 0,
                'monto' => $request['monto'],
                'observaciones' => "venta de insumos",
                'ordenTrabajo' => !empty($ordenTrabajo) ? $ordenTrabajo : "",
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        $request['secuencia'] = $secuencia;
        $request['movimientoCaja'] = $idMovimiento;
        DB::table(self::$tables)
            ->insertGetId($request);
    }

    public static function getAll(int $sucursal)
    {
        $recibos = DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal);

        return $recibos->get();
    }
}
