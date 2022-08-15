<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\Recibo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ReciboController extends Controller
{
    public function getAll(int $tipo, Request $request)
    {
        $recibos = Recibo::getAll(Auth::user()['sucursal'], $tipo, $request->all());
        return Inertia::render('Recibos/tabla', [
            'recibos' => $recibos,
            'tipo' => $tipo,
            'report' => (object)$request->all()
        ]);
    }

    public function getIngreso(Request $request)
    {
        Inertia::share('titlePage', 'Recibos de Ingreso');
        return $this->getAll(0, $request);
    }

    public function getEgreso(Request $request)
    {
        Inertia::share('titlePage', 'Recibos de Egreso');
        return $this->getAll(1, $request);
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
            $caja = Cajas::where('sucursal',Auth::user()['sucursal'])->get();
            Recibo::guardar($values, $caja->first()->id, $request['tipo']);
            return response()->json([
                'status' => 0,
                'path' => ($request['tipo']) ? 'recibosEgreso' : 'recibosIngreso'
            ]);
        } catch (Exception $error) {
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
