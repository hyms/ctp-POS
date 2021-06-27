<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\Cliente;
use App\Models\DetallesOrden;
use App\Models\MovimientoCaja;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use App\Models\Recibo;
use App\Models\Sucursal;
use App\Models\TipoProductos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function resumen(Request $request)
    {
        $fechaInicio = null;
        $fechaFin = null;
        if (count($request->all()) == 0) {
            $fechaInicio = Carbon::now();
            $fechaFin = Carbon::now();
        } else {
            $fechaInicio = Carbon::parse($request['fechaI']);
            $fechaFin = Carbon::parse($request['fechaF']);
        }
        $sucursales = Sucursal::getAll();
        $totalOrdenes = array();
        foreach ($sucursales as $key => $sucursal) {
            $totalOrdenes[$key] = ['name' => $sucursal->nombre, 'value' => []];
            $tipos = ProductoStock::getTipos($sucursal->id);
            foreach (OrdenesTrabajo::estadoCTP() as $keyEstado => $estado) {
                $totalOrdenes[$key]['value'][$keyEstado] = ['name' => $estado, 'value' => []];
                foreach ($tipos as $tipo) {
                    $tipoProducto = DB::table(TipoProductos::$tables)->where('id', '=', $tipo->tipo)->first();
                    $total = DB::table(OrdenesTrabajo::$tables)
                        ->where('tipoOrden', '=', $tipoProducto->id)
                        ->where('estado', '=', $keyEstado)
                        ->where('sucursal', '=', $sucursal->id)
                        ->whereBetween('updated_at', [$fechaInicio->startOfDay()->toDateTimeString(), $fechaFin->endOfDay()->toDateTimeString()])
                        ->count();
                    $totalOrdenes[$key]['value'][$keyEstado]['value'][$tipoProducto->id] = ['name' => $tipoProducto->nombre, 'value' => $total];
                }
            }
        }

        return Inertia::render('Reportes/resumen',
            [
                'totalOrdenes' => $totalOrdenes,
                'fechaI' => $fechaInicio,
                'fechaF' => $fechaFin
            ]);
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
                    'tipoPlacas' => $tipo->pluck('nombre', 'id'),
                    'data' => ['table' => [], 'fields' => []]
                ]);
        }
        return $this->placas($request->all(), $sucursales);
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
                    'sucursales' => (object)[],
                    'forms' => $request->all(),
                    'tipoPlacas' => $tipo->pluck('nombre', 'id'),
                    'data' => ['table' => [], 'fields' => []]
                ]);
        }
        $request = $request->all();
        $request['sucursal'] = Auth::user()['sucursal'];
        return $this->placas($request, [], $tipo);
    }

    private function placas($request, $sucursales, $tipo)
    {
        $data = ['table' => [], 'fields' => []];
        if (!isset($request['tipoOrden'])) {
            $request['tipoOrden'] = null;
        }
        $ordenes = OrdenesTrabajo::getReport(
            $request['fecha'],
            $request['fecha'],
            $request['sucursal'],
            $request['tipoOrden']);
        if (!empty($request['tipoOrden'])) {
            $tipoProducto = DB::table(TipoProductos::$tables)->where('id', '=', $request['tipoOrden']);
            $placas = ProductoStock::getProducts($request['sucursal'], $tipoProducto->get()->toArray());
        } else {
            $placas = ProductoStock::getProducts($request['sucursal']);
        }
        if (!empty($request['tipoOrden'])) {
            $placas = $placas[$request['tipoOrden']];
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
            if ($orden->estado == 0 || $orden->estado == 2 || $orden->estado == 5) {
                $orden = DetallesOrden::getOne($orden);
                foreach ($orden->detallesOrden as $detalle) {
                    $productoTmp = ProductoStock::getProduct($request['sucursal'], $detalle->stock);
                    if (!empty($productoTmp)) {
                        $row[$productoTmp->formato] += $detalle->cantidad;
                    }
                }

            }
            switch ($orden->estado) {
                case 2:
                    $row['observaciones'] = "<span class=\"text-primary\">Deuda</span>";
                    break;
                case 5:
                    $row['observaciones'] = "<span class=\"text-warning\">Quemado</span>";
                    break;
                case -1:
                    $row['observaciones'] = "<span class=\"text-danger\">Anulado</span>";
                    break;
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

    public function clientes(Request $request)
    {
        $total = 0;
        $sucursales = Sucursal::getAll()->pluck('nombre', 'id');
        $productosAll = ProductoStock::getProducts(Auth::user()['sucursal']);
        $validator = Validator::make($request->all(), [
            'sucursal' => 'required',
            'fechaI' => 'required',
            'fechaF' => 'required',
        ]);
        $clientes = [];
        $data = ['table' => [], 'fields' => []];
        if (!$validator->fails()) {
            $tipoBusqueda = 2;
            $clientesAll = Cliente::getAll($request['sucursal'])->toArray();
            foreach ($clientesAll as $key => $cliente) {
                $totalCliente = 0;
                $ordenes = OrdenesTrabajo::getAll($request['sucursal'], null, ['cliente' => $cliente->id, 'fechaI' => $request['fechaI'], 'fechaF' => $request['fechaF']]);
                $ordenes->where('estado', '=', $tipoBusqueda);
                $ordenes = DetallesOrden::getAll($ordenes->get());
                $ordenes = Recibo::getAllOrdenes($ordenes);
                foreach ($ordenes as $keyO => $orden) {
                    $totalVenta = 0;
                    $totalPagado = 0;
                    foreach ($orden->detallesOrden as $detalle) {
                        $totalVenta += $detalle->total;
                    }
                    foreach ($orden->recibos as $recibo) {
                        $totalVenta += $recibo->monto;
                    }
                    $totalCliente += $totalVenta - $totalPagado;
                    $ordenes[$keyO]->totalDeuda = $totalVenta - $totalPagado;
                }
                $total += $totalCliente;
                if ($totalCliente > 0) {
                    array_push(
                        $clientes,
                        [
                            'nombreResponsable'=>$cliente->nombreResponsable,
                            'ordenes' => $ordenes,
                            'fields' => [
                                'codigoServicio',
                                'totalDeuda',
                                [
                                    'key' => 'created_at',
                                    'label' => 'Fecha'
                                ],
                                'detalle'
                            ],
                            'mora' => $totalCliente
                        ]
                    );
                }
            }
            $data = ['table' => $clientes, 'fields' => ['nombreResponsable', 'mora']];
        }
        return Inertia::render('Reportes/mora',
            [
                'sucursales' => $sucursales,
                'productos' => $productosAll,
                'request' => (object)$request,
                'clientes' => $clientes,
                'total' => $total,
                'data' => $data
            ]);
    }
}
