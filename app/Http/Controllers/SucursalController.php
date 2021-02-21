<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class SucursalController extends Controller
{
    public function getAll()
    {
        $sucursal = Sucursal::getAll(true);
        return Inertia::render('Sucursales/tabla', ['sucursales' => $sucursal]);
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre'=>'required',
                'telefono'=>'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $sucursal = new Sucursal();
            if (!empty($request['id'])) {
                $sucursal = Sucursal::find($request['id']);
            }
            $sucursal->fill($request->all());
            $sucursal->save();
            return response()->json(["status" => 0, 'path' => 'sucursales']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $sucursal = Sucursal::find($id);
        $sucursal->delete();
        return back()->withInput();
    }
}
