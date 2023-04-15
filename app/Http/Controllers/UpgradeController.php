<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Carbon\Carbon;
use Exception;
use Inertia\Response;

class UpgradeController extends Controller
{
    function index(): Response
    {
        $versions = env('APP_VERSION');
        $message = "upgraded";
        $upgraded = 0;
        if (empty($versions) || $versions == "2.0.0") {
            $message = "need upgrade";
            $upgraded = 1;
        }
        return Inertia::render('Upgrade', [
            'message' => $message,
            'upgraded' => $upgraded,
        ]);
    }

    function upgrade(): JsonResponse
    {
        $errors = "";
        try {
//            $versions = env('APP_VERSION');
//            if (!empty($versions)) {
//                $version = Str::of($versions)->split('/[\s,]+/');
//                if($version[0]>=3){
//                    throw new Exception("not need upgrade");
//                }
//            }
//            $items = DB::table('clientes')->get()->collect();
//            $items->each(function ($item){
//                $id = DB::table('clients')->insertGetId([
//                    'id'=>$item->id,
//                    'name'=>$item->nombreCompleto,
//                    'company_name'=>$item->nombre,
//                    'code'=>$item->code,
//                    'email'=>$item->correo,
//                    'city'=>'Santa Cruz',
//                    'phone'=>$item->telefono,
//                    'adresse'=>$item->direccion,
//                    'nit_ci'=>$item->nitCi,
//                    'created_at'=>$item->created_at,
//                    'updated_at'=>Carbon::now(),
//                    'deleted_at'=>$item->deleted_at,
//                ]);
//            });
//            Log::info("finish client migration");
//
//            $items = DB::table('sucursales')->get()->collect();
//            $items->each(function ($item){
//                $id = DB::table('warehouses')->insertGetId([
//                    'id'=>$item->id,
//                    'name'=>$item->nombre,
//                    'city'=>null,
//                    'mobile'=>$item->telefono,
//                    'email'=>null,
//                    'country'=>null,
//                    'created_at'=>$item->created_at,
//                    'updated_at'=>Carbon::now(),
//                    'deleted_at'=>$item->deleted_at,
//                ]);
//            });
//            Log::info("finish warehouses migration");
//
//            $items = DB::table('users')->get()->collect();
//            $items->each(function ($item){
//                $id = DB::table('user_warehouse')->insertGetId([
//                    'user_id'=>$item->id,
//                    'warehouse_id'=>$item->sucursal,
//                ]);
//            });
//            Log::info("finish user_warehouse migration");

//            $items = collect([
//                ['value' => 1, 'text' => 'sadmin'],
//                ['value' => 2, 'text' => 'dueño'],
//                ['value' => 3, 'text' => 'venta'],
//                ['value' => 4, 'text' => 'operario']
//            ]);
//            $items->each(function ($item) {
//                $id = DB::table('roles')->insertGetId([
//                    'id' => $item['value'],
//                    'name' => $item['text'],
//                    'label' => Str::of($item['text'])->ucfirst(),
//                    'status' => ($item['value'] == 1) ? 1 : 0,
//                    'created_at' => Carbon::now(),
//                    'updated_at' => Carbon::now(),
//                ]);
//            });
//            Log::info("finish roles migration");
//
//            $id = DB::table('role_user')->insertGetId([
//                'user_id' => 1,
//                'role_id' => 1,
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ]);
//            $id = DB::table('role_user')->insertGetId([
//                'user_id' => 8,
//                'role_id' => 2,
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ]);
//            Log::info("finish role_user migration");
//            $items = DB::table('tipoProducto')->get();
//            $items->each(function ($item) {
//                $id = DB::table('categories')->insertGetId([
//                    'id' => $item->id,
//                    'code' => $item->codigo,
//                    'name' => $item->nombre,
//                    'created_at' => $item->created_at,
//                    'updated_at' => $item->updated_at,
//                    'deleted_at' => $item->deleted_at,
//                ]);
//            });
            $id = DB::table('units')->insertGetId([
                'name' => 'placa',
                'ShortName' => 'placa',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $id = DB::table('units')->insertGetId([
                'name' => 'bidon',
                'ShortName' => 'bidon',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $id = DB::table('units')->insertGetId([
                'name' => 'impresion',
                'ShortName' => 'imp',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            Log::info("finish categories migration");

        } catch (Exception $ex) {
            $errors = $ex->getMessage();
        }
        return response()->json([
            'errorsMessage' => $errors,
        ]);
    }
}
