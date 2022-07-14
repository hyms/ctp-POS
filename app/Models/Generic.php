<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Generic
{
    public function __construct(
        public string $table,
        public bool $onlyBuild=false,
    )
    {
    }

    public function getAll(array $filters = [], bool $asc = false, $limit = null): Collection
    {
        $collection = DB::table($this->table);

        if (isset($filters['enable'])) {
            $collection = $collection->where('enable', $filters['enable']);
        }
        if (isset($filters['sucursal'])) {
            $collection = $collection->where('sucursal', $filters['sucursal']);
        }
        if (isset($filters['tipo'])) {
            $collection = $collection->where('tipo', $filters['tipo']);
        }
        if (isset($filters['fecha'])) {
            $fecha = Carbon::parse($filters['fecha']);
            $collection = $collection->whereBetween('created_at', [$fecha->startOfDay()->toDateTimeString(), $fecha->endOfDay()->toDateTimeString()]);
        }
        if (isset($filters['fechaI']) && isset($filters['fechaF'])) {
            $fechaI = Carbon::parse($filters['fechaI']);
            $fechaF = Carbon::parse($filters['fechaF']);
            $collection = $collection->whereBetween('created_at', [$fechaI->startOfDay()->toDateTimeString(), $fechaF->endOfDay()->toDateTimeString()]);
        }
        if (isset($filters['detalle'])) {
            $collection = $collection->where('detalle', 'like', "%{$filters['detalle']}%");
        }
        if (isset($filters['secuencia'])) {
            $collection = $collection->where('secuencia', '=', $filters['secuencia']);
        }
        if (isset($filters['nombre'])) {
            $collection = $collection->where('nombre', '=', $filters['nombre']);
        }
        if (isset($report['orden'])) {
            $collection = $collection->where('correlativo', $report['orden']);
        }
        if (isset($report['responsable'])) {
            $collection = $collection->where('responsable', $report['responsable']);
        }
        if (isset($report['cliente'])) {
            $collection = $collection->where('cliente', $report['cliente']);
        }
        if (isset($report['estado'])) {
            $collection = $collection->where('estado', $report['estado']);
        }
        if (isset($report['tipoOrden'])) {
            $collection = $collection->where('tipoOrden', $report['tipoOrden']);
        }

        if (!empty($limit)) {
            $collection = $collection->limit($limit);
        }
        $collection = $collection->whereNull('deleted_at');
        $collection = $collection->orderBy('updated_at', ($asc) ? 'asc' : 'desc');
        if($this->onlyBuild){
            return $collection;
        }
        return $collection->get();
    }
}
