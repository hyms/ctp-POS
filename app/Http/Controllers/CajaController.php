<?php

namespace App\Http\Controllers;

use App\Models\Cajas;
use App\Models\MovimientoCaja;
use App\Models\Sucursal;
use Carbon\Carbon;
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

    public function arqueo(Request $request)
    {
        $sucursal = Auth::user()['sucursal'];
        $arqueo = new MovimientoCaja();
        $caja = Cajas::getOne($sucursal);
        if($request->has('fecha')){
            $fecha=Carbon::parse($request['fecha']);
            $day = $fecha->day;
            $endDate = $fecha->toDateTimeString();
        }
        else{
            $day = Carbon::now()->day;
            $endDate = Carbon::now()->toDateTimeString();
        }

        $variables = Cajas::getSaldo($caja->first()->id, $endDate, $endDate,false, false);

        return Inertia::render('Cajas/registroDiario',
            [
                'saldo' => $variables['saldo'],
                'arqueo' => $arqueo,
                'caja' => $caja,
                'fecha' => $endDate,
                'ventas' => $variables['ventas'],
                'deudas' => $variables['deudas'],
                'recibos' => $variables['recibos'],
                'cajas' => $variables['cajas'],
                'dia' => $day,
            ]);
    }

    public function getDebito()
    {
        return self::getCreditoDebito(false);
    }

    public function getCredito()
    {
        return self::getCreditoDebito(true);
    }

    private function getCreditoDebito(bool $credito)
    {
        $sucursal = Auth::user()['sucursal'];
        $registros = DB::table(MovimientoCaja::$tables);
        $caja = Cajas::getOne($sucursal)->first();
        $registros = $registros
            ->where('tipo', '=', 2)
            ->where($credito ? 'cajaDestino' : 'cajaOrigen', '=', $caja->id)
            ->limit(20)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Cajas/registros', ['registros' => $registros, 'credito' => $credito]);
    }

    public function debito(Request $request)
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
            return response()->json(self::creditoDebito($request->all(), false)
                ? ["status" => 0, 'path' => 'cajaDebito']
                : ['status' => -1, 'errors' => ['no se logro registrar la transaccion']]
            );
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function credito(Request $request)
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
            return response()->json(self::creditoDebito($request->all(), true)
                ? ["status" => 0, 'path' => 'cajaCredito']
                : ['status' => -1, 'errors' => ['no se logro registrar la transaccion']]
            );
        } catch
        (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    private function creditoDebito(array $datos, bool $credito)
    {
        $sucursal = Auth::user()['sucursal'];
        $registro = DB::table(MovimientoCaja::$tables);
        $caja = Cajas::getOne($sucursal)->first();
        $id = $registro->insertGetId([
            $credito ? 'cajaDestino' : 'cajaOrigen' => $caja->id,
            'user' => Auth::user()['id'],
            'monto' => $datos['monto'],
            'observaciones' => $datos['observaciones'],
            'tipo' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return (isset($id));
    }
}
