<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Sucursal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ClienteController extends Controller
{
    public function getAll()
    {
        $clientes = Cliente::getAll();
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->pluck('nombre','id');
        return Inertia::render('Clientes/tabla', [
            'clientes' => $clientes,
            'sucursales' => $sucursales
        ]);
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sucursal' => 'required',
                'nombreCompleto' => 'required',
                'nombreResponsable' => 'required',
                 'nitCi' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $Cliente = new Cliente();
            if (!empty($request['id'])) {
                $Cliente = Cliente::find($request['id']);
            }
            $Cliente->fill($request->all());

            $Cliente->save();
            return response()->json(["status" => 0, 'path' => 'clientes']);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $Cliente = Cliente::find($id);
        $Cliente->delete();
        return back()->withInput();
    }

    public function buscar($id){
        $clientes = DB::table(Cliente::$tables)
            ->where('nombreResponsable','like',"%{$id}%")
            ->whereNull('deleted_at')
            ->select(['nombreResponsable','id','telefono'])
            ->get();
        return response()->json(["items"=>$clientes]);
    }
}
