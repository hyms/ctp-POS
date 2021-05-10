<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class OrdenesController extends Controller
{
    public function index()
    {
        return Inertia::render('Index');
    }

    private function get(array $estado, string $component, array $report, bool $venta = false)
    {
        $ordenes = OrdenesTrabajo::getAll(Auth::user()['sucursal'], null, $report);
        $ordenes = $ordenes->whereIn('estado', $estado);
        $ordenes = DetallesOrden::getAll($ordenes->get());
        $estados = OrdenesTrabajo::estadoCTP();
        $productos = ProductoStock::getProducts(Auth::user()['sucursal']);
        return Inertia::render($component, [
            'ordenes' => $ordenes,
            'productos' => $productos,
            'estados' => $estados,
            'isVenta' => $venta,
            'report' => $report
        ]);
    }

    public function getAll()
    {
        return self::get([1], 'Ordenes/tabla', []);
    }

    public function getAllMora(Request $request)
    {
        return self::get([2], 'Ordenes/tabla', [], true);
    }

    public function getAllVenta()
    {
        return self::get([1], 'Ordenes/tabla', [], true);
    }

    public function getListVenta(Request $request)
    {
        return self::get([-1, 0, 1, 2],
            'Ordenes/tablaReporte',
            (!empty($request->get('orden')) || !empty($request->get('fecha'))) ? $request->all() : [],
            (Auth::user()->role >= 0 && Auth::user()->role <= 2));
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'responsable' => 'required',
                'telefono' => 'required',
                'productos' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            //armar orden
            $orden = array();
            $id = null;
            if (!isset($request['id'])) {
                $orden['sucursal'] = Auth::user()['sucursal'];
                $orden['estado'] = 1;
                $orden['correlativo'] = OrdenesTrabajo::getCorrelativo(Auth::user()['sucursal']);
            } else {
                $id = $request['id'];
            }
            $orden['userDiseñador'] = Auth::user()['id'];
            $orden['responsable'] = $request['responsable'];
            $orden['telefono'] = $request['telefono'];
            $orden['observaciones'] = !empty($request['observaciones']) ? $request['observaciones'] : "";
            $orden['cliente'] = (!empty($request['cliente']))
                ? $request['cliente']
                : Cliente::newCliente($request['responsable'], $request['telefono'], $orden['sucursal']);
            //armar detalleOrden
            $detalle = array();
            $orden['montoVenta'] = 0;
            $products = json_decode($request['productos'], true);
            foreach ($products as $item) {
                $tmp = array();
                $tmp['sucursal'] = Auth::user()['sucursal'];
                $tmp['producto'] = $item['producto'];
                $tmp['stock'] = $item['id'];
                $tmp['cantidad'] = $item['cantidad'];
                $tmp['costo'] = !empty($item['costo']) ? $item['costo'] : 0;
                $tmp['total'] = $tmp['cantidad'] * $tmp['costo'];
                $orden['montoVenta'] += $tmp['total'];
                array_push($detalle, $tmp);
            }
            OrdenesTrabajo::newOrden($orden, $detalle, $id);
            return response()->json(["status" => 0, 'path' => 'ordenes']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $Cliente = OrdenesTrabajo::find($id);
        if (isset($Cliente)) {
            $Cliente->estado = -1;
            $Cliente->updated_at = now();
            $Cliente->save();
        }
        return back()->withInput();
    }

    public function postVenta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $ordenPost = json_decode($request['item'], true);
            //armar orden
            $orden = array();
            $orden['id'] = $ordenPost['id'];
            $total = DetallesOrden::getTotal($orden['id'], $ordenPost['detallesOrden']);
            if ($ordenPost['montoVenta'] >= $total) {
                $orden['estado'] = 0;
            } else {
                $orden['estado'] = 2;
            }
            $orden['montoVenta'] = $ordenPost['montoVenta'];
            $orden['userVenta'] = Auth::user()['id'];
            OrdenesTrabajo::venta($orden);
            return response()->json(["status" => 0, 'path' => 'espera']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function postDeuda(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item' => 'required',
                'monto' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $ordenPost = json_decode($request['item'], true);
            //armar orden
            $orden = array();
            $orden['id'] = $ordenPost['id'];
            $total = DetallesOrden::getTotal($orden['id'], $ordenPost['detallesOrden']);
            $saldo = $total - $ordenPost['montoVenta'];
            $orden['montoVenta'] = $ordenPost['montoVenta']+$request['monto'];
            if ($orden['montoVenta'] >= $total) {
                $orden['estado'] = 0;
                $orden['montoVenta'] = $total;
            } else {
                $orden['estado'] = 2;
            }
            $orden['userVenta'] = Auth::user()['id'];
            OrdenesTrabajo::deuda($orden,$saldo,$request['monto']);
            return response()->json(["status" => 0, 'path' => 'recibosIngreso']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

}
