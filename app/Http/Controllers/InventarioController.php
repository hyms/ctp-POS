<?php

namespace App\Http\Controllers;

use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\ProductoStock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class InventarioController extends Controller
{
    public function get(Request $request)
    {
        $productosStocks = ProductoStock::getProducts(Auth::user()['sucursal']);
        $productos = $productosStocks->map(function ($value){
            return ['text'=>$value->formato,'value'=>$value->producto];
        });

        $stocks = ProductoStock::getAll(Auth::user()['sucursal']);
        $fields = [
            [
                'value' => 'productoView',
                'text' => 'Producto'
            ],
            [
                'value' => 'observaciones',
                'text' => 'Observaciones'
            ],
            [
                'value' => 'cantidad',
                'text' => 'Cantidad'
            ],
            [
                'value' => 'created_at',
                'text' => 'Fecha'
            ],
        ];
        $stocksId = $stocks->pluck('id')->toArray();
        $ingresos = MovimientoStock::getAllTable($stocksId, true, $request->all());
        $egresos = MovimientoStock::getAllTable($stocksId, false, $request->all());
        $inventario = [
            ['title' => 'Egreso', 'typeInventario' => 1, 'data' => $egresos->all()],
            ['title' => 'Ingreso', 'typeInventario' => 2, 'data' => $ingresos->all()],
        ];
        Inertia::share('titlePage', 'Inventario');
        return Inertia::render('Inventario', [
            'productos' => $productosStocks,
            'productosSelect' => $productos,
            'inventario' => $inventario,
            'fields' => $fields,
            'stocks' => $stocks,
            'report' => (object)$request->all()
        ]);
    }

    public function postIngreso(Request $request)
    {
        return $this->post($request, true);
    }

    public function postEgreso(Request $request)
    {
        return $this->post($request, false);
    }

    public function post(Request $request, bool $ingreso)
    {
        try {
            $validator = Validator::make($request->all(), [
                'productos' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $products = json_decode($request['productos'], true);
            foreach ($products as $item) {
                $stock = ProductoStock::find($item['id']);
                $stock->cantidad = $ingreso
                    ? ($stock->cantidad + $item['cantidad'])
                    : ($stock->cantidad - $item['cantidad']);
                $stock->save();
                $movimiento = DB::table(MovimientoStock::$tables);
                $movimiento->insert([
                    'producto' => $item['producto'],
                    'stockOrigen' => ($ingreso ? null : $item['id']),
                    'stockDestino' => ($ingreso ? $item['id'] : null),
                    'cantidad' => $item['cantidad'],
                    'observaciones' => $request['observaciones'],
                    'user' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
            return response()->json(["status" => 0, 'path' => '/inventario/' . ($ingreso ? 'ingreso' : 'egreso')]);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function saldo()
    {
        $productosAll = ProductoStock::getProducts(Auth::user()['sucursal']);
        return Inertia::render('Inventario/saldo', [
            'productos' => $productosAll,
        ]);
    }

}
