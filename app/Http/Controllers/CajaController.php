<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CajaController extends Controller
{
    public function getAll()
    {
        $cajas = Cajas::getAll();
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->pluck('nombre','id');
        return Inertia::render('Cajas/tabla', [
            'cajas' => $cajas,
            'sucursales' => $sucursales
        ]);
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sucursal' => 'required',
                'nombre' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $Cliente = new Cajas();
            if (!empty($request['id'])) {
                $Cliente = Cajas::find($request['id']);
            }
            else{
                $request['monto'] = 0;
            }
            $Cliente->fill($request->all());

            $Cliente->save();
            return response()->json(["status" => 0, 'path' => 'cajas']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $Cliente = Cajas::find($id);
        $Cliente->delete();
        return back()->withInput();
    }
}
