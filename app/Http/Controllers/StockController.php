<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoStock;
use App\Models\Sucursal;
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
        $stocksTable = ProductoStock::getTableAdmin($sucursales,$productos);
        return Inertia::render('Stocks/tabla',
            [
                'productos' => $productos,
                'sucursales' => $sucursales,
                'stocks' => $stocksTable,
            ]);
    }

    public function postMore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sucursal' => 'required',
                'producto' => 'required',
                'cantidad' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $stock = ProductoStock::more(
                $request->all()
            );
            return response()->json(["status" => 0, 'path' => 'stocks']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function postLess(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sucursal' => 'required',
                'producto' => 'required',
                'cantidad' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $stock = ProductoStock::less(
                $request->all()
            );
            return response()->json(["status" => 0, 'path' => 'stocks']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $producto = Producto::find($id);
        $producto->delete();
        return back()->withInput();
    }
}
