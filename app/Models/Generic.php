<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Generic
{
    public function __construct(
        public string $table,
        public bool $onlyBuild = false,
        public string $orderBy = 'updated_at',
        public string $filterDate = 'created_at',
    )
    {
    }

    public function getAll(array $filters = [], bool $asc = false, $limit = null): Collection|Builder
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
        if (isset($filters['secuencia'])) {
            $collection = $collection->where('secuencia', '=', $filters['secuencia']);
        }
        if (isset($filters['nombre'])) {
            $collection = $collection->where('nombre', '=', $filters['nombre']);
        }
        if (isset($filters['orden'])) {
            $collection = $collection->where('correlativo', $filters['orden']);
        }
        if (isset($filters['responsable'])) {
            $collection = $collection->where('responsable', $filters['responsable']);
        }
        if (isset($filters['cliente'])) {
            $collection = $collection->where('cliente', $filters['cliente']);
        }
        if (isset($filters['estado'])) {
            $collection = $collection->where('estado', $filters['estado']);
        }
        if (isset($filters['tipoOrden'])) {
            $collection = $collection->where('tipoOrden', $filters['tipoOrden']);
        }
        if (isset($filters['producto'])) {
            $collection = $collection->where('producto', $filters['producto']);
        }
        if (isset($filters['observaciones'])) {
            $collection = $collection->where('observaciones', 'like', "%{$filters['observaciones']}%");
        }
        if (isset($filters['detalle'])) {
            $collection = $collection->where('detalle', 'like', "%{$filters['detalle']}%");
        }
        if (!empty($limit)) {
            $collection = $collection->limit($limit);
        }
        if (isset($filters['fecha'])) {
            $fecha = Carbon::parse($filters['fecha']);
            $collection = $collection->whereBetween($this->filterDate, [$fecha->startOfDay()->toDateTimeString(), $fecha->endOfDay()->toDateTimeString()]);
        }
        if (isset($filters['fechaI']) && isset($filters['fechaF'])) {
            $fechaI = Carbon::parse($filters['fechaI']);
            $fechaF = Carbon::parse($filters['fechaF']);
            $collection = $collection->whereBetween($this->filterDate, [$fechaI->startOfDay()->toDateTimeString(), $fechaF->endOfDay()->toDateTimeString()]);
        }
        $collection = $collection->whereNull('deleted_at');
        $collection = $collection->orderBy($this->orderBy, ($asc) ? 'asc' : 'desc');

        return $this->onlyBuild ? $collection : $collection->get();
    }
}
