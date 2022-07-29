<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\TipoProductos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;


class ProductosController extends Controller
{
    public function getAll()
    {
        $productos = Producto::getAll();
        $productos = TipoProductos::getTipoProductoxProducto($productos);
        $tipoProducto = TipoProductos::getAll();
        $tipoProducto = $tipoProducto->map(function ($item,$key){ return ['value'=>$item->id,'text'=>$item->nombre];});
        return Inertia::render('Productos',
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

            $producto = !empty($request['id'])
                ? Producto::find($request['id'])
                : new Producto();
            $producto->fill($request->except('productoTipo'));
            $producto->save();
            if(isset($request['productoTipo']))
            {
                TipoProductos::saveTiposProducto($producto->id, Str::of($request['productoTipo'])->explode(','));
            }
            return response()->json(["status" => 0, 'path' => 'productos']);
        } catch (Exception $error) {
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
        return Inertia::render('TipoProducto', ['tipos' => $tipos]);
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

            $producto = !empty($request['id'])
                ? TipoProductos::find($request['id'])
                : new TipoProductos();
            $producto->fill($request->all());
            $producto->save();
            return response()->json(["status" => 0, 'path' => ' tipoProductos']);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }
    public function borrarTipo($id)
    {
        $tipos = TipoProductos::find($id);
        $tipos->delete();
        return back()->withInput();
    }
}
