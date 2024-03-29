<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\PaymentSale;
use App\Models\Permission;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalesType;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
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
        set_time_limit(500);
        try {
//            $versions = env('APP_VERSION');
//            if (!empty($versions)) {
//                $version = Str::of($versions)->split('/[\s,]+/');
//                if($version[0]>=3){
//                    throw new Exception("not need upgrade");
//                }
//            }
            DB::transaction(function () {
                Setting::create([
                    'CompanyName' => "Prographics",
                    'email' => "",
                    'CompanyPhone' => "",
                    'CompanyAdress' => "",
                    'days' => 1,
                ]);

                Log::info("finish settings migration");
                $items = DB::table('clientes')->get()->collect();
                $client = $items->map(function ($item) {
                    return [
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
                    ];
                });
                Client::insert($client->toArray());
                Log::info("finish client migration");
                $items = DB::table('sucursales')->get()->collect();
                $warehouse = $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->nombre,
                        'city' => null,
                        'mobile' => $item->telefono,
                        'email' => null,
                        'country' => null,
                        'created_at' => $item->created_at,
                        'updated_at' => Carbon::now(),
                        'deleted_at' => $item->deleted_at,
                    ];
                });
                Warehouse::insert($warehouse->toArray());
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
                $role = $items->map(function ($item) {
                    return [
                        'name' => $item['text'],
                        'label' => Str::of($item['text'])->ucfirst(),
                        'status' => ($item['value'] == 1) ? 1 : 0,
                        'guard_name' => 'web',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                });
                Role::insert($role->toArray());
                Log::info("finish roles migration");
                $items = collect([
                    'users_view',
                    'users_edit',
                    'record_view',
                    'users_delete',
                    'users_add',
                    'permissions_edit',
                    'permissions_view',
                    'permissions_delete',
                    'permissions_add',
                    'products_delete',
                    'products_view',
                    'barcode_view',
                    'products_edit',
                    'products_add',
                    'expense_add',
                    'expense_delete',
                    'expense_edit',
                    'expense_view',
                    'expense_category_add',
                    'expense_category_delete',
                    'expense_category_edit',
                    'expense_category_view',
                    'transfer_delete',
                    'transfer_add',
                    'transfer_view',
                    'transfer_edit',
                    'adjustment_delete',
                    'adjustment_add',
                    'adjustment_edit',
                    'adjustment_view',
                    'Sales_edit',
                    'Sales_view',
                    'Sales_delete',
                    'Sales_add',
                    'payment_sales_delete',
                    'payment_sales_add',
                    'payment_sales_edit',
                    'payment_sales_view',
                    'Customers_edit',
                    'Customers_view',
                    'Customers_delete',
                    'Customers_add',
                    'pay_due',
                    'unit',
                    'currency',
                    'category',
                    'backup',
                    'warehouse',
                    'sales_type',
                    'setting_system',
                    'Warehouse_report',
                    'Reports_quantity_alerts',
                    'Reports_profit',
                    'Reports_suppliers',
                    'Reports_customers',
                    'Reports_purchase',
                    'Reports_sales',
                    'Reports_payments_purchase_Return',
                    'Reports_payments_Sale_Returns',
                    'Reports_payments_Purchases',
                    'Reports_payments_Sales',
                    'users_report',
                    'stock_report',
                    'product_report',
                    'product_sales_report',
                    'Pos_view',
                    'dashboard',
                ]);
                $permision = $items->map(function ($item) {
                    return [
                        'name' => $item,
                        'label' => Str::of($item)->ucfirst(),
                        'guard_name' => 'web',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                });
                Permission::insert($permision->toArray());

                Log::info("finish role_user migration");
                $items = DB::table('tipoProducto')->get();
                $category = $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'code' => $item->codigo,
                        'name' => $item->nombre,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_at' => $item->deleted_at,
                    ];
                });
                Category::insert($category->toArray());
                Log::info("finish categories migration");
                Unit::insert([
                    [
                        'id' => 1,
                        'name' => 'placa',
                        'ShortName' => 'placa',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ], [
                        'id' => 2,
                        'name' => 'bidon',
                        'ShortName' => 'bidon',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ], [
                        'id' => 3,
                        'name' => 'impresion',
                        'ShortName' => 'imp',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]]);
                Log::info("finish units migration");

                $items = DB::table('tipoProducto')->get();
                $salesType = $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'code' => $item->codigo,
                        'name' => $item->nombre,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_at' => $item->deleted_at,
                    ];
                });
                SalesType::insert($salesType->toArray());
                Log::info("finish sales_type migration");
                $items = DB::table('productos')->get();
                $product = $items->map(function ($item) {
                    return [
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
                    ];
                });
                Product::insert($product->toArray());
                Log::info("finish products migration");

                $items = DB::table('stock')->get();
                Log::info("stock count " . $items->count());
                $product_warehouse = $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->producto,
                        'warehouse_id' => $item->sucursal,
                        'product_variant_id' => null,
                        'qty' => ($item->cantidad < 0) ? 0 : $item->cantidad,
                        'price' => $item->precioUnidad,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'deleted_at' => $item->deleted_at,
                    ];
                });
                product_warehouse::insert($product_warehouse->toArray());
                Log::info("finish product_warehouse migration");

                $items = DB::table('ordenesTrabajo')->get();
                $totalItems = $items->count();
                $paginate = 1000;
                $pages = (int)$totalItems / $paginate;
                for ($i = 0; $i <= $pages; $i++) {
                    $items = DB::table('ordenesTrabajo')->skip($paginate * $i)->take($paginate)->get();
                    Log::info("ordenesTrabajo start page " . $i);
//                    Log::info("ordenesTrabajo start page ");
                    $sales = $items->map(function ($item) {
                        if ($item->estado >= 0 && $item->estado < 10) {
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
                                $paid_amount = $GrandTotal;
                                $payment_statut = 'paid';
                            } else if ($due != $GrandTotal) {
                                $payment_statut = 'partial';
                            } else if ($due == $GrandTotal) {
                                $payment_statut = 'unpaid';
                            }
                            return [
                                "sale" => collect([
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
                                ]),
                                "detail" => $sale_datail,
                                "paymentSale" => $sale_payment,
                            ];
                        }
                    });
                    $sales = $sales->filter()->collect();
                    $detail = $sales->pluck('detail');
                    $paymentSale = $sales->pluck('paymentSale');
                    $sales = $sales->pluck('sale');
                    Sale::insert($sales->toArray());
                    $detail->each(function ($item) {
                        SaleDetail::insert($item->toArray());
                    });
                    $paymentSale->each(function ($item) {
                        PaymentSale::insert($item->toArray());
                    });
                }

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
            Log::error($ex->getMessage());
        }
        return response()->json([
            'errorsMessage' => $errors,
        ]);
    }

    function setPermissions()
    {
        $errors = "";
        try {
            $adminRole = Role::where('name', '=', 'sadmin')->first();
            $allPermission = Permission::pluck('name', 'id');
            $adminRole->givePermissionTo($allPermission);
            $user = User::where('username', '=', 'admin')->first();
            $user->assignRole($adminRole);
        } catch (Exception $ex) {
            $errors = $ex->getMessage();
            Log::error($ex->getMessage());
        }
        return response()->json([
            'errorsMessage' => $errors,
        ]);
    }
}
