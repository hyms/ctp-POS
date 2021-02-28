<?php

namespace App\Http\Controllers;

use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function get()
    {
    }

    public function placas(Request $request)
    {
        $data = ['table' => [], 'fields' => []];
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->pluck('nombre', 'id');
        if (!empty($request->all())) {

            $validator = Validator::make($request->all(), [
                'sucursal' => 'required',
                'fecha' => 'required'
            ]);
            if ($validator->fails()) {
                return Inertia::render('Reportes/placas',
                    [
                        'sucursales' => $sucursales,
                        'forms' => $request->all(),
                        'errors' => $validator->errors(),
                        'data' => $data
                    ]);
            }
            $ordenes = OrdenesTrabajo::getReport(
                $request['fecha'],
                $request['sucursal'],
                isset($request['tipoOrden']) ? $request['tipoOrden'] : null);
            $placas = ProductoStock::getProducts($request['sucursal']);
            $tipo = [
                0 => "Orden de Trabajo",
                1 => "Orden Interna",
                2 => "Reposicion",
            ];

            foreach ($ordenes as $orden) {
//            if ($orden->tipoOrden == 0) {
//                if ($orden->estado == 1)
//                    continue;
//            }
                $row = [
                    'fecha' => $orden->created_at,
                    'cliente' => ((empty($orden->cliente)) ? $orden->responsable : ""),//$orden->cliente->nombreNegocio),
                    'orden' => ($orden->tipoOrden == 0) ? $orden->correlativo : $orden->codigoServicio,
                    'tipo' => ($orden->tipoOrden != null) ? $tipo[$orden->tipoOrden] : "",
                    'estado' => $orden->estado
                ];
                foreach ($placas as $key => $placa) {
                    $row[$placa->formato] = 0;
                }
                $row['observaciones'] = "";
                if ($orden->estado >= 0) {
                    $orden = DetallesOrden::getOne($orden);
                    foreach ($orden->detallesOrden as $detalle) {
                        $productoTmp = ProductoStock::getProduct($request['sucursal'], $detalle->stock);
                        if (!empty($productoTmp)) {
                            $row[$productoTmp->formato] += $detalle->cantidad;
                        }
                    }
                } else {
                    $row['observaciones'] = "<span class=\"text-danger\">Anulado</span>";
                }
                if ($orden->tipoOrden != 0) {
//                if ($orden->tipoOrden == 2) {
//                    if (!empty($row['observaciones']))
//                    {$row['observaciones'] = $row['observaciones'] . "-";}
//                    $row['observaciones'] = $row['observaciones'] . "<span class=\"text-warning\">" . ((is_array(SGOperation::tiposReposicion($orden->tipoRepos))) ? '' : SGOperation::tiposReposicion($orden->tipoRepos)) . "</span>" . ((empty($orden->attribuible)) ? "" : "-" . $orden->attribuible);
//                    $row['observaciones'] = $row['observaciones'] . "- <span class=\"text-info\">Orden ".((empty($orden->fk_idParent))?$orden->codDependiente:$orden->fkIdParent->correlativo). "</span>";
//                }
                    if (!empty($row['observaciones'])) {
                        $row['observaciones'] .= "-";
                    }
                    $row['observaciones'] .= $orden->observaciones;
                }
                array_push($data['table'], $row);
            }
            $data['fields'] = [
                '#',
                'fecha',
                'cliente',
                'orden'
            ];
            foreach ($placas as $row) {
                array_push($data['fields'], $row->formato);
            }
            array_push($data['fields'], 'observaciones');
        }
        return Inertia::render('Reportes/placas',
            [
                'sucursales' => $sucursales,
                'forms' => (object)$request->all(),
                'errors' => (object)[],
                'data' => $data,
            ]);
    }

    public function cajas()
    {
    }
}
