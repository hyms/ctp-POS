<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\Recibo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ReciboController extends Controller
{
    public function getAll(int $tipo)
    {
        $recibos = Recibo::getAll(Auth::user()['sucursal'],$tipo);
        return Inertia::render('Recibos/tabla', [
            'recibos' => $recibos,
            'active' => ($tipo) ? 1 : 2,
            'tipo'=>$tipo
        ]);
    }
    public function getIngreso()
    {
        return $this->getAll(0);
    }
    public function getEgreso()
    {
        return $this->getAll(1);
    }
    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required',
                'ciNit' => 'required|numeric',
                'monto' => 'required|numeric',
                'detalle' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $values = [
                'codigo' => '',
                'detalle' => $request['detalle'],
                'nombre' => $request['nombre'],
                'ciNit' => $request['ciNit'],
                'codigoVenta' => '',
                'saldo' => 0,
                'monto' => $request['monto'],
                'acuenta' => 0,
                'tipo' => $request['tipo'],
                'created_at' => now(),
                'updated_at' => now(),
                'sucursal' => Auth::user()['sucursal'],
                'userVenta' => Auth::id(),
            ];
            $caja = Cajas::getOne(Auth::user()['sucursal']);
            Recibo::guardar($values, $caja->get()->first()->id,$request['tipo']);
            return response()->json([
                'status' => 0,
                'path'=>($request['tipo'])?'recibosEgreso':'recibosIngreso'
            ]);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $recibo = Recibo::find($id);
        $recibo->delete();
        return back()->withInput();
    }
}
