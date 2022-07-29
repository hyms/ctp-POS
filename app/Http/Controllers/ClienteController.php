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
        $clientes = Cliente::all();
        $clientes->transform(function ($item, $key) {
            $item['nombreSucursal']=$item->Sucursal?->nombre;
            return $item;
        });
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->map(function ($item,$key){ return ['value'=>$item->id,'text'=>$item->nombre];});
        return Inertia::render('Clientes', [
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

            $Cliente = !empty($request['id'])
                ? Cliente::find($request['id'])
                : new Cliente();
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
        Cliente::find($id)
            ->delete();
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
