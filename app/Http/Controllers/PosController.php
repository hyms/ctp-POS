<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\PaymentSale;
use App\Models\product_warehouse;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalesType;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Stripe;

class PosController extends Controller
{

    //------------ Create New  POS --------------\\

    public function CreatePOS(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'Sales_pos', Sale::class);

        request()->validate([
            'client_id' => 'required',
            'warehouse_id' => 'required',
            'payment.amount' => 'required',
        ]);

        $item = DB::transaction(function () use ($request) {
//            $helpers = new helpers();
//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $order = new Sale;

            $order->is_pos = 1;
            $order->date = Carbon::now();
            $order->sales_type_id = $request->sales_type;
            $order->Ref = app(SalesController::class)->getNumberOrder($request->sales_type);
            $order->client_id = $request->client_id;
            $order->warehouse_id = $request->warehouse_id;
            $order->tax_rate = $request->tax_rate;
            $order->TaxNet = $request->TaxNet;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->shipping_status = "";
            $order->GrandTotal = $request->GrandTotal;
            $order->notes = $request->notes;
//            $order->statut = 'completed';
            $order->statut = $request->statut;
//            $order->payment_statut = 'unpaid';
            $order->payment_statut = $request->payment_statut;
            $order->user_pos = Auth::user()->id;
            $order->user_id = Auth::user()->id;

            $order->save();

            $data = collect($request['details']);
            $orderDetails = collect();
            foreach ($data as $key => $value) {

                $unit = Unit::where('id', $value['sale_unit_id'])
                    ->first();
                $orderDetails->add([
                    'date' => Carbon::now(),
                    'sale_id' => $order->id,
                    'sale_unit_id' => $value['sale_unit_id'],
                    'quantity' => $value['quantity'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'total' => $value['subtotal'],
                    'price' => $value['Unit_price'],
                    'TaxNet' => $value['tax_percent'],
                    'tax_method' => $value['tax_method'],
                    'discount' => $value['discount'],
                    'discount_method' => $value['discount_Method'],
                ]);

                if ($value['product_variant_id'] !== null) {
                    $product_warehouse = product_warehouse::where('warehouse_id', $order->warehouse_id)
                        ->where('product_id', $value['product_id'])->where('product_variant_id', $value['product_variant_id'])
                        ->first();

                } else {
                    $product_warehouse = product_warehouse::where('warehouse_id', $order->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();
                }

                if ($unit && $product_warehouse && $request['statut'] == "completed") {
                    if ($unit->operator == '/') {
                        $product_warehouse->qty -= $value['quantity'] / $unit->operator_value;
                    } else {
                        $product_warehouse->qty -= $value['quantity'] * $unit->operator_value;
                    }
                    $product_warehouse->save();
                }
            }

            SaleDetail::insert($orderDetails->toArray());

            $sale = Sale::findOrFail($order->id);
            // Check If User Has Permission view All Records
//            if (!$view_records) {
//                // Check If User->id === sale->id
//                $this->authorizeForUser($request->user('api'), 'check_record', $sale);
//            }

            try {

                $total_paid = $sale->paid_amount + $request['amount'];
                $due = $sale->GrandTotal - $total_paid;

                if ($due === 0.0 || $due < 0.0) {
                    $payment_statut = 'paid';
                } else if ($due != $sale->GrandTotal) {
                    $payment_statut = 'partial';
                } else if ($due == $sale->GrandTotal) {
                    $payment_statut = 'unpaid';
                }

                if ($request['amount'] > 0) {
                    PaymentSale::create([
                        'sale_id' => $order->id,
                        'Ref' => app('App\Http\Controllers\PaymentSalesController')->getNumberOrder(),
                        'date' => Carbon::now(),
                        'Reglement' => $request->payment['Reglement'],
                        'montant' => $request['amount'],
                        'change' => $request['change'],
                        'notes' => $request->payment['notes'],
                        'user_id' => Auth::user()->id,
                    ]);

                    $sale->update([
                        'paid_amount' => $total_paid,
                        'payment_statut' => $payment_statut,
                    ]);
                }

            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

            return $order->id;

        }, 10);

        return response()->json(['success' => true, 'id' => $item]);

    }

    //------------ Get Products--------------\\

    public function GetProductsByParametre(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'Sales_pos', Sale::class);
        // How many items do you want to display.
//        $perPage = 8;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
        $data = collect();

        $product_warehouse_data = product_warehouse::where('warehouse_id', $request->warehouse_id)
            ->with('product', 'product.unitSale')
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($request) {
                return $query->whereHas('product', function ($q) use ($request) {
                    $q->where('not_selling', '=', 0);
                });
//                    ->where(function ($query) use ($request) {
//                        if ($request->stock == '1') {
//                            return $query->where('qty', '>', 0);
//                        }
//                    });
            })

            // Filter
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('category_id'), function ($query) use ($request) {
                    return $query->whereHas('product', function ($q) use ($request) {
                        $q->where('category_id', '=', $request->category_id);
                    });
                });
            })
//            ->where(function ($query) use ($request) {
//                return $query->when($request->filled('brand_id'), function ($query) use ($request) {
//                    return $query->whereHas('product', function ($q) use ($request) {
//                        $q->where('brand_id', '=', $request->brand_id);
//                    });
//                });
//            });
        ;
//        $totalRows = $product_warehouse_data->count();

        $product_warehouse_data = $product_warehouse_data
//            ->offset($offSet)
//            ->limit(8)
            ->get();

        foreach ($product_warehouse_data as $product_warehouse) {
            if ($product_warehouse->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $product_warehouse->product_id)
                    ->where('id', $product_warehouse->product_variant_id)
                    ->where('deleted_at', null)
                    ->first();

                $item['product_variant_id'] = $product_warehouse->product_variant_id;
                $item['Variant'] = $productsVariants->name;
                $item['code'] = $productsVariants->name . '-' . $product_warehouse['product']->code;

            } else if ($product_warehouse->product_variant_id === null) {
                $item['product_variant_id'] = null;
                $item['Variant'] = null;
                $item['code'] = $product_warehouse['product']->code;
            }
            $item['id'] = $product_warehouse->product_id;
            $item['barcode'] = $product_warehouse['product']->code;
            $item['name'] = $product_warehouse['product']->name;
//            $firstimage = explode(',', $product_warehouse['product']->image);
//            $item['image'] = $firstimage[0];

            if ($product_warehouse['product']['unitSale']->operator == '/') {
                $item['qte_sale'] = $product_warehouse->qty * $product_warehouse['product']['unitSale']->operator_value;
                $price = $product_warehouse['product']->price / $product_warehouse['product']['unitSale']->operator_value;

            } else {
                $item['qte_sale'] = $product_warehouse->qty / $product_warehouse['product']['unitSale']->operator_value;
                $price = $product_warehouse['product']->price * $product_warehouse['product']['unitSale']->operator_value;

            }
            $item['unitSale'] = $product_warehouse['product']['unitSale']->ShortName;
            $item['qte'] = $product_warehouse->qty;

            $item['Net_price'] = $price;
            if ($product_warehouse['product']->TaxNet !== 0.0) {

                //Exclusive
                if ($product_warehouse['product']->tax_method == '1') {
                    $tax_price = $price * $product_warehouse['product']->TaxNet / 100;

                    $item['Net_price'] = $price + $tax_price;
                }
            }

            $data->add($item);
        }

        return response()->json([
            'products' => $data,
//            'totalRows' => $totalRows,
        ]);
    }

    //--------------------- Get Element POS ------------------------\\

    public function GetELementPos(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'Sales_pos', Sale::class);
        $clients = Client::where('deleted_at', '=', null)->get(['id', 'company_name as name']);
        $settings = Setting::where('deleted_at', '=', null)->first();

        //get warehouses assigned to user
        $defaultWarehouse = '';
        $user_auth = auth()->user();
        if ($user_auth->is_all_warehouses) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            if ($settings->warehouse_id) {
                if (Warehouse::where('id', $settings->warehouse_id)->where('deleted_at', '=', null)->first()) {
                    $defaultWarehouse = $settings->warehouse_id;
                }
            }
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
            if ($settings->warehouse_id) {
                if (Warehouse::where('id', $settings->warehouse_id)->whereIn('id', $warehouses_id)->where('deleted_at', '=', null)->first()) {
                    $defaultWarehouse = $settings->warehouse_id;
                }
            }
        }

        $defaultClient = '';
        if ($settings->client_id) {
            if (Client::where('id', $settings->client_id)->where('deleted_at', '=', null)->first()) {
                $defaultClient = $settings->client_id;
            }
        }
        $categories = Category::where('deleted_at', '=', null)->get(['id', 'name']);
        $sales_types = SalesType::where('deleted_at', '=', null)->get(['id', 'name']);

        Inertia::share('titlePage', 'POS');
        return Inertia::render('Pos', [
            'defaultWarehouse' => $defaultWarehouse,
            'defaultClient' => $defaultClient,
            'clients' => $clients,
            'sales_types' => $sales_types,
            'warehouses' => $warehouses,
            'categories' => $categories,
        ]);
    }
}
