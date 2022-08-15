<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class SucursalController extends Controller
{
    public function getAll()
    {
        $sucursal = Sucursal::getAll(true);
        $sucursal->transform(function ($item, $key) {
            $item->centralView=($item->enable === 1) ? "Si" : "No" ;
            $item->enableView=($item->enable === 1) ? "Si" : "No" ;
            return $item;
        });
        $sucursales = $sucursal->map(function ($item,$key){ return ['value'=>$item->id,'text'=>$item->nombre];});
        Inertia::share('titlePage', 'Sucursales');
        return Inertia::render('Sucursales', ['sucursales' => $sucursal,'sucursalOptions'=>$sucursales]);
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre'=>'required',
                'telefono'=>'required',
                'gmap'=>'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }

            $sucursal = !empty($request['id'])
                ? Sucursal::find($request['id'])
                : new Sucursal();
            $sucursal->fill($request->all());
            $sucursal->save();
            return response()->json(["status" => 0, 'path' => 'sucursales']);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        Sucursal::find($id)->delete();
        return back()->withInput();
    }

}
