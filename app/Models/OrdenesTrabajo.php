<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class OrdenesTrabajo extends Model
{
    protected $table = 'ordenesTrabajo';
    protected static $tables = 'ordenesTrabajo';
    protected $guarded = [];

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

    public static function getAll(int $sucursal = null, int $usuario = null, bool $onlyDay = false)
    {
        $ordenes = DB::table(self::$tables);
        if (!isNull($sucursal)) {
            $ordenes = $ordenes->where('sucursal', '=', $sucursal);
        }
        if (!isNull($usuario)) {
            $ordenes = $ordenes->where('userDiseñador', '=', $usuario);
        }
        if ($onlyDay) {
            $ordenes = $ordenes->whereDate('created_at', Carbon::today());
        }
        return $ordenes
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
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


}
