<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ReciboController extends Controller
{
    public function getAll()
    {
        $recibos = Recibo::getAll(Auth::user()['sucursal']);
        return Inertia::render('Recibos/tabla', [
            'recibos' => $recibos,
        ]);
    }
    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'monto' => 'required',
                'detalle' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }


        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }
}
