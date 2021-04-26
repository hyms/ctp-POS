<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\MovimientoCaja;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CajaController extends Controller
{
    public function getAll()
    {
        $cajas = Cajas::getAll();
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->pluck('nombre', 'id');
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
            $Cajas = new Cajas();
            if (!empty($request['id'])) {
                $Cajas = Cajas::find($request['id']);
            } else {
                $request['monto'] = 0;
            }
            $Cajas->fill($request->all());

            $Cajas->save();
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

    public function borrarMovimiento($id)
    {
        $Cliente = MovimientoCaja::find($id);
        $Cliente->delete();
        return back()->withInput();
    }

    public function getMovimientos()
    {

    }

    public function arqueo()
    {
        $sucursal = Auth::user()['sucursal'];
        $arqueo = new MovimientoCaja();
        $caja = Cajas::getOne($sucursal);
//                    if (isset($post['MovimientoCaja'])) {
//                        $datos = array('arqueo' => $post['MovimientoCaja'], 'caja' => $caja);
//                        $arqueoTransaccion = new SGOrdenes();
//                        $datos = $arqueoTransaccion->arqueo($datos);
//                        if ($arqueoTransaccion->success) {
//                            return $this->redirect(['caja', 'op' => 'arqueos']);
//                        }
//                    }
        $d = date("d");
        $end = date("Y-m-d H:i:s");

        $variables = Cajas::getSaldo($caja->first()->id, $end, false, false);

        return Inertia::render('Cajas/registroDiario',
            [
                'saldo' => $variables['saldo'],
                'arqueo' => $arqueo,
                'caja' => $caja,
                'fecha' => date('Y-m-d H:i:s', strtotime($end)),
                'ventas' => $variables['ventas'],
                'deudas' => $variables['deudas'],
                'recibos' => $variables['recibos'],
                'cajas' => $variables['cajas'],
                'dia' => $d,
            ]);
    }

    public function getRegistro()
    {
        $sucursal = Auth::user()['sucursal'];
        $registros = DB::table(MovimientoCaja::$tables);
        $caja = Cajas::getOne($sucursal)->first();
        $registros= $registros
            ->where('tipo', '=', 2)
            ->where('cajaOrigen', '=', $caja->id)
            ->limit(20)
            ->get();

        return Inertia::render('Cajas/registros', ['registros' => $registros]);
    }

    public function registro(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'monto' => 'required',
                'observaciones' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $sucursal = Auth::user()['sucursal'];
            $registro = DB::table(MovimientoCaja::$tables);
            $caja = Cajas::getOne($sucursal)->first();

            $id = $registro->insertGetId([
                'cajaOrigen'=>$caja->id,
                'user'=>Auth::user()['id'],
                'monto'=>$request['monto'],
                'observaciones'=>$request['observaciones'],
                'tipo'=>2,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]);

            return response()->json(["status" => 0, 'path' => 'cajaRegistro']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }
}
