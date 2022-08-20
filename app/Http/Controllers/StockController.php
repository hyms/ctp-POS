<?php

namespace App\Http\Controllers;

use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\ProductoStock;
use App\Models\Sucursal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class StockController extends Controller
{
    public function getAll()
    {
        $productos = Producto::getAll();
        $sucursales = Sucursal::getAll();
        $stocksTable = ProductoStock::getTableAdmin($sucursales, $productos);
        Inertia::share('titlePage', 'Stoks de Producto');

        return Inertia::render('Stocks',
            [
                'productos' => $productos,
                'sucursales' => $sucursales,
                'stocks' => $stocksTable,
            ]);
    }

    public function borrar($id)
    {
        $producto = Producto::find($id);
        $producto->delete();
        return back()->withInput();
    }

    public function movimientos()
    {
        $productos = Producto::getAll();
        $sucursales = Sucursal::getAll();
        $stocks = MovimientoStock::gelAll();
        return Inertia::render('Stocks/movimientos',
            [
                'productos' => $productos,
                'sucursales' => $sucursales,
                'movimientos' => $stocks,
            ]);
    }

    public function enableStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $stock = ProductoStock::find($request['id']);
            $stock->enable = !$stock->enable;
            $stock->save();
            return response()->json(["status" => 0]);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function priceStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'precioUnidad' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $stock = ProductoStock::find($request['id']);
            $stock->precioUnidad = $request['precioUnidad'];
            $stock->save();
            return response()->json(["status" => 0]);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function amountStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'cantidad' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $stock = ProductoStock::find($request['id']);
            $request2 = collect(['sucursal' => $stock->sucursal, 'producto' => $stock->producto]);
            $request2->put('cantidad', $request['cantidad']);
            ProductoStock::more($request2->all());

            return response()->json(["status" => 0]);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

}
