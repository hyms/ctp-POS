<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\Cliente;
use App\Models\DetallesOrden;
use App\Models\Export;
use App\Models\Generic;
use App\Models\MovimientoCaja;
use App\Models\MovimientoStock;
use App\Models\OrdenesTrabajo;
use App\Models\Producto;
use App\Models\ProductoStock;
use App\Models\Recibo;
use App\Models\Sucursal;
use App\Models\TipoProductos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

//use Maatwebsite\Excel\Facades\Excel;

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
        return $this->placas($request->all(), $sucursales, $tipo);
    }

    public function placasV(Request $request)
    {
        $tipo = TipoProductos::getAll();
        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
        ]);
        Inertia::share('titlePage', 'Registro diario');
        $request = $request->all();
        $data = ['table' => [], 'fields' => []];
        if (!$validator->fails()) {
            $request['sucursal'] = Auth::user()['sucursal'];
            $data = $this->getTablePlacas($request);
        }

        return Inertia::render('Reportes/placas',
            [
                'forms' => $request,
                'tipoPlacas' => $tipo->map(function ($value) {
                    return ['text' => $value->nombre, 'value' => (string)$value->id];
                }),
                'ventaReport' => true,
                'data' => $data,
            ]);
    }

    private function placas($request, $sucursales, $tipo)
    {
        $data = $this->getTablePlacas($request);
        return Inertia::render('Reportes/placas',
            [
                'sucursales' => (object)$sucursales,
                'forms' => (array)$request,
                'tipoPlacas' => $tipo->map(function ($value) {
                    return ['text' => $value->nombre, 'value' => $value->id];
                }),
                'errors' => (object)[],
                'data' => $data,
            ]);
    }

    public function getTablePlacas($request)
    {
        $data = ['table' => collect([]), 'fields' => collect([])];
        if (!isset($request['tipoOrden'])) {
            $request['tipoOrden'] = null;
        }
        if (empty($request['fechahasta'])) {
            $request['fechahasta'] = $request['fecha'];
        }
        $ordenes = OrdenesTrabajo::getReport(
            $request['fecha'],
            $request['fechahasta'],
            $request['sucursal'],
            $request['tipoOrden']);
        if (!empty($request['tipoOrden'])) {
            $tipoProducto = DB::table(TipoProductos::$tables)->where('id', '=', $request['tipoOrden']);
            $placas = ProductoStock::getProducts($request['sucursal'], $tipoProducto->get()->toArray());
        } else {
            $placas = ProductoStock::getProducts($request['sucursal']);
        }
        if (!empty($request['tipoOrden'])) {
            $placas = $placas->first(function ($item, $key) use ($request) {
                return $key == $request['tipoOrden'];
            });
        }
        foreach ($ordenes as $orden) {
            $row = [
                'fechaOrden' => Carbon::parse($orden->created_at)->format("d/m/Y H:i"),
                'fechaTrabajo' => Carbon::parse($orden->updated_at)->format("d/m/Y H:i"),
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
            switch ($orden->estado) {
                case 0:
                case 2:
                case 5:
                case 10:
                    $orden = DetallesOrden::setAllDetalle(collect([$orden]))->first();
                    foreach ($orden->detallesOrden as $detalle) {
                        $productoTmp = ProductoStock::getProduct($request['sucursal'], $detalle->stock);
                        if (!empty($productoTmp)) {
                            $row[$productoTmp->formato] += $detalle->cantidad;
                        }
                    }
                    break;
            }

            switch ($orden->estado) {
                case 2:
                    $row['observaciones'] = "<strong class=\"green--text\">Deuda</strong>";
                    break;
                case 5:
                    $row['observaciones'] = "<strong class=\"yellow--text\">Quemado</strong>";
                    break;
                case -1:
                    $row['observaciones'] = "<strong class=\"red--text\">Anulado</strong>";
                    break;
                case 10:
                    $row['observaciones'] = "<strong class=\"black--text\">Reposicion</strong>";
                    break;
            }
            if ($orden->tipoOrden == 0) {
                if (!empty($row['observaciones'])) {
                    $row['observaciones'] .= " - ";
                }
                $row['observaciones'] .= $orden->observaciones;
            }
            $data['table']->add($row);
        }
        $data['fields'] = collect([
            '#',
            'fechaOrden',
            'fechaTrabajo',
            'cliente',
            'orden',
//                'monto'
        ]);
        foreach ($placas as $row) {
            $data['fields']->add($row->formato);
        }
        $data['fields']->add('observaciones');
        return $data;
    }

    public function exportPlacas(Request $request)
    {
        $data = $this->getTablePlacas($request->all());
//        return Excel::create('regsitroPlacas', function($excel) use($data) {
//
//            $excel->sheet('Sheetname', function($sheet) use($data) {
//
//                $sheet->fromArray($data['table']);
//
//            });
//
//        })->download('xls');
        $export = new Export();
        $export->data = $data['table'];
        array_shift($data['fields']);
        $export->dataHeading = $data['fields'];
        //return Excel::download($export, 'registroPlacas.xlsx');
        return null;
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
        $sucursales = Sucursal::getSelect();
        $productosAll = ProductoStock::getProducts();
        //$productosAll = ProductoStock::getProducts(Auth::user()['sucursal']);
        $validator = Validator::make($request->all(), [
            'sucursal' => 'required',
            'fechaI' => 'required',
            'fechaF' => 'required',
        ]);
        $clientes = collect([]);
        $data = ['table' => [], 'fields' => []];
        if (!$validator->fails()) {
            $tipoBusqueda = 2;
            $clientesAll = Cliente::getAll($request['sucursal'])->toArray();
            foreach ($clientesAll as $cliente) {
                $totalCliente = 0;
                $ordenes = OrdenesTrabajo::getAll($request['sucursal'], null, null, collect(['cliente' => $cliente->id, 'fechaI' => $request['fechaI'], 'fechaF' => $request['fechaF']]));
                $ordenes->where('estado', '=', $tipoBusqueda);
                $ordenes = DetallesOrden::setAllDetalle($ordenes->get());
                $ordenes = Recibo::setRecibos($ordenes);
                $ordenes->transform(function ($item) {
                    $totalVenta = 0;
                    $totalPagado = 0;
                    foreach ($item->detallesOrden as $detalle) {
                        $totalVenta += $detalle->total;
                    }
                    foreach ($item->recibos as $recibo) {
                        $totalPagado += $recibo->monto;
                    }
                    $item->totalDeuda = $totalVenta - $totalPagado;
                    return $item;
                });
                $totalCliente += $ordenes->sum('totalDeuda');
                $total += $totalCliente;
                if ($totalCliente > 0) {
                    $clientes->add([
                        'nombreResponsable' => $cliente->nombreResponsable,
                        'ordenes' => $ordenes,
                        /*'fields' => [
                            'codigoServicio',
                            'totalDeuda',
                            [
                                'key' => 'created_at',
                                'label' => 'Fecha'
                            ],
                            'detalle'
                        ],*/
                        'mora' => $totalCliente
                    ]);
                }
            }
            $data = ['table' => $clientes->all(), 'fields' => ['nombreResponsable', 'mora', 'desde', 'hasta', 'cantidad']];
        }
        Inertia::share('titlePage', 'Mora de Clientes');
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
                ->get();
            $ingreso = DB::table(MovimientoCaja::$tables)
                ->whereNull('deleted_at')
                ->whereBetween('created_at', [$fechaI->startOfDay()->toDateTimeString(), $fechaF->endOfDay()->toDateTimeString()])
                ->where('cajaDestino', $caja->id)
                ->get();
            ///recorrer array para llenado de datos del array
            $ingreso->transform(function ($item) {
                if ($item->tipo == 0) {
                    $orden = DB::table(OrdenesTrabajo::$tables)
                        ->where('id', $item->ordenTrabajo)
                        ->get()
                        ->first();
                    if (isset($orden)) {
                        if ($orden->deleted_at) {
                            $item->observaciones = "Eliminado " . $item->observaciones;
                            $item->monto = 0;
                        } else {
                            $item->observaciones = "Orden " . (($orden->tipoOrden == null) ? "#" . $orden->correlativo : $orden->codigoServicio);
                        }
                    }
                } elseif ($item->tipo == 4) {
                    $recibo = DB::table(Recibo::$tables)
                        ->where('movimientoCaja', $item->id)
                        ->get()
                        ->first();
                    if (isset($recibo)) {
                        if ($recibo->deleted_at) {
                            $item->observaciones = "Eliminado " . $item->observaciones;
                            $item->monto = 0;
                        } else if ($recibo->codigoVenta) {
                            $orden = DB::table(OrdenesTrabajo::$tables)
                                ->where('correlativo', $recibo->codigoVenta)
                                ->where('sucursal', $recibo->sucursal)
                                ->get()
                                ->first();
                            $item->observaciones = "Pago de deuda de la Orden " . (($orden->tipoOrden == null) ? "#" . $orden->correlativo : $orden->codigoServicio);
                        } else {
                            $item->observaciones = $recibo->detalle;
                        }
                    }
                }
                $item->fecha = $item->created_at;
                return $item;
            });

            $egreso->transform(function ($item) {
                if ($item->tipo == 4) {
                    $recibo = DB::table(Recibo::$tables)
                        ->where('movimientoCaja', $item->id)
                        ->get()
                        ->first();
                    if (isset($recibo)) {
                        if ($recibo->deleted_at) {
                            $item->observaciones = "Eliminado " . $item->observaciones;
                            $item->monto = 0;
                        } else {
                            $item->observaciones = $recibo->detalle;
                        }
                    }
                }
                $item->fecha = $item->created_at;
                return $item;
            });

            $movimientos = [
                'egreso' => $egreso,
                'ingreso' => $ingreso
            ];
            $fields = ['observaciones', 'fecha', 'monto'];
        }
        $sucursales = Sucursal::getAll()->map(function ($value) {
            return ['text' => $value->nombre, 'value' => (string)$value->id];
        });
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
        Inertia::share('titlePage', 'Rendicion Diaria');
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

            $ordenes = OrdenesTrabajo::getAll($request['sucursal'], null, null, collect(['cliente' => $request['cliente'], 'fechaI' => $fechaI, 'fechaF' => $fechaF]));
            $ordenes->whereIn('estado', [0, 1, 2]);
            $ordenes = DetallesOrden::getAll($ordenes->get());
            $movimientos = $ordenes;
            $fields = ['codigoServicio', ['key' => 'created_at', 'label' => 'Fecha'], 'montoVenta', 'Acciones'];
        }
        $sucursales = Sucursal::getAll()->pluck('nombre', 'id');
        $productosAll = ProductoStock::getProducts(Auth::user()['sucursal']);
        $clientes = Cliente::getAll($request['sucursal'])->pluck('nombreResponsable', 'id');
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

    public function ordenes(Request $request)
    {
        $data = ['table' => [], 'fields' => []];
        $totals = ['venta' => [], 'deuda' => [], 'pagado' => [], 'ordenes' => [],];
        $estadoCTP = OrdenesTrabajo::estadoCTP();
        $tiposSelect = TipoProductos::getAll()->map(function ($item, $key) {
            return ['value' => (string)$item->id, 'text' => $item->nombre];
        });
        $validator = Validator::make($request->all(), [
            'sucursal' => 'required',
            'fechaI' => 'required',
            'fechaF' => 'required',
        ]);
        if (!$validator->fails()) {
            $ordenes = OrdenesTrabajo::getAll($request['sucursal'], null, null, collect($request->except(['sucursal'])), true);
            $ordenes = DetallesOrden::setAllDetalle($ordenes->get());
            $ordenes = Recibo::setRecibos($ordenes);
            $totalVentaOrden = collect([]);
            $totalDeudaOrden = collect([]);
            $totalPagadoOrden = collect([]);
            $totalesOrden = collect([]);
            $tiposSelect->each(function ($item) use ($totalVentaOrden, $totalPagadoOrden, $totalDeudaOrden) {
                $totalVentaOrden->put($item['text'], 0);
                $totalDeudaOrden->put($item['text'], 0);
                $totalPagadoOrden->put($item['text'], 0);
            });
            $totalVentaOrden->put('reposicion', 0);
            $totalDeudaOrden->put('reposicion', 0);
            $totalPagadoOrden->put('reposicion', 0);
            $estadoCTP->each(function ($item) use ($totalesOrden) {
                $totalesOrden->put($item['text'], 0);
            });
            $ordenes = $ordenes->map(function ($item, $key) use ($totalVentaOrden, $totalPagadoOrden, $totalDeudaOrden, $tiposSelect, $totalesOrden) {
                $totalVenta = 0;
                $totalPagado = 0;
                if ($item->estado === 2 || $item->estado === 0) {
                    foreach ($item->detallesOrden as $detalle) {
                        $totalVenta += $detalle->total;
                    }
                    $totalPagado = $item->montoVenta;
                }
                $item->montoVenta = $totalVenta;
                $item->totalDeuda = $totalVenta - $totalPagado;
                $item->totalPagado = $totalPagado;
                $item->estado = OrdenesTrabajo::estadoCTP($item->estado);
                $tipoSelect = $tiposSelect->firstWhere('value', '=', (string)$item->tipoOrden);
                if (isset($tipoSelect)) {
                    $totalVentaOrden[$tipoSelect['text']] += $item->montoVenta ?? 0;
                    $totalDeudaOrden[$tipoSelect['text']] += $item->totalDeuda ?? 0;
                    $totalPagadoOrden[$tipoSelect['text']] += $item->totalPagado ?? 0;
                } else {
                    $totalVentaOrden['reposicion'] += $item->montoVenta ?? 0;
                    $totalDeudaOrden['reposicion'] += $item->totalDeuda ?? 0;
                    $totalPagadoOrden['reposicion'] += $item->totalPagado ?? 0;
                }
                $totalesOrden[(string)$item->estado] += 1;
                return [
                    'codigo' => $item->codigoServicio,
                    'cliente' => $item->responsable,
                    'estado' => $item->estado,
                    'venta' => $item->montoVenta * 1,
                    'pagado' => $item->totalPagado * 1,
                    'deuda' => $item->totalDeuda * 1,
                    'fechaCreado' => $item->created_at,
                    'fechaModificado' => $item->updated_at,
                ];
            });
            $fields = [
                ['value' => 'codigo', 'text' => 'Codigo'],
                ['value' => 'cliente', 'text' => 'Cliente'],
                ['value' => 'estado', 'text' => 'Estado'],
                ['value' => 'venta', 'text' => 'Venta (Bs)'],
                ['value' => 'pagado', 'text' => 'Pagado (Bs)'],
                ['value' => 'deuda', 'text' => 'Deuda (Bs)'],
                ['value' => 'fechaCreado', 'text' => 'Fecha Creado'],
                ['value' => 'fechaModificado', 'text' => 'Fecha Modificado'],
            ];
            $data = ['table' => $ordenes, 'fields' => $fields];
            $totals = ['venta' => $totalVentaOrden->all(), 'deuda' => $totalDeudaOrden->all(), 'pagado' => $totalPagadoOrden->all(), 'ordenes' => $totalesOrden->all(),];
        }

        Inertia::share('titlePage', 'Reporte de Ordenes');
        return Inertia::render('Reportes/ordenes',
            [
                'sucursales' => Sucursal::getSelect(),
                'tipoOrdenes' => $estadoCTP,
                'tipoProducto' => $tiposSelect,
                'clientes' => Cliente::getAll()->map(function ($value) {
                    return ['value' => (string)$value->id, 'text' => $value->nombreResponsable];
                }),
                'request' => $request->all(),
                'data' => $data,
                'totals' => $totals
            ]);
    }

    public function ordenesAudit(Request $request)
    {
        $data = ['table' => [], 'fields' => []];
        $tiposSelect = TipoProductos::getAll()->map(function ($item, $key) {
            return ['value' => (string)$item->id, 'text' => $item->nombre];
        });

        $validator = Validator::make($request->all(), [
            'sucursal' => 'required',
            'fechaI' => 'required',
            'fechaF' => 'required',
        ]);
        if (!$validator->fails()) {
            $ordenes = OrdenesTrabajo::getAll($request['sucursal'], null, null, collect($request->except(['sucursal'])), true);
            $ordenes = DetallesOrden::setAllDetalle($ordenes->get());
            $ordenes = Recibo::setRecibos($ordenes);
            $productosAll = ProductoStock::getProducts($request['sucursal']);

            $reporte = Collection::empty();
            $ordenes->each(function ($item, $key) use ($reporte, $tiposSelect, $productosAll) {
                $totalVenta = 0;
                $totalPagado = 0;
                if ($item->estado === 2 || $item->estado === 0) {
                    foreach ($item->detallesOrden as $detalle) {
                        $totalVenta += $detalle->total;
                    }
                    $totalPagado = $item->montoVenta;
                }
                $item->montoVenta = $totalVenta;
                $item->totalDeuda = $totalVenta - $totalPagado;
                $item->totalPagado = $totalPagado;
                $item->estado = OrdenesTrabajo::estadoCTP($item->estado);
                $item->usuario = User::where('id', '=', $item->userDiseñador)->first()?->username;
                $item->vendedor = User::where('id', '=', $item->userVenta)->first()?->username;
                $tipoSelect = $tiposSelect->firstWhere('value', '=', (string)$item->tipoOrden);

                foreach ($item->detallesOrden as $value) {
                    $producto = $productosAll->first(function ($item) use ($value) {
                        return $item->id === $value->stock;
                    });
                    $reporte->add([
                        'codigo' => $item->codigoServicio,
                        'tipoOrden' => $tipoSelect['text'] ?? 'reposicion',
                        'estado' => $item->estado,
                        'fechaCreado' => $item->created_at,
                        'fechaModificado' => $item->updated_at,
                        'venta' => $item->montoVenta * 1,
                        'pagado' => $item->totalPagado * 1,
                        'deuda' => $item->totalDeuda * 1,
                        'cliente' => $item->responsable,
                        'diseñador' => $item->usuario,
                        'vendedor' => $item->vendedor,

                        'producto' => $producto?->formato,
                        'costoProducto' => $value->costo * 1,
                        'cantidadProducto' => $value->cantidad * 1,
                        'totalProducto' => $value->total * 1,

                    ]);
                }
            });
            $fields = [
                ['value' => 'codigo', 'text' => 'Codigo'],
                ['value' => 'tipoOrden', 'text' => 'Tipo de Orden'],
                ['value' => 'estado', 'text' => 'Estado'],
                ['value' => 'fechaCreado', 'text' => 'Fecha Creado'],
                ['value' => 'fechaModificado', 'text' => 'Fecha Modificado'],
                ['value' => 'venta', 'text' => 'Monto Venta'],
                ['value' => 'pagado', 'text' => 'Monto Pagado'],
                ['value' => 'deuda', 'text' => 'Monto Deuda'],
                ['value' => 'cliente', 'text' => 'Cliente'],
                ['value' => 'diseñador', 'text' => 'Diseñador'],
                ['value' => 'vendedor', 'text' => 'Vendedor'],

                ['value' => 'producto', 'text' => 'Producto'],
                ['value' => 'costoProducto', 'text' => 'Costo de Producto'],
                ['value' => 'cantidadProducto', 'text' => 'Cnt. de Producto'],
                ['value' => 'totalProducto', 'text' => 'Monto de Producto'],
            ];
            $data = ['table' => $reporte, 'fields' => $fields];
        }

        Inertia::share('titlePage', 'Reporte de Ordenes');
        return Inertia::render('Reportes/ordenesAudit',
            [
                'sucursales' => Sucursal::getSelect(),
                'tipoProducto' => $tiposSelect,
                'request' => $request->all(),
                'data' => $data,
            ]);
    }

    public function movimientosInventario(Request $request)
    {
        $data = ['table' => [], 'fields' => []];
        $tiposSelect = TipoProductos::getAll()->map(function ($item, $key) {
            return ['value' => (string)$item->id, 'text' => $item->nombre];
        });

        $validator = Validator::make($request->all(), [
            'sucursal' => 'required',
            'fechaI' => 'required',
            'fechaF' => 'required',
        ]);
        if (!$validator->fails()) {
            $productos = Producto::all();
            $stocks = ProductoStock::getAll(Auth::user()['sucursal']);
            $stocksId = $stocks->pluck('id')->toArray();
            $movimientos = new Generic(MovimientoStock::$tables);
            $movimientos->isDelete = false;
            $movimientos->onlyBuild = true;
            $movimientos = $movimientos->getAll($request->except('sucursal'));
            $movimientos = $movimientos->where(function ($query) use ($stocksId) {
                $query->whereIn('stockDestino', $stocksId)
                    ->orWhereIn('stockOrigen', $stocksId);
            })->get();
            $reporte = $movimientos->map(function ($value, $key) use ($productos, $stocks) {
                $producto = $productos->first(function ($item) use ($value) {
                    return $item->id === $value->producto;
                });

                $tipoMovimiento = "-";
                if (!empty($value->stockOrigen)) {
                    $tipoMovimiento = "Egreso";
                }
                if (!empty($value->stockDestino)) {
                    $tipoMovimiento = "Ingreso";
                }
                if(!empty($value->detalleOrden)) {
                    $detalles = DB::table(DetallesOrden::$tables)->where('id', $value->detalleOrden)->get()->first();
                    $orden = DB::table(OrdenesTrabajo::$tables)->where('id', $detalles->ordenTrabajo)->get()->first();
                    $value->observaciones.= " codigo:{$orden->codigoServicio}";
                }
                return [
                    'producto' => "{$producto->formato} ({$producto->dimension})",
                    'tipoMovimiento' => $tipoMovimiento,
                    'detalle' => $value->observaciones,
                ];
            });
            $fields = [
                ['value' => 'producto', 'text' => 'Producto'],
                ['value' => 'tipoMovimiento', 'text' => 'Tipo Movimiento'],
                ['value' => 'detalle', 'text' => 'Detalle'],
            ];
            $data = ['table' => $reporte, 'fields' => $fields];
        }

        Inertia::share('titlePage', 'Reporte Movimiento de Inventario');
        return Inertia::render('Reportes/ordenesAudit',
            [
                'sucursales' => Sucursal::getSelect(),
                'tipoProducto' => $tiposSelect,
                'request' => $request->all(),
                'data' => $data,
            ]);
    }
}
