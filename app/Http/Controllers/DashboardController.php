<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\Sucursal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    function index()
    {
        $sucursal = Sucursal::find(Auth::user()->sucursal);
        $user = Auth::user()->nombre . ' ' . Auth::user()->apellido;
        return Inertia::render('Welcome', ['usernames' => $user, 'sucursal' => $sucursal]);
    }

    function migrate()
    {
        $error = "";
        try {
            DB::transaction(function () {
                $data_v3 = DB::connection('mysql_v3')->table('users')->get();
                $data_v3->each(function ($item) {
                    DB::connection('mysql')->table('users')
                        ->updateOrInsert(
                            ['id' => $item->id],
                            [
                                'password' => $item->password,
                                'username' => $item->username,
                                'apellido' => $item->lastname,
                                'nombre' => $item->firstname,
                                'created_at' => $item->created_at,
                                'updated_at' => $item->updated_at,
                                'enable' => $item->statut,
                                'sucursal' => '3',
                                'role' =>  $item->role??'2',
                                'ci'=> $item->ci,
                                'telefono'=>$item->phone??'123',
                                'email'=>$item->email??'asd@asdd.com',
                                'allSucursales'=>'0'
                            ],
                        );
                });

                $data_v3 = DB::connection('mysql_v3')->table('sales')->where('date', '>=', '2024-01-06')->get();
                $data_v3->each(function ($item) {
                    //new sales
                    $orden['sucursal'] = $item->warehouse_id;
                    $orden['estado'] = 1;
                    $orden['userDiseñador'] = $item->user_id;
                    $orden['tipoOrden'] = $item->sales_type_id;
                    $client = DB::connection('mysql_v3')->table('clients')
                        ->where('id', '=', $item->client_id)->first();
                    $client_old = DB::connection('mysql')->table('clientes')
                        ->where('id', '=', $item->client_id)->first();
                    $orden['responsable'] = $client->company_name;
                    $orden['telefono'] = $client->phone;
                    $orden['observaciones'] = $item->notes . "\n Orden: " . $item->Ref;
                    $orden['cliente'] = $client_old->id ?? Cliente::newCliente($client->company_name, $client->phone, $item->warehouse_id);//$item->client_id;
                    $orden['created_at'] = $item->created_at;
                    $orden['updated_at'] = $item->updated_at;

                    //armar detalleOrden

                    $detalle = Collection::empty();
                    $products = DB::connection('mysql_v3')->table('sale_details')
                        ->where('sale_id', '=', $item->id)->get();
                    foreach ($products as $item_detalle) {
                        $tmp = array();
                        $tmp['sucursal'] = $item->warehouse_id;
                        $tmp['producto'] = $item_detalle->product_id;
                        $stock = DB::connection('mysql')->table('stock')
                            ->where('producto', '=', $item_detalle->product_id)
                            ->where('sucursal', '=', $item->warehouse_id)
                            ->first();
                        $tmp['stock'] = $stock->id;
                        $tmp['cantidad'] = $item_detalle->quantity;
                        $tmp['costo'] = $item_detalle->price;
                        $tmp['total'] = $tmp['cantidad'] * $tmp['costo'];
                        $tmp['created_at'] = $item->created_at;
                        $tmp['updated_at'] = $item->created_at;
                        $detalle->add($tmp);
                    }
                    $orden['montoVenta'] = $detalle->sum('total');
                    $id = OrdenesTrabajo::newOrden($orden, $detalle->all(), null);

                    $orden = array();
                    $orden['id'] = $id;
                    $ordenPost['montoVenta'] = round($item->paid_amount, 2);
                    $total = DetallesOrden::getTotal($orden['id'], $detalle->all());
                    $orden['estado'] = (($item->paid_amount >= $total)) ? 0 : 2;
                    $orden['montoVenta'] = $item->paid_amount;
                    $orden['userVenta'] = $item->user_id;
                    $orden['created_at'] = $item->created_at;
                    $orden['updated_at'] = $item->updated_at;
                    $id = OrdenesTrabajo::venta($orden);
                });
            });
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }
        return response()->json(["status" => 0,
            'message' => $error]);
    }
}
