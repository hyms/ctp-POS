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
                'orden' => ($orden->tipoOrden == null && $orden->estado != 10) ? $orden->correlativo : $orden->codigoServicio,
                'tipo' => $orden->tipoOrden,
                'estado' => $orden->estado,
//                    'monto' => $orden->montoVenta,
            ];

            foreach ($placas as $placa) {
                $row[$placa->formato] = 0;
            }
            $row['observaciones'] = "";
            $i = $orden->estado;
            if ($i == 0 || $i == 2 || $i == 5 || $i == 10) {
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
                case 10:
                    $row['observaciones'] = "<span class=\"text-info\">Reposicion</span>";
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
                    $row['observaciones'] .= " - ";
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
            foreach ($clientesAll as $cliente) {
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
                        $totalPagado += $recibo->monto;
                    }
                    $totalCliente += $totalVenta - $totalPagado;
                    $ordenes[$keyO]->totalDeuda = $totalVenta - $totalPagado;
                }
                $total += $totalCliente;
                if ($totalCliente > 0) {
                    array_push(
                        $clientes,
                        [
                            'nombreResponsable' => $cliente->nombreResponsable,
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
            $data = ['table' => $clientes, 'fields' => ['nombreResponsable', 'mora','desde','hasta','cantidad']];
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

    private function rendicion(Request $request, bool $isAdmin)
    {
        $movimientos = ['egreso' => [], 'ingreso' => []];
        $fields = [];
        if ($isAdmin) {
            $validator = Validator::make($request->all(), [
                'sucursal' => 'required',
                'fechaI' => 'required',
                'fechaF' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'fechaI' => 'required',
            ]);
            $request['sucursal'] = Auth::user()['sucursal'];
        }
        if (!$validator->fails()) {
            $fechaI = Carbon::parse($request['fechaI']);
            $fechaF = $isAdmin ? Carbon::parse($request['fechaF']) : $fechaI;

            $caja = Cajas::getAll($request['sucursal'])->first();
            $egreso = DB::table(MovimientoCaja::$tables)
                ->whereNull('deleted_at')
                ->whereBetween('created_at', [$fechaI->startOfDay()->toDateTimeString(), $fechaF->endOfDay()->toDateTimeString()])
                ->where('cajaOrigen', $caja->id)
                ->get()
                ->toArray();
            $ingreso = DB::table(MovimientoCaja::$tables)
                ->whereNull('deleted_at')
                ->whereBetween('created_at', [$fechaI->startOfDay()->toDateTimeString(), $fechaF->endOfDay()->toDateTimeString()])
                ->where('cajaDestino', $caja->id)
                ->get()
                ->toArray();
            ///recorrer array para llenado de datos del array
            foreach ($ingreso as $key => $item) {
                if ($item->tipo == 0) {
                    $orden = DB::table(OrdenesTrabajo::$tables)
                        ->where('id', $item->ordenTrabajo)
                        ->get()
                        ->first();
                    if ($orden->deleted_at) {
                        $ingreso[$key]->observaciones = "Eliminado " . $ingreso[$key]->observaciones;
                        $ingreso[$key]->monto = 0;
                    } else {
                        $ingreso[$key]->observaciones = "Orden " . (($orden->tipoOrden == null) ? "#" . $orden->correlativo : $orden->codigoServicio);
                    }
                } elseif ($item->tipo == 4) {
                    $recibo = DB::table(Recibo::$tables)
                        ->where('movimientoCaja', $item->id)
                        ->get()
                        ->first();
                    if ($recibo->deleted_at) {
                        $ingreso[$key]->observaciones = "Eliminado " . $ingreso[$key]->observaciones;
                        $ingreso[$key]->monto = 0;
                    } else if ($recibo->codigoVenta) {
                        $orden = DB::table(OrdenesTrabajo::$tables)
                            ->where('correlativo', $recibo->codigoVenta)
                            ->where('sucursal', $recibo->sucursal)
                            ->get()
                            ->first();
                        $ingreso[$key]->observaciones = "Pago de deuda de la Orden " . (($orden->tipoOrden == null) ? "#" . $orden->correlativo : $orden->codigoServicio);
                    } else {
                        $ingreso[$key]->observaciones = $recibo->detalle;
                    }
                }
            }
            foreach ($egreso as $key => $item) {
                if ($item->tipo == 4) {
                    $recibo = DB::table(Recibo::$tables)
                        ->where('movimientoCaja', $item->id)
                        ->get()
                        ->first();
                    if ($recibo->deleted_at) {
                        $egreso[$key]->observaciones = "Eliminado " . $egreso[$key]->observaciones;
                        $egreso[$key]->monto = 0;
                    } else {
                        $egreso[$key]->observaciones = $recibo->detalle;
                    }
                }
            }

            $movimientos = [
                'egreso' => $egreso,
                'ingreso' => $ingreso
            ];
            $fields = ['observaciones', ['key' => 'created_at', 'label' => 'Fecha'], 'monto'];
        }
        $sucursales = Sucursal::getAll()->pluck('nombre', 'id');
        $data = ['table' => $movimientos, 'fields' => $fields];
        return Inertia::render('Reportes/rendicionDiaria',
            [
                'request' => (object)$request,
                'data' => $data,
                'admin' => $isAdmin,
                'sucursales' => $sucursales
            ]);
    }

    public function rendicionDiaria(Request $request)
    {
        return $this->rendicion($request, false);
    }

    public function rendicionDiariaAdm(Request $request)
    {
        return $this->rendicion($request, true);
    }

    public function reporteCliente(Request $request, bool $isAdm)
    {
        $movimientos = [];
        $fields = [];
        if ($isAdm) {
            $validator = Validator::make($request->all(), [
                'sucursal' => 'required',
                'cliente' => 'required',
                'fechaI' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'fechaI' => 'required',
                'fechaF' => 'required',
                'cliente' => 'required',
            ]);
            $request['sucursal'] = Auth::user()['sucursal'];
        }
        if (!$validator->fails()) {
            $fechaI = Carbon::parse($request['fechaI']);
            $fechaF = Carbon::parse($request['fechaF']);

            $ordenes = OrdenesTrabajo::getAll($request['sucursal'], null, ['cliente' => $request['cliente'], 'fechaI' => $fechaI, 'fechaF' =>$fechaF]);
            $ordenes->whereIn('estado',[0,1,2]);
            $ordenes = DetallesOrden::getAll($ordenes->get());
            $movimientos=$ordenes;
            $fields=['codigoServicio', ['key' => 'created_at', 'label' => 'Fecha'], 'montoVenta','Acciones'];
        }
        $sucursales = Sucursal::getAll()->pluck('nombre', 'id');
        $productosAll = ProductoStock::getProducts(Auth::user()['sucursal']);
        $clientes = Cliente::getAll($request['sucursal'])->pluck('nombreResponsable','id');
        $data = ['table' => $movimientos, 'fields' => $fields];
        return Inertia::render('Reportes/clientes',
            [
                'request' => (object)$request,
                'data' => $data,
                'admin' => $isAdm,
                'clientes' => $clientes,
                'sucursales' => $sucursales,
                'productosAll' => $productosAll
            ]);
    }

    public function cliente(Request $request)
    {
        return $this->reporteCliente($request, false);
    }

    public function clienteAdm(Request $request)
    {
        return $this->reporteCliente($request, true);
    }


}
