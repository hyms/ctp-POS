<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ProductosController extends Controller
{
    public function getAll()
    {
        $productos = Producto::getAll();
        return Inertia::render('Productos/tabla', ['productos' => $productos]);
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
//               'codigo' => 'required',
                'formato' => 'required',
//               'dimension' => 'required',
                'cantidadPaquete' => 'numeric'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $producto = new Producto();
            if (!empty($request['id'])) {
                $producto = Producto::find($request['id']);
            }
            $producto->fill($request->all());

            $producto->save();
            return response()->json(["status" => 0, 'path' => 'productos']);
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
