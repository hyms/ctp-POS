<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\DetallesOrden;
use App\Models\MovimientoCaja;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use App\Models\Sucursal;
use App\Models\TipoProductos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use PhpParser\Node\Expr\Cast\Object_;
use function GuzzleHttp\Promise\all;

class ReporteController extends Controller
{
    public function get()
    {
    }

    public function placasA(Request $request)
    {
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->pluck('nombre', 'id');
        $tipo = TipoProductos::getAll();
        $validator = Validator::make($request->all(), [
            'sucursal' => 'required',
            'fecha' => 'required',
        ]);
        if ($validator->fails()) {
            return Inertia::render('Reportes/placas',
                [
                    'sucursales' => $sucursales,
                    'forms' => $request->all(),
//                    'errors' => $validator->errors(),
                    'tipoPlacas' => $tipo->pluck('nombre', 'id'),
                    'data' => ['table' => [], 'fields' => []]
                ]);
        }
        return $this->placas($request->all(),$sucursales);
    }
    public function placasV(Request $request)
    {
        $tipo = TipoProductos::getAll();
        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
        ]);
        if ($validator->fails()) {
            return Inertia::render('Reportes/placas',
                [
                    'sucursales' => (Object)[],
                    'forms' => $request->all(),
//                    'errors' => $validator->errors(),
                    'tipoPlacas' => $tipo->pluck('nombre', 'id'),
                    'data' => ['table' => [], 'fields' => []]
                ]);
        }
        $request = $request->all();
        $request['sucursal'] = Auth::user()['sucursal'];
        return $this->placas($request, [],$tipo);
    }

    private function placas($request,$sucursales,$tipo)
    {
        $data=['table' => [], 'fields' => []];
         if(!isset($request['tipoOrden'])){
             $request['tipoOrden'] = null;
         }
        $ordenes = OrdenesTrabajo::getReport(
            $request['fecha'],
            $request['fecha'],
            $request['sucursal'],
            $request['tipoOrden']);
        if(!empty($request['tipoOrden'])){
           $tipoProducto = DB::table(TipoProductos::$tables)->where('id','=',$request['tipoOrden']);
           $placas = ProductoStock::getProducts($request['sucursal'],$tipoProducto->get()->toArray());
        }
        else
        {
             $placas = ProductoStock::getProducts($request['sucursal']);
        }
        if(!empty($request['tipoOrden'])){
            $placas=$placas[$request['tipoOrden']];
        }

        foreach ($ordenes as $orden) {
            $row = [
                'fecha' => $orden->created_at,
                'cliente' => $orden->responsable,
                'orden' => ($orden->tipoOrden == null) ? $orden->correlativo : $orden->codigoServicio,
                'tipo' => $orden->tipoOrden,
                'estado' => $orden->estado,
//                    'monto' => $orden->montoVenta,
            ];

            foreach ($placas as $key => $placa) {
                $row[$placa->formato] = 0;
            }
            $row['observaciones'] = "";
            if ($orden->estado == 0 || $orden->estado == 2) {
                $orden = DetallesOrden::getOne($orden);
                foreach ($orden->detallesOrden as $detalle) {
                    $productoTmp = ProductoStock::getProduct($request['sucursal'], $detalle->stock);
                    if (!empty($productoTmp)) {
                        $row[$productoTmp->formato] += $detalle->cantidad;
                    }
                }
            } else if ($orden->estado == -1) {
                $row['observaciones'] = "<span class=\"text-danger\">Anulado</span>";
            }
            if ($orden->tipoOrden == 0) {
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
            'orden',
//                'monto'
        ];
        foreach ($placas as $row) {
            array_push($data['fields'], $row->formato);
        }
        array_push($data['fields'], 'observaciones');
        return Inertia::render('Reportes/placas',
            [
                'sucursales' => $sucursales,
                'forms' => (object)$request,
                'tipoPlacas' => $tipo->pluck('nombre', 'id'),
                'errors' => (object)[],
                'data' => $data,
            ]);
    }
    public function arqueos(Request $request)
    {
        $sucursal = Auth::user()['sucursal'];
        $arqueo = new MovimientoCaja();
        $caja = Cajas::getOne($sucursal);
        if ($request->has('fechaI') && $request->has('fechaF')) {
            $fechaI = Carbon::parse($request['fechaI']);
            $fechaF = Carbon::parse($request['fechaF']);
            $startDate = $fechaI->toDateTimeString();
            $endDate = $fechaF->toDateTimeString();
        } else {
            $startDate = Carbon::now()->toDateTimeString();
            $endDate = Carbon::now()->toDateTimeString();
        }

        $variables = Cajas::getSaldo($caja->first()->id, $startDate, $endDate, false, false);

        return Inertia::render('Reportes/arqueos',
            [
                'saldo' => $variables['saldo'],
                'arqueo' => $arqueo,
                'caja' => $caja,
                'fechaI' => $startDate,
                'fechaF' => $endDate,
                'ventas' => $variables['ventas'],
                'deudas' => $variables['deudas'],
                'recibos' => $variables['recibos'],
                'cajas' => $variables['cajas'],
            ]);
    }

    public function cajas()
    {
    }
}
