<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\TipoProductos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ProductosController extends Controller
{
    public function getAll()
    {
        $productos = Producto::getAll();
        $productos = TipoProductos::setTiposProducto($productos);
        $tipoProducto = TipoProductos::getAll();
        $tipoProducto = $tipoProducto->pluck('nombre', 'id');
        return Inertia::render('Productos/tabla',
            [
                'productos' => $productos,
                'tipoProducto' => $tipoProducto
            ]
        );
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
//               'codigo' => 'required',
                'formato' => 'required',
                'dimension' => 'required',
//                'cantidadPaquete' => 'numeric'
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
            $producto->fill($request->except('productoTipo'));
            $producto->save();
            if(isset($request['productoTipo']))
            {
                TipoProductos::saveTiposProducto($producto->id, explode(',', $request['productoTipo']));
            }
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

    public function tipos()
    {
        $tipos = TipoProductos::all();
        return Inertia::render('TipoProducto/tabla', ['tipos' => $tipos]);
    }

    public function tiposPost(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $producto = new TipoProductos();
            if (!empty($request['id'])) {
                $producto = TipoProductos::find($request['id']);
            }
            $producto->fill($request->all());
            $producto->save();
            return response()->json(["status" => 0, 'path' => ' tipoProductos']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }
}
