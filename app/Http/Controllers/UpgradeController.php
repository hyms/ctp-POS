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
        set_time_limit(300);
        try {
//            $versions = env('APP_VERSION');
//            if (!empty($versions)) {
//                $version = Str::of($versions)->split('/[\s,]+/');
//                if($version[0]>=3){
//                    throw new Exception("not need upgrade");
//                }
//            }
            DB::transaction(function () {
                $id = DB::table('settings')->insertGetId([
                    'CompanyName' => "Prographics",
                    'email' => "",
                    'CompanyPhone' => "",
                    'CompanyAdress' => "",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' =>null,
                ]);

                $items = DB::table('clientes')->get()->collect();
                $items->each(function ($item) {
                    $id = DB::table('clients')->insertGetId([
                        'id' => $item->id,
                        'name' => $item->nombreCompleto,
                        'company_name' => $item->nombre,
                        'code' => $item->code,
                        'email' => $item->correo,
                        'city' => 'Santa Cruz',
                        'phone' => $item->telefono,
                        'adresse' => $item->direccion,
                        'nit_ci' => $item->nitCi,
                        'created_at' => $item->created_at,
                        'updated_at' => Carbon::now(),
                        'deleted_at' => $item->deleted_at,
                    ]);
                });
                Log::info("finish client migration");
                $items = DB::table('sucursales')->get()->collect();
                $items->each(function ($item) {
                    $id = DB::table('warehouses')->insertGetId([
                        'id' => $item->id,
                        'name' => $item->nombre,
                        'city' => null,
                        'mobile' => $item->telefono,
                        'email' => null,
                        'country' => null,
                        'created_at' => $item->created_at,
                        'updated_at' => Carbon::now(),
                        'deleted_at' => $item->deleted_at,
                    ]);
                });
                Log::info("finish warehouses migration");
                $items = DB::table('users')->get()->collect();
                $items->each(function ($item) {
                    $id = DB::table('user_warehouse')->insertGetId([
                        'user_id' => $item->id,
                        'warehouse_id' => $item->sucursal,
                    ]);
                });
                Log::info("finish user_warehouse migration");

                $items = collect([
                    ['value' => 1, 'text' => 'sadmin'],
                    ['value' => 2, 'text' => 'dueño'],
                    ['value' => 3, 'text' => 'venta'],
                    ['value' => 4, 'text' => 'operario']
                ]);
                $items->each(function ($item) {
                    $id = DB::table('roles')->insertGetId([
                        'id' => $item['value'],
                        'name' => $item['text'],
                        'label' => Str::of($item['text'])->ucfirst(),
                        'status' => ($item['value'] == 1) ? 1 : 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                });
                Log::info("finish roles migration");

                $id = DB::table('role_user')->insertGetId([
                    'user_id' => 1,
                    'role_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $id = DB::table('role_user')->insertGetId([
                    'user_id' => 8,
                    'role_id' => 2,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                Log::info("finish role_user migration");
                $items = DB::table('tipoProducto')->get();
                $items->each(function ($item) {
                    $id = DB::table('categories')->insertGetId([
                        'id' => $item->id,
                        'code' => $item->codigo,
                        'name' => $item->nombre,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_at' => $item->deleted_at,
                    ]);
                });
                Log::info("finish categories migration");

                $unit_id = DB::table('units')->insertGetId([
                    'id' => 1,
                    'name' => 'placa',
                    'ShortName' => 'placa',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $id = DB::table('units')->insertGetId([
                    'id' => 2,
                    'name' => 'bidon',
                    'ShortName' => 'bidon',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $id = DB::table('units')->insertGetId([
                    'id' => 3,
                    'name' => 'impresion',
                    'ShortName' => 'imp',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                Log::info("finish units migration");

                $items = DB::table('tipoProducto')->get();
                $items->each(function ($item) {
                    $id = DB::table('sales_type')->insertGetId([
                        'id' => $item->id,
                        'code' => $item->codigo,
                        'name' => $item->nombre,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_at' => $item->deleted_at,
                    ]);
                });
                Log::info("finish sales_type migration");
                $items = DB::table('productos')->get();
                $items->each(function ($item) {

                    $id = DB::table('products')->insertGetId([
                        'id' => $item->id,
                        'code' => $item->codigo,
                        'name' => $item->formato,
                        'cost' => 0,
                        'price' => 0,
                        'category_id' => $item->tipo ?? 1,
                        'unit_id' => 1,
                        'unit_sale_id' => 1,
                        'unit_purchase_id' => 1,
                        'TaxNet' => 0,
                        'tax_method' => '1',
                        'note' => $item->dimension,
                        'stock_alert' => 0,
                        'is_variant' => 0,
                        'not_selling' => 0,
                        'is_active' => 1,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_at' => $item->deleted_at,
                    ]);
                });
                Log::info("finish products migration");

                $items = DB::table('stock')->get();
                $items->each(function ($item) {

                    $id = DB::table('product_warehouse')->insertGetId([
                        'id' => $item->id,
                        'product_id' => $item->producto,
                        'warehouse_id' => $item->sucursal,
                        'product_variant_id' => null,
                        'qty' => $item->cantidad,
                        'price' => $item->precioUnidad,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_at' => $item->deleted_at,
                    ]);
                });
                Log::info("finish product_warehouse migration");

                $items = DB::table('ordenesTrabajo')->get();
                $items->each(function ($item) {
                    if($item->estado>=0 && $item->estado < 10) {
                        if (empty($item->tipoOrden)) {
                            $item->tipoOrden = 1;
                        }
                        $details = DB::table('detallesOrden')->where('ordenTrabajo', '=', $item->id)->get();
                        $GrandTotal = 0;
                        $paid_amount = 0;
                        $sale_datail = collect();
                        foreach ($details as $detail) {
                            $product_id = DB::table('stock')->where('id', '=', $detail->stock)->first()?->producto;
                            $sale_unit_id = DB::table('products')->where('id', '=', $product_id)->first()?->unit_sale_id;
                            $GrandTotal += $detail->total;
                            $sale_datail->add([
                                "id" => $detail->id,
                                "date" => Carbon::parse($item->created_at),
                                "sale_id" => $detail->ordenTrabajo,
                                "product_id" => $product_id,
                                "product_variant_id" => null,
                                "sale_unit_id" => $sale_unit_id,
                                "price" => $detail->costo,
                                "TaxNet" => 0,
                                "tax_method" => 0,
                                "discount" => 0,
                                "discount_method" => 0,
                                "total" => $detail->total,
                                "quantity" => $detail->cantidad,
                                "created_at" => $detail->created_at,
                                "updated_at" => $detail->updated_at,
                            ]);
                        }
                        $sale_payment = collect();
                        $moving = DB::table('movimientoCajas')->where('ordenTrabajo', '=', $item->id)->get();
                        foreach ($moving as $mov) {
                            if ($mov->monto > 0 and $GrandTotal > $paid_amount) {
                                if ($mov->tipo == 4) {
                                    $detalle = DB::table('recibos')->where('movimientoCaja', '=', $item->id)->get()->first();
                                    if (isset($detalle))
                                        $mov->observaciones = $detalle->detalle;
                                }
                                if ($mov->monto > $GrandTotal) {
                                    $mov->monto = $GrandTotal;
                                }
                                $sale_payment->add([
//                        "id" => "",
                                    "user_id" => $mov->user,
                                    "date" => $mov->created_at,
                                    "Ref" => $mov->id,
                                    "sale_id" => $mov->ordenTrabajo,
                                    "montant" => $mov->monto,
                                    "change" => 0,
                                    "Reglement" => "cash",
                                    "notes" => $mov->observaciones,
                                    "created_at" => $mov->created_at,
                                    "updated_at" => $mov->updated_at,
                                    "deleted_at" => $mov->deleted_at
                                ]);
                                $paid_amount += $mov->monto;
                            }
                        }
//                        if ($item->estado == 0) {
//                            $paid_amount = $GrandTotal;
//                        }
                        $payment_statut = '';
                        $due = $GrandTotal - $paid_amount;
                        if ($due === 0.0 || $due < 0.0) {
                        $paid_amount=$GrandTotal;
                            $payment_statut = 'paid';
                        } else if ($due != $GrandTotal) {
                            $payment_statut = 'partial';
                        } else if ($due == $GrandTotal) {
                            $payment_statut = 'unpaid';
                        }
                        $id = DB::table('sales')->insertGetId([
                            "id" => $item->id,
                            "user_pos" => $item->userDiseñador,
                            "user_id" => $item->userVenta ?? $item->userDiseñador,
                            "date" => Carbon::parse($item->created_at),
                            "Ref" => $item->codigoServicio ?? $item->correlativo,
                            "is_pos" => 0,
                            "client_id" => $item->cliente,
                            "warehouse_id" => $item->sucursal,
                            "TaxNet" => 0,
                            "tax_rate" => 0,
                            "discount" => 0,
                            "shipping" => 0,
                            "GrandTotal" => $GrandTotal,
                            "paid_amount" => $paid_amount,
                            "payment_statut" => $payment_statut,
                            "statut" => ($item->estado == 1) ? 'pending' : 'completed',
                            "shipping_status" => "",
                            "notes" => $item->observaciones,
                            "created_at" => $item->created_at,
                            "updated_at" => $item->updated_at,
                            "deleted_at" => $item->deleted_at,
                            "sales_type_id" => $item->tipoOrden,
                        ]);
                        foreach ($sale_datail as $detail) {
                            $id = DB::table('sale_details')->insertGetId($detail);
                        }
                        foreach ($sale_payment as $payment) {
                            $id = DB::table('payment_sales')->insertGetId($payment);
                        }
                    }
                });
                Log::info("finish sales migration");

//                $items = DB::table('movimientoCajas')->whereNotNull('ordenTrabajo')->get();
//                $items->each(function ($item) {
//                    if($item->monto >0) {
//                        if ($item->tipo == 4) {
//                            $detalle = DB::table('recibos')->where('movimientoCaja', '=', $item->id)->get()->first();
//                            $item->observaciones = $detalle->detalle;
//                        }
//                        $id = DB::table('payment_sales')->insertGetId([
////                        "id" => "",
//                            "user_id" => $item->user,
//                            "date" => $item->created_at,
//                            "Ref" => $item->id,
//                            "sale_id" => $item->ordenTrabajo,
//                            "montant" => $item->monto,
//                            "change" => 0,
//                            "Reglement" => "Efectivo",
//                            "notes" => $item->observaciones,
//                            "created_at" => $item->created_at,
//                            "updated_at" => $item->updated_at,
//                            "deleted_at" => $item->deleted_at
//                        ]);
//                    }
//                });
                Log::info("finish payment_sales migration");
            });
        } catch (Exception $ex) {
            $errors = $ex->getMessage();
        }
        return response()->json([
            'errorsMessage' => $errors,
        ]);
    }
}
