<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Carbon\Carbon;
use Exception;

class UpgradeController extends Controller
{
    function index(){
        $versions = env('APP_VERSION');
        $message="upgraded";
        $upgraded=0;
        if(empty($versions) || $versions=="2.0.0"){
            $message="need upgrade";
            $upgraded=1;
        }
        return Inertia::render('upgrade', [
            'message' => $message,
            'upgraded' => $upgraded,
        ]);
    }
    function upgrade(){
        $errors="";
        try {
            $versions = env('APP_VERSION');
            if (!empty($versions)) {
                $version = Str::of($versions)->split('/[\s,]+/');
                if($version[0]>=3){
                    throw new Exception("not need upgrade");
                }
            }
            $clientes = Cliente::getAll();
            $clientes->each(function ($item){
                $id = DB::table('clients')->insertGetId([
                    'name'=>$item->nombreCompleto,
                    'company_name'=>$item->nombre,
                    'code'=>$item->code,
                    'email'=>$item->correo,
                    'city'=>'Santa Cruz',
                    'phone'=>$item->telefono,
                    'adresse'=>$item->direccion,
                    'nit_ci'=>$item->nitCi,
                    'created_at'=>$item->created_at,
                    'updated_at'=>Carbon::now(),
                    'deleted_at'=>$item->deleted_at,
                ]);
            });



        }
        catch (Exception $ex){
            $errors= $ex->getMessage();
        }
        return response()->json([
            'errorsMessage' => $errors,
        ]);
    }
}
