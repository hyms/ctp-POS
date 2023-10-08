<?php

namespace App\Http\Controllers;

use App\Mail\SaleMail;
use App\Models\Client;
use App\Models\PaymentSale;
use App\Models\PosSetting;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\ProductVariant;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\SalesType;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use App\utils\helpers;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class SalesController extends Controller
{

    //------------- GET ALL SALES -----------\\

    public function index(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Sale::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $filter = collect($request->get('filter'));
        $warehouses = helpers::getWarehouses(auth()->user());
        $Sales = Sale::with('facture', 'client', 'warehouse', 'user', 'userpos', 'sales_type')
            ->where('deleted_at', '=', null)
            ->whereIn('warehouse_id', $warehouses->pluck('id'))
            ->orderBy('updated_at', 'desc');
        if ($filter->count() > 0) {
            $filterData = false;
            if (!empty($filter->get('start_date')) && $filter->get('end_date')) {
                $Sales = $Sales->whereBetween('date', [Carbon::parse($filter->get('start_date')), Carbon::parse($filter->get('end_date'))]);
                $filterData = true;
            }
            if (!empty($filter->get('sale_ref'))) {
                $Sales = $Sales->where('Ref', 'like', "%{$filter->get('sale_ref')}%");
                $filterData = true;
            }
            if (!empty($filter->get('client'))) {
                $Sales = $Sales->where('client_id', '=', $filter->get('client'));
                $filterData = true;
            }
            if (!empty($filter->get('sale_type'))) {
                $Sales = $Sales->where('sales_type_id', '=', $filter->get('sale_type'));
                $filterData = true;
            }
            if (!empty($filter->get('statut'))) {
                $Sales = $Sales->where('statut', '=', $filter->get('statut'));
                $filterData = true;
            }
            if (!empty($filter->get('status_payment'))) {
                $Sales = $Sales->where('payment_statut', '=', $filter->get('status_payment'));
                $filterData = true;
            }
            if (!$filterData) {
                $Sales = $Sales->limit(1000);
            }
        } else {
            $Sales = $Sales->limit(500);
        }
        $Sales = $Sales->get();
        $data = collect();
        foreach ($Sales as $Sale) {

            $item['id'] = $Sale['id'];
            $item['date'] = $Sale['date'];
            $item['Ref'] = $Sale['Ref'];
            $item['created_by'] = $Sale->userpos ? $Sale->userpos->username : $Sale->user->username;
//            $statut = match ($Sale['statut']) {
//                'completed' => 'Completado',
//                'pending' => 'Pendiente',
//                'ordered' => 'Ordenado',
//                default => "",
//            };
            $item['statut'] = $Sale['statut'];
            $item['shipping_status'] = $Sale['shipping_status'];
            $item['discount'] = $Sale['discount'];
            $item['shipping'] = $Sale['shipping'];
            $item['warehouse_name'] = $Sale->warehouse?->name;
            $item['client_id'] = $Sale->client?->id;
            $item['client_name'] = $Sale->client?->company_name;
            $item['client_email'] = $Sale->client?->email;
            $item['client_tele'] = $Sale->client?->phone;
            $item['client_code'] = $Sale->client?->code;
            $item['client_adr'] = $Sale->client?->adresse;
            $item['GrandTotal'] = number_format($Sale['GrandTotal'], 2, '.', '');
            $item['paid_amount'] = number_format($Sale['paid_amount'], 2, '.', '');
            $item['due'] = number_format($item['GrandTotal'] - $item['paid_amount'], 2, '.', '');
//            $payment_status = match ($Sale['payment_statut']) {
//                'unpaid' => 'Deuda',
//                'paid' => 'Pagado',
//                'partial' => 'Parcial',
//                default => "",
//            };
            $item['payment_status'] = $Sale['payment_statut'];
            $item['sales_type'] = $Sale['sales_type']['name'];

            if (SaleReturn::where('sale_id', $Sale['id'])->where('deleted_at', '=', null)->exists()) {
                $sellReturn = SaleReturn::where('sale_id', $Sale['id'])->where('deleted_at', '=', null)->first();
                $item['salereturn_id'] = $sellReturn->id;
                $item['sale_has_return'] = 'yes';
            } else {
                $item['sale_has_return'] = 'no';
            }

            $data->add($item);
        }

        $customers = client::where('deleted_at', '=', null)->get(['id', 'company_name as name']);

        //get warehouses assigned to user


        $sales_types = SalesType::where('deleted_at', '=', null)->get(['id', 'name']);

        Inertia::share('titlePage', 'Lista de Ordenes');
        return Inertia::render('Sales/Index_Sale', [
            'sales' => $data,
            'sales_types' => $sales_types,
            'customers' => $customers,
            'warehouses' => $warehouses,
            'filter_form' => $filter
        ]);
    }

    //------------- STORE NEW SALE-----------\\

    public function store(Request $request)
    {

//        $this->authorizeForUser($request->user('api'), 'create', Sale::class);

        request()->validate([
            'client_id' => 'required',
            'warehouse_id' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $helpers = new helpers();
            $order = new Sale;

            $order->is_pos = 0;
            $order->sales_type_id = $request->sales_type;
            $order->date = $request->date;
            $order->Ref = $this->getNumberOrder($request->sales_type);
            $order->client_id = $request->client_id;
            $order->GrandTotal = $request->GrandTotal;
            $order->warehouse_id = $request->warehouse_id;
            $order->tax_rate = $request->tax_rate;
            $order->TaxNet = $request->TaxNet;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->shipping_status = "";
            $order->statut = $request->statut;
            $order->payment_statut = 'unpaid';
            $order->notes = $request->notes;
            $order->user_id = Auth::user()->id;

            $order->save();

            $data = $request['details'];
            $orderDetails = collect();
            foreach ($data as $key => $value) {
                $unit = Unit::where('id', $value['sale_unit_id'])
                    ->first();
                $orderDetails->add([
                    'date' => $request->date,
                    'sale_id' => $order->id,
                    'sale_unit_id' => $value['sale_unit_id'],
                    'quantity' => $value['quantity'],
                    'price' => $value['Unit_price'],
                    'TaxNet' => $value['tax_percent'],
                    'tax_method' => $value['tax_method'],
                    'discount' => $value['discount'],
                    'discount_method' => $value['discount_Method'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'total' => $value['subtotal'],
                ]);


                if ($order->statut == "completed") {
                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $order->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                    } else {
                        $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $order->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                    }
                    if ($unit && $product_warehouse) {
                        $product_warehouse->qty -= $unit->operator == '/' ? $value['quantity'] / $unit->operator_value : $value['quantity'] * $unit->operator_value;
                        $product_warehouse->save();
                    }
                }
            }
            SaleDetail::insert($orderDetails->toArray());

            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');

            if ($request->payment['status'] != 'pending') {
                $sale = Sale::findOrFail($order->id);
                // Check If User Has Permission view All Records
//                if (!$view_records) {
//                    // Check If User->id === sale->id
//                    $this->authorizeForUser($request->user('api'), 'check_record', $sale);
//                }


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
                            'Ref' => app(PaymentSalesController::class)->getNumberOrder(),
                            'date' => Carbon::now(),
                            'Reglement' => $request->payment['Reglement'],
                            'montant' => $request['amount'],
                            'change' => $request['change'],
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

            }

        }, 10);

        return response()->json(['success' => true]);
    }


    //------------- UPDATE SALE -----------

    public function update(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', Sale::class);

        request()->validate([
            'warehouse_id' => 'required',
            'client_id' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {

//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $current_Sale = Sale::findOrFail($id);

            if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
                return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
            } else {
                // Check If User Has Permission view All Records
//                if (!$view_records) {
//                    // Check If User->id === Sale->id
//                    $this->authorizeForUser($request->user('api'), 'check_record', $current_Sale);
//                }
                $old_sale_details = SaleDetail::where('sale_id', $id)->get();
                $new_sale_details = collect($request['details']);

                foreach ($old_sale_details as $key => $value) {

                    //check if detail has sale_unit_id Or Null
                    if ($value['sale_unit_id'] !== null) {
                        $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                    } else {
                        $product_unit_sale_id = Product::with('unitSale')
                            ->where('id', $value['product_id'])
                            ->first();
                        $old_unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                    }

                    if ($value['sale_unit_id'] !== null) {
                        $this->save_product_warehouse($current_Sale, $value, $old_unit);
                        // Delete Detail
                        if ($new_sale_details->doesntContain('id', '=', $value->id)) {
                            $SaleDetail = SaleDetail::findOrFail($value->id);
                            $SaleDetail->delete();
                        }
                    }
                }

                // Update Data with New request
                foreach ($new_sale_details as $prd => $prod_detail) {
                    $item = collect($prod_detail);
                    if ($item['sale_unit_id'] !== 0) {
                        $unit_prod = Unit::where('id', $item['sale_unit_id'])->first();

                        if ($request['statut'] == "completed") {
                            $product_warehouse = $this->getProduct_warehouse($item, $request);
                            if ($product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qty -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qty -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }

                        $orderDetails['sale_id'] = $id;
                        $orderDetails['date'] = $request['date'];
                        $orderDetails['price'] = $item['Unit_price'];
                        $orderDetails['sale_unit_id'] = $item['sale_unit_id'];
                        $orderDetails['TaxNet'] = $item['tax_percent'];
                        $orderDetails['tax_method'] = $item['tax_method'];
                        $orderDetails['discount'] = $item['discount'];
                        $orderDetails['discount_method'] = $item['discount_Method'];
                        $orderDetails['quantity'] = $item['quantity'];
                        $orderDetails['product_id'] = $item['product_id'];
                        $orderDetails['product_variant_id'] = $item['product_variant_id'];
                        $orderDetails['total'] = $item['subtotal'];

                        if ($old_sale_details->doesntContain('id', '=', $item->get('id'))) {
                            $orderDetails['date'] = Carbon::now();
                            $orderDetails['sale_unit_id'] = $unit_prod ? $unit_prod->id : Null;
                            SaleDetail::create($orderDetails);
                        } else {
                            SaleDetail::where('id', $item['id'])->update($orderDetails);
                        }
                    }
                }

                $due = $request['GrandTotal'] - $current_Sale->paid_amount;
                if ($due === 0.0 || $due < 0.0) {
                    $payment_statut = 'paid';
                } else if ($due != $request['GrandTotal']) {
                    $payment_statut = 'partial';
                } else if ($due == $request['GrandTotal']) {
                    $payment_statut = 'unpaid';
                }

                $current_Sale->update([
                    'date' => $request['date'],
                    'client_id' => $request['client_id'],
                    'user_id' => Auth::user()->id,
                    'warehouse_id' => $request['warehouse_id'],
                    'notes' => $request['notes'],
                    'statut' => $request['statut'],
                    'tax_rate' => $request['tax_rate'],
                    'TaxNet' => $request['TaxNet'],
                    'discount' => $request['discount'],
                    'shipping' => $request['shipping'],
                    'GrandTotal' => $request['GrandTotal'],
                    'payment_statut' => $payment_statut,
                ]);
            }

        }, 10);

        return response()->json(['success' => true]);
    }

    public function getProduct_warehouse(mixed $value, $current_Sale): mixed
    {
        if ($value['product_variant_id'] !== null) {
            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                ->where('warehouse_id', $current_Sale->warehouse_id)
                ->where('product_id', $value['product_id'])
                ->where('product_variant_id', $value['product_variant_id'])
                ->first();
        } else {
            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                ->where('warehouse_id', $current_Sale->warehouse_id)
                ->where('product_id', $value['product_id'])
                ->first();
        }
        return $product_warehouse;
    }

    //------------- Remove SALE BY ID -----------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Sale::class);

        DB::transaction(function () use ($id, $request) {
//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $current_Sale = Sale::findOrFail($id);
            $old_sale_details = SaleDetail::where('sale_id', $id)->get();
            //$shipment_data = Shipment::where('sale_id', $id)->first();

            if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
                return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
            } else {

                // Check If User Has Permission view All Records
//                if (!$view_records) {
//                    // Check If User->id === Sale->id
//                    $this->authorizeForUser($request->user('api'), 'check_record', $current_Sale);
//                }
                foreach ($old_sale_details as $key => $value) {

                    //check if detail has sale_unit_id Or Null
                    if ($value['sale_unit_id'] !== null) {
                        $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                    } else {
                        $product_unit_sale_id = Product::with('unitSale')
                            ->where('id', $value['product_id'])
                            ->first();
                        $old_unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                    }

                    $this->save_product_warehouse($current_Sale, $value, $old_unit);

                }

//                if ($shipment_data) {
//                    $shipment_data->delete();
//                }
                $current_Sale->details()->delete();
                $current_Sale->update([
                    'deleted_at' => Carbon::now(),
                    'shipping_status' => '',
                ]);


                $Payment_Sale_data = PaymentSale::where('sale_id', $id)->get();
                foreach ($Payment_Sale_data as $Payment_Sale) {
                    $Payment_Sale->delete();
                }
            }

        }, 10);

        return response()->json(['success' => true]);
    }

    //---------------- Get Details Sale-----------------\\

    public function show(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'view', Sale::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $sale_data = Sale::with(['details.product.unitSale', 'client'])
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = collect();

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === sale->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $sale_data);
//        }

        $sale_details['id'] = $sale_data->id;
        $sale_details['Ref'] = $sale_data->Ref;
        $sale_details['date'] = $sale_data->date;
        $sale_details['note'] = $sale_data->notes;
        $sale_details['statut'] = $sale_data->statut;
        $sale_details['warehouse'] = $sale_data['warehouse']->name;
        $sale_details['discount'] = $sale_data->discount;
        $sale_details['shipping'] = $sale_data->shipping;
        $sale_details['tax_rate'] = $sale_data->tax_rate;
        $sale_details['TaxNet'] = $sale_data->TaxNet;
        $sale_details['client_name'] = $sale_data->client?->name;
        $sale_details['client_company_name'] = $sale_data->client?->company_name;
        $sale_details['client_phone'] = $sale_data->client->phone;
        $sale_details['client_adr'] = $sale_data->client->adresse;
        $sale_details['client_email'] = $sale_data->client->email;
        $sale_details['client_tax'] = $sale_data->client->tax_number;
        $sale_details['GrandTotal'] = number_format($sale_data->GrandTotal, 2, '.', '');
        $sale_details['paid_amount'] = number_format($sale_data->paid_amount, 2, '.', '');
        $sale_details['due'] = number_format($sale_details['GrandTotal'] - $sale_details['paid_amount'], 2, '.', '');
        $sale_details['payment_status'] = $sale_data->payment_statut;

        if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
            $sellReturn = SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->first();
            $sale_details['salereturn_id'] = $sellReturn->id;
            $sale_details['sale_has_return'] = 'yes';
        } else {
            $sale_details['sale_has_return'] = 'no';
        }

        foreach ($sale_data['details'] as $detail) {

            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
            }

            if ($detail->product_variant_id) {

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];

            } else {
                $data['code'] = $detail['product']['code'];
            }

            $data['quantity'] = $detail->quantity;
            $data['total'] = $detail->total;
            $data['name'] = $detail['product']['name'];
            $data['price'] = $detail->price;
            $data['unit_sale'] = $unit->ShortName;

            if ($detail->discount_method == '2') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->price * $detail->discount / 100;
            }

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
            $data['Unit_price'] = $detail->price;
            $data['discount'] = $detail->discount;

            if ($detail->tax_method == '1') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = $tax_price;
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
            }

            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $details->add($data);
        }

        $company = Setting::where('deleted_at', '=', null)->first();


        Inertia::share('titlePage', 'Lista de Ordenes');
        return Inertia::render('Sales/Detail_Sale', [
            'details' => $details,
            'sale' => $sale_details,
            'company' => $company,
        ]);

    }

    //-------------- Print Invoice ---------------\\

    public function Print_Invoice_POS(Request $request, $id)
    {
        $helpers = new helpers();
        $details = collect();

        $sale = Sale::with(['details.product.unitSale', 'warehouse', 'client'])
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $item['id'] = $sale->id;
        $item['Ref'] = $sale->Ref;
        $item['date'] = Carbon::parse($sale->date)->format('d-m-Y');
        $item['discount'] = number_format($sale->discount, 2, '.', '');
        $item['shipping'] = number_format($sale->shipping, 2, '.', '');
        $item['taxe'] = number_format($sale->TaxNet, 2, '.', '');
        $item['tax_rate'] = $sale->tax_rate;
        $item['client_name'] = $sale['client']->company_name;
        $item['warehouse'] = $sale['warehouse']->name;
        $item['GrandTotal'] = number_format($sale->GrandTotal, 2, '.', '');
        $item['paid_amount'] = number_format($sale->paid_amount, 2, '.', '');
        $item['notes'] = $sale['notes'];

        foreach ($sale['details'] as $detail) {

            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
            }

            if ($detail->product_variant_id) {

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];
                $data['name'] = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
            }


            $data['quantity'] = number_format($detail->quantity, 2, '.', '');
            $data['total'] = number_format($detail->total, 2, '.', '');
            $data['unit_sale'] = $unit->ShortName;

            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $details->add($data);
        }

        $payments = PaymentSale::with('sale')
            ->where('sale_id', $id)
            ->orderBy('id', 'DESC')
            ->get();

        $settings = Setting::where('deleted_at', '=', null)->first();
        $pos_settings = PosSetting::where('deleted_at', '=', null)->first();
        $symbol = $helpers->Get_Currency_Code();

        return response()->json([
            'symbol' => $symbol,
            'payments' => $payments,
            'setting' => $settings,
            'pos_settings' => $pos_settings,
            'sale' => $item,
            'details' => $details,
        ]);

    }

    //------------- GET PAYMENTS SALE -----------\\

    public function Payments_Sale(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'view', PaymentSale::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $Sale = Sale::findOrFail($id);

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === Sale->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $Sale);
//        }

        $payments = PaymentSale::with('sale')
            ->where('sale_id', $id)
//            ->where(function ($query) use ($view_records) {
//                if (!$view_records) {
//                    return $query->where('user_id', '=', Auth::user()->id);
//                }
//            })
            ->orderBy('id', 'DESC')->get();

        $due = $Sale->GrandTotal - $Sale->paid_amount;

        return response()->json(['payments' => $payments, 'due' => $due]);

    }

    //------------- Reference Number Order SALE -----------\\

    public function getNumberOrder($type)
    {
        $last = DB::table('sales')->latest('id')->where('sales_type_id', '=', $type)->first();
        $base_code = DB::table('sales_type')->where('id', '=', $type)->first();
        return helpers::get_code($last?->Ref, $base_code->code);
    }

    //------------- SALE PDF -----------\\

    public function Sale_PDF(Request $request, $id)
    {

        $details = collect();
        $helpers = new helpers();
        $sale_data = Sale::with(['details.product.unitSale', 'client'])
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $sale['client_name'] = $sale_data['client']?->name;
        $sale['client_company_name'] = $sale_data['client']?->company_name;
        $sale['client_phone'] = $sale_data['client']->phone;
        $sale['client_adr'] = $sale_data['client']->adresse;
        $sale['client_email'] = $sale_data['client']->email;
        $sale['client_tax'] = $sale_data['client']->tax_number;
        $sale['TaxNet'] = number_format($sale_data->TaxNet, 2, '.', '');
        $sale['discount'] = number_format($sale_data->discount, 2, '.', '');
        $sale['shipping'] = number_format($sale_data->shipping, 2, '.', '');
        $sale['statut'] = $sale_data->statut;
        $sale['Ref'] = $sale_data->Ref;
        $sale['date'] = $sale_data->date;
        $sale['GrandTotal'] = number_format($sale_data->GrandTotal, 2, '.', '');
        $sale['paid_amount'] = number_format($sale_data->paid_amount, 2, '.', '');
        $sale['due'] = number_format($sale['GrandTotal'] - $sale['paid_amount'], 2, '.', '');
        $sale['payment_status'] = $sale_data->payment_statut;

        $detail_id = 0;
        foreach ($sale_data['details'] as $detail) {

            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
            }

            if ($detail->product_variant_id) {

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];
            } else {
                $data['code'] = $detail['product']['code'];
            }

            $data['detail_id'] = $detail_id += 1;
            $data['quantity'] = number_format($detail->quantity, 2, '.', '');
            $data['total'] = number_format($detail->total, 2, '.', '');
            $data['name'] = $detail['product']['name'];
            $data['unitSale'] = $unit->ShortName;
            $data['price'] = number_format($detail->price, 2, '.', '');

            if ($detail->discount_method == '2') {
                $data['DiscountNet'] = number_format($detail->discount, 2, '.', '');
            } else {
                $data['DiscountNet'] = number_format($detail->price * $detail->discount / 100, 2, '.', '');
            }

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
            $data['Unit_price'] = number_format($detail->price, 2, '.', '');
            $data['discount'] = number_format($detail->discount, 2, '.', '');

            if ($detail->tax_method == '1') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = number_format($tax_price, 2, '.', '');
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                $data['taxe'] = number_format($detail->price - $data['Net_price'] - $data['DiscountNet'], 2, '.', '');
            }

            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $details->add($data);
        }
        $settings = Setting::where('deleted_at', '=', null)->first();
        $symbol = $helpers->Get_Currency_Code();

        $Html = view('pdf.sale_pdf', [
            'symbol' => $symbol,
            'setting' => $settings,
            'sale' => $sale,
            'details' => $details,
        ])->render();

        $pdf = PDF::loadHTML($Html);
        return $pdf->download('sale.pdf');

    }

    //----------------Show Form Create Sale ---------------\\

    public function create(Request $request)
    {

//        $this->authorizeForUser($request->user('api'), 'create', Sale::class);

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());

        $clients = Client::select(['id', 'company_name as name'])->where('deleted_at', '=', null)->get();
//        $stripe_key = config('app.STRIPE_KEY');
        $sales_types = SalesType::where('deleted_at', '=', null)->get(['id', 'name']);
        Inertia::share('titlePage', 'Nueva Orden');
        return Inertia::render('Sales/Form_Sale', [
//            'stripe_key' => $stripe_key,
            'clients' => helpers::to_select_vuetify($clients),
            'warehouses' => helpers::to_select_vuetify($warehouses),
            'sales_types' => helpers::to_select_vuetify($sales_types)
        ]);

    }

    //------------- Show Form Edit Sale -----------\\

    public function edit(Request $request, $id)
    {
        if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
            return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
        } else {
//            $this->authorizeForUser($request->user('api'), 'update', Sale::class);
//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $Sale_data = Sale::with('details.product.unitSale')
                ->where('deleted_at', '=', null)
                ->findOrFail($id);
            $details = collect();
            // Check If User Has Permission view All Records
//            if (!$view_records) {
//                // Check If User->id === sale->id
//                $this->authorizeForUser($request->user('api'), 'check_record', $Sale_data);
//            }

            $sale['client_id'] = '';
            if ($Sale_data->client_id) {
                if (Client::where('id', $Sale_data->client_id)
                    ->where('deleted_at', '=', null)
                    ->first()) {
                    $sale['client_id'] = $Sale_data->client_id;
                }
            }

            $sale['warehouse_id'] = '';
            if ($Sale_data->warehouse_id) {
                if (Warehouse::where('id', $Sale_data->warehouse_id)
                    ->where('deleted_at', '=', null)
                    ->first()) {
                    $sale['warehouse_id'] = $Sale_data->warehouse_id;
                }
            }

            $sale['id'] = $Sale_data->id;
            $sale['sales_type_id'] = $Sale_data->sales_type_id;
            $sale['date'] = $Sale_data->date;
            $sale['tax_rate'] = $Sale_data->tax_rate;
            $sale['TaxNet'] = $Sale_data->TaxNet;
            $sale['discount'] = $Sale_data->discount;
            $sale['shipping'] = $Sale_data->shipping;
            $sale['statut'] = $Sale_data->statut;
            $sale['notes'] = $Sale_data->notes;

            $detail_id = 0;
            foreach ($Sale_data['details'] as $detail) {

                //check if detail has sale_unit_id Or Null
                if ($detail->sale_unit_id !== null) {
                    $unit = Unit::where('id', $detail->sale_unit_id)->first();
                    $data['no_unit'] = 1;
                } else {
                    $product_unit_sale_id = Product::with('unitSale')
                        ->where('id', $detail->product_id)
                        ->first();
                    $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                    $data['no_unit'] = 0;
                }

                if ($detail->product_variant_id) {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('deleted_at', '=', null)
                        ->where('product_variant_id', $detail->product_variant_id)
                        ->where('warehouse_id', $Sale_data->warehouse_id)
                        ->first();

                    $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                        ->where('id', $detail->product_variant_id)->first();

                    $item_product ? $data['del'] = 0 : $data['del'] = 1;
                    $data['product_variant_id'] = $detail->product_variant_id;
                    $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];

                } else {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('deleted_at', '=', null)->where('warehouse_id', $Sale_data->warehouse_id)
                        ->where('product_variant_id', '=', null)->first();

                    $item_product ? $data['del'] = 0 : $data['del'] = 1;
                    $data['product_variant_id'] = null;
                    $data['code'] = $detail['product']['code'];

                }
                if ($unit && $unit->operator == '/') {
                    $data['stock'] = $item_product ? $item_product->qte * $unit->operator_value : 0;
                } else if ($unit && $unit->operator == '*') {
                    $data['stock'] = $item_product ? $item_product->qte / $unit->operator_value : 0;
                } else {
                    $data['stock'] = 0;
                }

                $data['id'] = $detail->id;
                $data['detail_id'] = $detail_id += 1;
                $data['product_id'] = $detail->product_id;
                $data['total'] = $detail->total;
                $data['name'] = $detail['product']['name'];
                $data['quantity'] = $detail->quantity;
                $data['qte_copy'] = $detail->quantity;
                $data['etat'] = 'current';
                $data['unitSale'] = $unit->ShortName;
                $data['sale_unit_id'] = $unit->id;

                if ($detail->discount_method == '2') {
                    $data['DiscountNet'] = $detail->discount;
                } else {
                    $data['DiscountNet'] = $detail->price * $detail->discount / 100;
                }

                $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
                $data['Unit_price'] = $detail->price;

                $data['tax_percent'] = $detail->TaxNet;
                $data['tax_method'] = $detail->tax_method;
                $data['discount'] = $detail->discount;
                $data['discount_Method'] = $detail->discount_method;

                if ($detail->tax_method == '1') {
                    $data['Net_price'] = $detail->price - $data['DiscountNet'];
                    $data['taxe'] = $tax_price;
                } else {
                    $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                    $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                }
                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);

                $details->add($data);
            }

            //get warehouses assigned to user
            $user_auth = auth()->user();
            if ($user_auth->is_all_warehouses) {
                $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            } else {
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
                $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
            }

            $clients = Client::where('deleted_at', '=', null)->get(['id', 'name']);
            $sales_types = SalesType::where('deleted_at', '=', null)->get(['id', 'name']);
            Inertia::share('titlePage', 'Editar Orden');
            return Inertia::render('Sales/Form_Sale', [
                'clients' => helpers::to_select_vuetify($clients),
                'warehouses' => helpers::to_select_vuetify($warehouses),
                'sales_types' => helpers::to_select_vuetify($sales_types),
                'details' => $details,
                'sale' => $sale,
            ]);
        }
    }


    //------------- SEND SALE TO EMAIL -----------\\

    public function Send_Email(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Sale::class);

        $sale['id'] = $request->id;
        $sale['Ref'] = $request->Ref;
        $settings = Setting::where('deleted_at', '=', null)->first();
        $sale['company_name'] = $settings->CompanyName;
        $pdf = $this->Sale_PDF($request, $sale['id']);
        $this->Set_config_mail(); // Set_config_mail => BaseController
        $mail = Mail::to($request->to)->send(new SaleMail($sale, $pdf));
        return $mail;
    }

    //------------- Show Form Convert To Sale -----------\\

    public function Elemens_Change_To_Sale(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'update', Quotation::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $Quotation = Quotation::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);
        $details = array();
        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === Quotation->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $Quotation);
//        }

        if ($Quotation->client_id) {
            if (Client::where('id', $Quotation->client_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $sale['client_id'] = $Quotation->client_id;
            } else {
                $sale['client_id'] = '';
            }
        } else {
            $sale['client_id'] = '';
        }

        if ($Quotation->warehouse_id) {
            if (Warehouse::where('id', $Quotation->warehouse_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $sale['warehouse_id'] = $Quotation->warehouse_id;
            } else {
                $sale['warehouse_id'] = '';
            }
        } else {
            $sale['warehouse_id'] = '';
        }

        $sale['date'] = $Quotation->date;
        $sale['TaxNet'] = $Quotation->TaxNet;
        $sale['tax_rate'] = $Quotation->tax_rate;
        $sale['discount'] = $Quotation->discount;
        $sale['shipping'] = $Quotation->shipping;
        $sale['statut'] = 'pending';
        $sale['notes'] = $Quotation->notes;

        $detail_id = 0;
        foreach ($Quotation['details'] as $detail) {

            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();

                if ($detail->product_variant_id) {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('product_variant_id', $detail->product_variant_id)
                        ->where('warehouse_id', $Quotation->warehouse_id)
                        ->where('deleted_at', '=', null)
                        ->first();
                    $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                        ->where('id', $detail->product_variant_id)->where('deleted_at', null)->first();

                    $item_product ? $data['del'] = 0 : $data['del'] = 1;
                    $data['product_variant_id'] = $detail->product_variant_id;
                    $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];

                    if ($unit && $unit->operator == '/') {
                        $data['stock'] = $item_product ? $item_product->qte / $unit->operator_value : 0;
                    } else if ($unit && $unit->operator == '*') {
                        $data['stock'] = $item_product ? $item_product->qte * $unit->operator_value : 0;
                    } else {
                        $data['stock'] = 0;
                    }

                } else {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('warehouse_id', $Quotation->warehouse_id)
                        ->where('product_variant_id', '=', null)
                        ->where('deleted_at', '=', null)
                        ->first();

                    $item_product ? $data['del'] = 0 : $data['del'] = 1;
                    $data['product_variant_id'] = null;
                    $data['code'] = $detail['product']['code'];

                    if ($unit && $unit->operator == '/') {
                        $data['stock'] = $item_product ? $item_product->qte * $unit->operator_value : 0;
                    } else if ($unit && $unit->operator == '*') {
                        $data['stock'] = $item_product ? $item_product->qte / $unit->operator_value : 0;
                    } else {
                        $data['stock'] = 0;
                    }
                }

                $data['id'] = $id;
                $data['detail_id'] = $detail_id += 1;
                $data['quantity'] = $detail->quantity;
                $data['product_id'] = $detail->product_id;
                $data['total'] = $detail->total;
                $data['name'] = $detail['product']['name'];
                $data['etat'] = 'current';
                $data['qte_copy'] = $detail->quantity;
                $data['unitSale'] = $unit->ShortName;
                $data['sale_unit_id'] = $unit->id;

                $data['is_imei'] = $detail['product']['is_imei'];
                $data['imei_number'] = $detail->imei_number;

                if ($detail->discount_method == '2') {
                    $data['DiscountNet'] = $detail->discount;
                } else {
                    $data['DiscountNet'] = $detail->price * $detail->discount / 100;
                }
                $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
                $data['Unit_price'] = $detail->price;
                $data['tax_percent'] = $detail->TaxNet;
                $data['tax_method'] = $detail->tax_method;
                $data['discount'] = $detail->discount;
                $data['discount_Method'] = $detail->discount_method;

                if ($detail->tax_method == '1') {
                    $data['Net_price'] = $detail->price - $data['DiscountNet'];
                    $data['taxe'] = $tax_price;
                    $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
                } else {
                    $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                    $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                    $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
                }

                $details[] = $data;
            }
        }

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());

        $clients = Client::where('deleted_at', '=', null)->get(['id', 'name']);

        return response()->json([
            'details' => $details,
            'sale' => $sale,
            'clients' => $clients,
            'warehouses' => $warehouses,
        ]);

    }

    //------------------- get_Products_by_sale -----------------\\

    public function get_Products_by_sale(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'create', SaleReturn::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $SaleReturn = Sale::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = array();

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === SaleReturn->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $SaleReturn);
//        }

        $Return_detail['client_id'] = $SaleReturn->client_id;
        $Return_detail['warehouse_id'] = $SaleReturn->warehouse_id;
        $Return_detail['sale_id'] = $SaleReturn->id;
        $Return_detail['tax_rate'] = 0;
        $Return_detail['TaxNet'] = 0;
        $Return_detail['discount'] = 0;
        $Return_detail['shipping'] = 0;
        $Return_detail['statut'] = "received";
        $Return_detail['notes'] = "";

        $detail_id = 0;
        foreach ($SaleReturn['details'] as $detail) {

            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
                $data['no_unit'] = 1;
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                $data['no_unit'] = 0;
            }

            if ($detail->product_variant_id) {
                $item_product = product_warehouse::where('product_id', $detail->product_id)
                    ->where('product_variant_id', $detail->product_variant_id)
                    ->where('deleted_at', '=', null)
                    ->where('warehouse_id', $SaleReturn->warehouse_id)
                    ->first();

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $item_product ? $data['del'] = 0 : $data['del'] = 1;
                $data['product_variant_id'] = $detail->product_variant_id;
                $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];

                if ($unit && $unit->operator == '/') {
                    $data['stock'] = $item_product ? $item_product->qte * $unit->operator_value : 0;
                } else if ($unit && $unit->operator == '*') {
                    $data['stock'] = $item_product ? $item_product->qte / $unit->operator_value : 0;
                } else {
                    $data['stock'] = 0;
                }

            } else {
                $item_product = product_warehouse::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $SaleReturn->warehouse_id)
                    ->where('deleted_at', '=', null)->where('product_variant_id', '=', null)
                    ->first();

                $item_product ? $data['del'] = 0 : $data['del'] = 1;
                $data['product_variant_id'] = null;
                $data['code'] = $detail['product']['code'];

                if ($unit && $unit->operator == '/') {
                    $data['stock'] = $item_product ? $item_product->qte * $unit->operator_value : 0;
                } else if ($unit && $unit->operator == '*') {
                    $data['stock'] = $item_product ? $item_product->qte / $unit->operator_value : 0;
                } else {
                    $data['stock'] = 0;
                }

            }

            $data['id'] = $detail->id;
            $data['detail_id'] = $detail_id += 1;
            $data['quantity'] = $detail->quantity;
            $data['sale_quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['name'] = $detail['product']['name'];
            $data['unitSale'] = $unit->ShortName;
            $data['sale_unit_id'] = $unit->id;
            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            if ($detail->discount_method == '2') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->price * $detail->discount / 100;
            }

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
            $data['Unit_price'] = $detail->price;
            $data['tax_percent'] = $detail->TaxNet;
            $data['tax_method'] = $detail->tax_method;
            $data['discount'] = $detail->discount;
            $data['discount_Method'] = $detail->discount_method;

            if ($detail->tax_method == '1') {

                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = $tax_price;
                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
            }

            $details[] = $data;
        }


        return response()->json([
            'details' => $details,
            'sale_return' => $Return_detail,
        ]);

    }

    /**
     * @param $current_Sale
     * @param mixed $value
     * @param $old_unit
     * @return void
     */
    public function save_product_warehouse($current_Sale, mixed $value, $old_unit): void
    {
        if ($current_Sale->statut == "completed") {
            $product_warehouse = $this->getProduct_warehouse($value, $current_Sale);

            if ($product_warehouse) {
                if ($old_unit->operator == '/') {
                    $product_warehouse->qty += $value['quantity'] / $old_unit->operator_value;
                } else {
                    $product_warehouse->qty += $value['quantity'] * $old_unit->operator_value;
                }
                $product_warehouse->save();
            }
        }
    }

}




