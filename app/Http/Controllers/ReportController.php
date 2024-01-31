<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
use App\Models\Client;
use App\Models\Expense;
use App\Models\PaymentPurchase;
use App\Models\PaymentPurchaseReturns;
use App\Models\PaymentSale;
use App\Models\PaymentSaleReturns;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\ProductVariant;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
use App\Models\Setting;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\Unit;
use App\Models\User;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PDF;

class ReportController extends Controller
{

    //----------- Get Last 5 Sales --------------\\

    public function Get_last_Sales()
    {

//        $Role = Auth::user()->roles()->first();
//        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $Sales = Sale::with('details', 'client', 'facture')->where('deleted_at', '=', null)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
        $data = collect();
        foreach ($Sales as $Sale) {

            $item['Ref'] = $Sale['Ref'];
            $item['statut'] = $Sale['statut'];
            $item['client_name'] = $Sale['client']['name'];
            $item['GrandTotal'] = $Sale['GrandTotal'];
            $item['paid_amount'] = $Sale['paid_amount'];
            $item['due'] = $Sale['GrandTotal'] - $Sale['paid_amount'];
            $item['payment_status'] = $Sale['payment_statut'];

            $data->add($item);
        }

        return response()->json($data);
    }


    //----------------- Customers Report -----------------------\\

    public function Client_Report(request $request)
    {
        if (!helpers::checkPermission('Reports_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $data = collect();

        $clients = Client::where('deleted_at', '=', null);
        $filter = collect($request->get('filter'));
        if (empty($filter->get('start_date'))) {
            $filter['start_date'] = Carbon::now()->startOfDay()->format('Y-m-d');
        }
        if (empty($filter->get('end_date'))) {
            $filter['end_date'] = Carbon::parse($filter['start_date'])->endOfDay()->format('Y-m-d');
        }
        $clients = $clients->orderByDesc('id')
            ->get();

        foreach ($clients as $client) {
            $item['total_sales'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->whereBetween('date', [$filter->get('start_date'), $filter->get('end_date')])
                ->count();

            $item['total_amount'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->whereBetween('date', [$filter->get('start_date'), $filter->get('end_date')])
                ->sum('GrandTotal');

            $item['total_paid'] = DB::table('sales')
                ->where('sales.deleted_at', '=', null)
                ->where('sales.client_id', $client->id)
                ->whereBetween('date', [$filter->get('start_date'), $filter->get('end_date')])
                ->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = DB::table('sale_returns')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->whereBetween('date', [$filter->get('start_date'), $filter->get('end_date')])
                ->sum('GrandTotal');

            $item['total_paid_return'] = DB::table('sale_returns')
                ->where('sale_returns.deleted_at', '=', null)
                ->where('sale_returns.client_id', $client->id)
                ->whereBetween('date', [$filter->get('start_date'), $filter->get('end_date')])
                ->sum('paid_amount');

            $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

            $item['name'] = $client->company_name;
            $item['phone'] = $client->phone;
            $item['code'] = $client->code;
            $item['id'] = $client->id;

            $data->add($item);
        }

        return response()->json(['report' => $data]);
    }

    //----------------- Customers Report By ID-----------------------\\

    public function Client_Report_detail(request $request, $id)
    {
        if (!helpers::checkPermission('Reports_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();

        $data['total_amount'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)
            ->sum('GrandTotal');

        $data['total_paid'] = DB::table('sales')
            ->where('sales.deleted_at', '=', null)
            ->where('sales.client_id', $client->id)
            ->sum('paid_amount');

        $data['due'] = $data['total_amount'] - $data['total_paid'];

        Inertia::share('titlePage', 'Detalle de Cliente');
        return Inertia::render('Reports/detail_Customer_Report', [
            'report' => $data,
            'client_id' => $id,
        ]);
    }

    //----------------- Provider Report By ID-----------------------\\

    public function Provider_Report_detail(request $request, $id)
    {
        if (!helpers::checkPermission('Reports_suppliers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $provider = Provider::where('deleted_at', '=', null)->findOrFail($id);

        $data['total_purchase'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->count();

        $data['total_amount'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)
            ->sum('GrandTotal');

        $data['total_paid'] = DB::table('purchases')
            ->where('purchases.deleted_at', '=', null)
            ->where('purchases.provider_id', $id)
            ->sum('paid_amount');

        $data['due'] = $data['total_amount'] - $data['total_paid'];

        return response()->json(['report' => $data]);

    }

    //-------------------- Get Sales By Clients -------------\\

    public function Sales_Client(request $request)
    {
        if (!helpers::checkPermission('Reports_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $sales = Sale::where('deleted_at', '=', null)->with('client', 'warehouse')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where('client_id', $request->get('id'));
        // Search With Multiple Param
//            ->where(function ($query) use ($request) {
//                return $query->when($request->filled('search'), function ($query) use ($request) {
//                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
//                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
//                        ->orWhere('payment_statut', 'like', "%{$request->search}%")
//                        ->orWhere(function ($query) use ($request) {
//                            return $query->whereHas('warehouse', function ($q) use ($request) {
//                                $q->where('name', 'LIKE', "%{$request->search}%");
//                            });
//                        })
//                        ->orWhere(function ($query) use ($request) {
//                            return $query->whereHas('client', function ($q) use ($request) {
//                                $q->where('name', 'LIKE', "%{$request->search}%");
//                            });
//                        });
//                });
//            });


        $sales = $sales
            ->orderByDesc('id')
            ->get();

        $data = collect();
        foreach ($sales as $sale) {
            $data->add([
                'id' => $sale->id,
                'date' => $sale->date,
                'Ref' => $sale->Ref,
                'warehouse_name' => $sale['warehouse']?->name,
                'client_name' => $sale['client']?->company_name,
                'client_code' => $sale['client']?->code,
                'statut' => $sale->statut,
                'GrandTotal' => $sale->GrandTotal,
                'paid_amount' => $sale->paid_amount,
                'due' => $sale->GrandTotal - $sale->paid_amount,
                'payment_status' => $sale->payment_statut,
                'shipping_status' => $sale->shipping_status,
            ]);
        }
        return response()->json([
            'sales' => $data,
        ]);

    }

    //-------------------- Get Payments By Clients -------------\\

    public function Payments_Client(request $request)
    {
        if (!helpers::checkPermission('Reports_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $payments = DB::table('payment_sales')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where('payment_sales.deleted_at', '=', null)
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->where('sales.client_id', $request->id)
            // Search With Multiple Param
//            ->where(function ($query) use ($request) {
//                return $query->when($request->filled('search'), function ($query) use ($request) {
//                    return $query->where('payment_sales.Ref', 'LIKE', "%{$request->search}%")
//                        ->orWhere('payment_sales.date', 'LIKE', "%{$request->search}%")
//                        ->orWhere('payment_sales.Reglement', 'LIKE', "%{$request->search}%");
//                });
//            })
            ->select(
                'payment_sales.date', 'payment_sales.Ref AS Ref', 'sales.Ref AS Sale_Ref',
                'payment_sales.Reglement', 'payment_sales.montant'
            );

        $payments = $payments
            ->orderBy('payment_sales.id', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments,
        ]);

    }

    //-------------------- Get Quotations By Clients -------------\\

    public function Quotations_Client(request $request)
    {

        if (!helpers::checkPermission('Reports_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $data = collect();

        $Quotations = Quotation::with('client', 'warehouse')
            ->where('deleted_at', '=', null)
            ->where('client_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            //Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $Quotations->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $Quotations = $Quotations
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($Quotations as $Quotation) {
            $data->add([
                'id' => $Quotation->id,
                'date' => $Quotation->date,
                'Ref' => $Quotation->Ref,
                'statut' => $Quotation->statut,
                'warehouse_name' => $Quotation['warehouse']->name,
                'client_name' => $Quotation['client']->name,
                'GrandTotal' => $Quotation->GrandTotal,

            ]);
        }

        return response()->json([
            'quotations' => $data,
            'totalRows' => $totalRows,
        ]);
    }

    //-------------------- Get Returns By Client -------------\\

    public function Returns_Client(request $request)
    {

        if (!helpers::checkPermission('Reports_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
        $data = collect();

        //  Check If User Has Permission Show All Records
        $Role = Auth::user()->roles()->first();
//        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $SaleReturn = SaleReturn::where('deleted_at', '=', null)->with('sale', 'client', 'warehouse')
            ->where('client_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $SaleReturn->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $SaleReturn = $SaleReturn
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($SaleReturn as $Sale_Return) {
            $item['id'] = $Sale_Return->id;
            $item['Ref'] = $Sale_Return->Ref;
            $item['statut'] = $Sale_Return->statut;
            $item['client_name'] = $Sale_Return['client']->name;
            $item['sale_ref'] = $Sale_Return['sale'] ? $Sale_Return['sale']->Ref : '---';
            $item['sale_id'] = $Sale_Return['sale'] ? $Sale_Return['sale']->id : NULL;
            $item['warehouse_name'] = $Sale_Return['warehouse']->name;
            $item['GrandTotal'] = $Sale_Return->GrandTotal;
            $item['paid_amount'] = $Sale_Return->paid_amount;
            $item['due'] = $Sale_Return->GrandTotal - $Sale_Return->paid_amount;
            $item['payment_status'] = $Sale_Return->payment_statut;

            $data->add($item);
        }

        return response()->json([
            'totalRows' => $totalRows,
            'returns_customer' => $data,
        ]);
    }


    //------------- Show Report Purchases ----------\\

    public function Report_Purchases(request $request)
    {
        if (!helpers::checkPermission('ReportPurchases')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
//        $order = $request->SortField;
//        $dir = $request->SortType;
        $helpers = new helpers();
        // Filter fields With Params to retrieve
        $param = array(
            0 => 'like',
            1 => 'like',
            2 => '=',
            3 => 'like',
            4 => '=',
        );
        $columns = array(
            0 => 'Ref',
            1 => 'statut',
            2 => 'provider_id',
            3 => 'payment_statut',
            4 => 'warehouse_id',
        );
        $data = collect();
        $total = 0;

        $Purchases = Purchase::select('purchases.*')
            ->with('facture', 'provider', 'warehouse')
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->where('purchases.deleted_at', '=', null)
            ->whereBetween('purchases.date', array($request->from, $request->to));

        //  Check If User Has Permission Show All Records
//        $Purchases = $helpers->Show_Records($Purchases);
        //Multiple Filter
        $Filtred = $helpers->filter($Purchases, $columns, $param, $request)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('GrandTotal', $request->search)
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $Filtred->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $Purchases = $Filtred
//            ->offset($offSet)
//            ->limit($perPage)
//            ->orderBy('purchases.' . $order, $dir)
            ->get();

        foreach ($Purchases as $Purchase) {

            $item['id'] = $Purchase->id;
            $item['date'] = $Purchase->date;
            $item['Ref'] = $Purchase->Ref;
            $item['warehouse_name'] = $Purchase['warehouse']->name;
            $item['discount'] = $Purchase->discount;
            $item['shipping'] = $Purchase->shipping;
            $item['statut'] = $Purchase->statut;
            $item['provider_name'] = $Purchase['provider']->name;
            $item['provider_email'] = $Purchase['provider']->email;
            $item['provider_tele'] = $Purchase['provider']->phone;
            $item['provider_code'] = $Purchase['provider']->code;
            $item['provider_adr'] = $Purchase['provider']->adresse;
            $item['GrandTotal'] = $Purchase['GrandTotal'];
            $item['paid_amount'] = $Purchase['paid_amount'];
            $item['due'] = $Purchase['GrandTotal'] - $Purchase['paid_amount'];
            $item['payment_status'] = $Purchase['payment_statut'];

            $data->add($item);
        }

        $suppliers = provider::where('deleted_at', '=', null)->get(['id', 'name']);

        //get warehouses assigned to user
        $user_auth = auth()->user();
        $warehouses = helpers::getWarehouses($user_auth);

        return response()->json([
            'totalRows' => $totalRows,
            'purchases' => $data,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    //------------- Show Report SALES -----------\\

    public function Report_Sales(request $request)
    {
        if (!helpers::checkPermission('Reports_sales')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $data = collect();
        $filter = collect($request->get('filter'));
        if (empty($filter->get('startDate'))) {
            $filter['startDate'] = Carbon::now()->startOfDay();
        }
        if (empty($filter->get('endDate'))) {
            $filter['endDate'] = Carbon::parse($filter['startDate'])->endOfDay();
        }

        $Sales = Sale::select('sales.*')
            ->with('facture', 'client', 'warehouse', 'details', 'details.product')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->where('sales.deleted_at', '=', null);
//                ->whereBetween('sales.date', array($request->from, $request->to));

        $Sales = helpers::getFilter($filter,
            $Sales,
            collect([
                collect(['type' => 'date', 'key' => 'date', 'filter' => 'startDate', 'filter2' => 'endDate']),
                collect(['type' => 'string', 'key' => 'Ref', 'filter' => 'Ref']),
                collect(['type' => 'string', 'key' => 'statut', 'filter' => 'status']),
                collect(['type' => 'string', 'key' => 'payment_statut', 'filter' => 'Payment']),
                collect(['type' => '', 'key' => 'sales.warehouse_id', 'filter' => 'warehouse']),
                collect(['type' => '', 'key' => 'sales.client_id', 'filter' => 'client']),
            ])
        );
        $Sales = $Sales->orderByDesc('date')->get();
        foreach ($Sales as $Sale) {
            foreach ($Sale['details'] as $datail) {
                $data->add([
                    'id' => $Sale['id'],
                    'date' => $Sale['date'],
                    'Ref' => $Sale['Ref'],
                    'statut' => $Sale['statut'],
                    'discount' => $Sale['discount'],
                    'shipping' => $Sale['shipping'],
                    'warehouse_name' => $Sale['warehouse']['name'],
                    'client_name' => $Sale['client']?->company_name,
                    'client_email' => $Sale['client']?->email,
                    'client_tele' => $Sale['client']?->phone,
                    'client_code' => $Sale['client']?->code,
                    'client_adr' => $Sale['client']?->adresse,
                    'product' => $datail['product']?->name,
                    'qty' => $datail?->quantity,
                    'price' => $datail?->price,
                    'GrandTotal' => $Sale['GrandTotal'],
                    'paid_amount' => $Sale['paid_amount'],
                    'due' => $Sale['GrandTotal'] - $Sale['paid_amount'],
                    'payment_status' => $Sale['payment_statut'],
                ]);
            }
        }

        $customers = Client::where('deleted_at', '=', null)->pluck('company_name', 'id');

        //get warehouses assigned to user
        $user_auth = auth()->user();
        $warehouses = helpers::getWarehouses($user_auth)->pluck('name', 'id');
        return response()->json([
            'sales' => $data,
            'customers' => $customers,
            'warehouses' => $warehouses
        ]);
    }

    //----------------- Providers Report -----------------------\\

    public function Providers_Report(request $request)
    {
        if (!helpers::checkPermission('Reports_suppliers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;
        $data = array();

        $providers = Provider::where('deleted_at', '=', null)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('code', 'LIKE', "%{$request->search}%")
                        ->orWhere('phone', 'LIKE', "%{$request->search}%");
                });
            });

        $totalRows = $providers->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $providers = $providers->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($providers as $provider) {
            $item['total_purchase'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->count();

            $item['total_amount'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('GrandTotal');

            $item['total_paid'] = DB::table('purchases')
                ->where('purchases.deleted_at', '=', null)
                ->where('purchases.provider_id', $provider->id)
                ->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = DB::table('purchase_returns')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('GrandTotal');

            $item['total_paid_return'] = DB::table('purchase_returns')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('paid_amount');

            $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

            $item['id'] = $provider->id;
            $item['name'] = $provider->name;
            $item['phone'] = $provider->phone;
            $item['code'] = $provider->code;

            $data[] = $item;
        }

        return response()->json([
            'report' => $data,
            'totalRows' => $totalRows,
        ]);

    }

    //-------------------- Get Purchases By Provider -------------\\

    public function Purchases_Provider(request $request)
    {
        if (!helpers::checkPermission('Reports_suppliers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $purchases = Purchase::where('deleted_at', '=', null)
            ->with('provider', 'warehouse')
            ->where('provider_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $purchases->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $purchases = $purchases->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($purchases as $purchase) {
            $item['id'] = $purchase->id;
            $item['Ref'] = $purchase->Ref;
            $item['warehouse_name'] = $purchase['warehouse']->name;
            $item['provider_name'] = $purchase['provider']->name;
            $item['statut'] = $purchase->statut;
            $item['GrandTotal'] = $purchase->GrandTotal;
            $item['paid_amount'] = $purchase->paid_amount;
            $item['due'] = $purchase->GrandTotal - $purchase->paid_amount;
            $item['payment_status'] = $purchase->payment_statut;

            $data[] = $item;
        }

        return response()->json([
            'totalRows' => $totalRows,
            'purchases' => $data,
        ]);

    }

    //-------------------- Get Payments By Provider -------------\\

    public function Payments_Provider(request $request)
    {

        if (!helpers::checkPermission('Reports_suppliers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $payments = DB::table('payment_purchases')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where('payment_purchases.deleted_at', '=', null)
            ->join('purchases', 'payment_purchases.purchase_id', '=', 'purchases.id')
            ->where('purchases.provider_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('payment_purchases.Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_purchases.date', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_purchases.Reglement', 'LIKE', "%{$request->search}%");
                });
            })
            ->select(
                'payment_purchases.date', 'payment_purchases.Ref AS Ref', 'purchases.Ref AS purchase_Ref',
                'payment_purchases.Reglement', 'payment_purchases.montant'
            );

        $totalRows = $payments->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $payments = $payments->offset($offSet)
            ->limit($perPage)
            ->orderBy('payment_purchases.id', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments,
            'totalRows' => $totalRows,
        ]);
    }

    //-------------------- Get Returns By Providers -------------\\

    public function Returns_Provider(request $request)
    {

        if (!helpers::checkPermission('Reports_suppliers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $PurchaseReturn = PurchaseReturn::where('deleted_at', '=', null)
            ->with('purchase', 'provider', 'warehouse')
            ->where('provider_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('purchase', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $PurchaseReturn->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $PurchaseReturn = $PurchaseReturn->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($PurchaseReturn as $Purchase_Return) {
            $item['id'] = $Purchase_Return->id;
            $item['Ref'] = $Purchase_Return->Ref;
            $item['statut'] = $Purchase_Return->statut;
            $item['purchase_ref'] = $Purchase_Return['purchase'] ? $Purchase_Return['purchase']->Ref : '---';
            $item['purchase_id'] = $Purchase_Return['purchase'] ? $Purchase_Return['purchase']->id : NULL;
            $item['provider_name'] = $Purchase_Return['provider']->name;
            $item['warehouse_name'] = $Purchase_Return['warehouse']->name;
            $item['GrandTotal'] = $Purchase_Return->GrandTotal;
            $item['paid_amount'] = $Purchase_Return->paid_amount;
            $item['due'] = $Purchase_Return->GrandTotal - $Purchase_Return->paid_amount;
            $item['payment_status'] = $Purchase_Return->payment_statut;

            $data[] = $item;
        }

        return response()->json([
            'totalRows' => $totalRows,
            'returns_supplier' => $data,
        ]);

    }

    //-------------------- Top 5 Suppliers -------------\\

    public function ToProviders(Request $request)
    {
        if (!helpers::checkPermission('Reports_suppliers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $results = DB::table('purchases')->where('purchases.deleted_at', '=', null)
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->select(DB::raw('providers.name'), DB::raw('count(*) as count'))
            ->groupBy('providers.name')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $data = [];
        $providers = [];
        foreach ($results as $result) {
            $providers[] = $result->name;
            $data[] = $result->count;
        }
        $data[] = 0;
        return response()->json(['providers' => $providers, 'data' => $data]);
    }

    //----------------- Warehouse Report By ID-----------------------\\

    public function Warehouse_Report(request $request)
    {
        if (!helpers::checkPermission('Warehouse_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $data = collect();
        $data->put('sales', Sale::where('deleted_at', '=', null)
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                });
            })->count());
        $data->put('purchases', Purchase::where('deleted_at', '=', null)
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                });
            })->count());

//        $data['ReturnPurchase'] = PurchaseReturn::where('deleted_at', '=', null)
//            ->where(function ($query) use ($request) {
//                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
//                    return $query->where('warehouse_id', $request->warehouse_id);
//                });
//            })->count();
//
//        $data['ReturnSale'] = SaleReturn::where('deleted_at', '=', null)
//            ->where(function ($query) use ($request) {
//                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
//                    return $query->where('warehouse_id', $request->warehouse_id);
//                });
//            })->count();

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user())->pluck('name', 'id');

        return response()->json([
            'data' => $data,
            'warehouses' => $warehouses,
        ], 200);

    }

    //-------------------- Get Sales By Warehouse -------------\\

    public function Sales_Warehouse(request $request)
    {
        if (!helpers::checkPermission('Warehouse_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
        $data = collect();

        $sales = Sale::where('deleted_at', '=', null)->with('client', 'warehouse')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                });
            })
            // Search With Multiple Param
//            ->where(function ($query) use ($request) {
//                return $query->when($request->filled('search'), function ($query) use ($request) {
//                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
//                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
//                        ->orWhere('GrandTotal', $request->search)
//                        ->orWhere('payment_statut', 'like', "$request->search")
//                        ->orWhere(function ($query) use ($request) {
//                            return $query->whereHas('client', function ($q) use ($request) {
//                                $q->where('name', 'LIKE', "%{$request->search}%");
//                            });
//                        });
//                });
//            })
        ;

        $totalRows = $sales->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $sales = $sales
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();
        $data = $sales->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'date' => $item->date,
                'Ref' => $item->Ref,
                'client_name' => $item->client?->company_name,
                'warehouse_name' => $item->warehouse?->name,
                'statut' => $item->statut,
                'GrandTotal' => $item->GrandTotal,
                'paid_amount' => $item->paid_amount,
                'due' => $item->GrandTotal - $item->paid_amount,
                'payment_status' => $item->payment_statut,
                'shipping_status' => $item->shipping_status,
            ];
        });

        return response()->json([
            'totalRows' => $totalRows,
            'sales' => $data,
        ]);

    }

    //-------------------- Get Quotations By Warehouse -------------\\

    public function Quotations_Warehouse(request $request)
    {
        if (!helpers::checkPermission('Warehouse_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = [];

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $Quotations = Quotation::where('deleted_at', '=', null)
            ->with('client', 'warehouse')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                });
            })
            //Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('GrandTotal', $request->search)
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });
        $totalRows = $Quotations->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $Quotations = $Quotations->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($Quotations as $Quotation) {
            $item['id'] = $Quotation->id;
            $item['date'] = $Quotation->date;
            $item['Ref'] = $Quotation->Ref;
            $item['warehouse_name'] = $Quotation['warehouse']->name;
            $item['client_name'] = $Quotation['client']->name;
            $item['statut'] = $Quotation->statut;
            $item['GrandTotal'] = $Quotation->GrandTotal;

            $data[] = $item;
        }

        return response()->json([
            'quotations' => $data,
            'totalRows' => $totalRows,
        ]);
    }

    //-------------------- Get Returns Sale By Warehouse -------------\\

    public function Returns_Sale_Warehouse(request $request)
    {
        if (!helpers::checkPermission('Warehouse_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        //  Check If User Has Permission Show All Records
        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $SaleReturn = SaleReturn::where('deleted_at', '=', null)
            ->with('sale', 'client', 'warehouse')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                });
            })
            //Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('GrandTotal', $request->search)
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $SaleReturn->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $SaleReturn = $SaleReturn->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($SaleReturn as $Sale_Return) {
            $item['id'] = $Sale_Return->id;
            $item['warehouse_name'] = $Sale_Return['warehouse']->name;
            $item['Ref'] = $Sale_Return->Ref;
            $item['statut'] = $Sale_Return->statut;
            $item['client_name'] = $Sale_Return['client']->name;
            $item['sale_ref'] = $Sale_Return['sale'] ? $Sale_Return['sale']->Ref : '---';
            $item['sale_id'] = $Sale_Return['sale'] ? $Sale_Return['sale']->id : NULL;
            $item['GrandTotal'] = $Sale_Return->GrandTotal;
            $item['paid_amount'] = $Sale_Return->paid_amount;
            $item['due'] = $Sale_Return->GrandTotal - $Sale_Return->paid_amount;
            $item['payment_status'] = $Sale_Return->payment_statut;

            $data[] = $item;
        }

        return response()->json([
            'totalRows' => $totalRows,
            'returns_sale' => $data,
        ]);
    }

    //-------------------- Get Returns Purchase By Warehouse -------------\\

    public function Returns_Purchase_Warehouse(request $request)
    {
        if (!helpers::checkPermission('Warehouse_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        //  Check If User Has Permission Show All Records
        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $PurchaseReturn = PurchaseReturn::where('deleted_at', '=', null)
            ->with('purchase', 'provider', 'warehouse')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->orWhere(function ($query) use ($request) {
                return $query->whereHas('purchase', function ($q) use ($request) {
                    $q->where('Ref', 'LIKE', "%{$request->search}%");
                });
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                });
            })
            //Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('GrandTotal', $request->search)
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $PurchaseReturn->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $PurchaseReturn = $PurchaseReturn->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($PurchaseReturn as $Purchase_Return) {
            $item['id'] = $Purchase_Return->id;
            $item['Ref'] = $Purchase_Return->Ref;
            $item['statut'] = $Purchase_Return->statut;
            $item['purchase_ref'] = $Purchase_Return['purchase'] ? $Purchase_Return['purchase']->Ref : '---';
            $item['purchase_id'] = $Purchase_Return['purchase'] ? $Purchase_Return['purchase']->id : NULL;
            $item['warehouse_name'] = $Purchase_Return['warehouse']->name;
            $item['provider_name'] = $Purchase_Return['provider']->name;
            $item['GrandTotal'] = $Purchase_Return->GrandTotal;
            $item['paid_amount'] = $Purchase_Return->paid_amount;
            $item['due'] = $Purchase_Return->GrandTotal - $Purchase_Return->paid_amount;
            $item['payment_status'] = $Purchase_Return->payment_statut;

            $data[] = $item;
        }

        return response()->json([
            'totalRows' => $totalRows,
            'returns_purchase' => $data,
        ]);
    }

    //-------------------- Get Expenses By Warehouse -------------\\

    public function Expenses_Warehouse(request $request)
    {
        if (!helpers::checkPermission('Warehouse_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
        $data = collect();

        $Expenses = Expense::where('deleted_at', '=', null)
            ->with('expense_category', 'warehouse')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                });
            })
            //Search With Multiple Param
//            ->where(function ($query) use ($request) {
//                return $query->when($request->filled('search'), function ($query) use ($request) {
//                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
//                        ->orWhere('date', 'LIKE', "%{$request->search}%")
//                        ->orWhere('details', 'LIKE', "%{$request->search}%")
//                        ->orWhere(function ($query) use ($request) {
//                            return $query->whereHas('expense_category', function ($q) use ($request) {
//                                $q->where('name', 'LIKE', "%{$request->search}%");
//                            });
//                        });
//                });
//            })
        ;

        $totalRows = $Expenses->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $Expenses = $Expenses
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = $Expenses->map(function ($item, $key) {
            return [
                'date' => $item->date,
                'Ref' => $item->Ref,
                'details' => $item->details,
                'amount' => $item->amount,
                'warehouse_name' => $item['warehouse']->name,
                'category_name' => $item['expense_category']->name
            ];
        });

        return response()->json([
            'totalRows' => $totalRows,
            'expenses' => $data,
        ]);
    }

    //----------------- Warhouse Count Stock -----------------------\\

    public function Warhouse_Count_Stock(Request $request)
    {
        if (!helpers::checkPermission('Warehouse_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $stock_count = product_warehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->join('warehouses', 'product_warehouse.warehouse_id', '=', 'warehouses.id')
            ->where('product_warehouse.deleted_at', '=', null)
            ->select(
                DB::raw("count(DISTINCT products.id) as value"),
                DB::raw("warehouses.name as name"),
                DB::raw('(IFNULL(SUM(qty),0)) AS value1'),
            )
            ->where('qty', '>', 0)
            ->groupBy('warehouses.name')
            ->get();

        $stock_value = product_warehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->join('warehouses', 'product_warehouse.warehouse_id', '=', 'warehouses.id')
            ->where('product_warehouse.deleted_at', '=', null)
            ->select(
                DB::raw("SUM(products.price * qty ) as price"),
                DB::raw("SUM(products.cost * qty) as cost"),
                DB::raw("warehouses.name as name"),
            )
            ->where('qty', '>', 0)
            ->groupBy('warehouses.name')
            ->get();

        $data = $stock_value->map(function ($value, $key) {
            return [
                'name' => $value->name,
                'value' => $value->price,
                'value1' => $value->cost,
            ];
        });

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user())->pluck('name');

        return response()->json([
            'stock_count' => $stock_count,
            'stock_value' => $data,
            'warehouses' => $warehouses,
        ]);

    }

    //-------------- Count  Product Quantity Alerts ---------------\\

    public function count_quantity_alert(request $request)
    {

        $products_alerts = product_warehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->whereRaw('qty <= stock_alert')
            ->count();

        return response()->json($products_alerts);
    }

    //-----------------Profit And Loss ---------------------------\\

    public function ProfitAndLoss(request $request)
    {
        if (!helpers::checkPermission('Reports_profit')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $data = collect();

        $item['sales'] = Sale::where('deleted_at', '=', null)->whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['purchases'] = Purchase::where('deleted_at', '=', null)->whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['returns_sales'] = SaleReturn::where('deleted_at', '=', null)->whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['returns_purchases'] = PurchaseReturn::where('deleted_at', '=', null)->whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(GrandTotal) AS sum'),
                DB::raw("count(*) as nmbr")
            )->first();

        $item['paiement_sales'] = PaymentSale::whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['PaymentSaleReturns'] = PaymentSaleReturns::whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['PaymentPurchaseReturns'] = PaymentPurchaseReturns::whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['paiement_purchases'] = PaymentPurchase::whereBetween('date', array($request->from, $request->to))
            ->select(
                DB::raw('SUM(montant) AS sum')
            )->first();

        $item['expenses'] = Expense::whereBetween('date', array($request->from, $request->to))
            ->where('deleted_at', '=', null)
            ->select(
                DB::raw('SUM(amount) AS sum')
            )->first();


        $item['return_sales'] = SaleReturn::where('deleted_at', '=', null)
            ->whereBetween('date', array($request->from, $request->to))
//            ->where(function ($query) use ($view_records) {
//                if (!$view_records) {
//                    return $query->where('user_id', '=', Auth::user()->id);
//                }
//            })
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;


        $item['purchases_return'] = PurchaseReturn::where('deleted_at', '=', null)
            ->whereBetween('date', array($request->from, $request->to))
//            ->where(function ($query) use ($view_records) {
//                if (!$view_records) {
//                    return $query->where('user_id', '=', Auth::user()->id);
//                }
//            })
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;


        //calcule COGS and average cost
        $cogs_average_data = $this->CalculeCogsAndAverageCost($request);

        $cogs = $cogs_average_data['total_cogs_products'];
        $total_average_cost = $cogs_average_data['total_average_cost'];


        $item['product_cost_fifo'] = $cogs;
        $item['total_average_cost'] = $total_average_cost;

        $item['profit_fifo'] = $item['sales']['sum'] - $cogs;
        $item['profit_average_cost'] = $item['sales']['sum'] - $total_average_cost;


        $item['payment_received'] = $item['paiement_sales']['sum'] + $item['PaymentPurchaseReturns']['sum'];
        $item['payment_sent'] = $item['paiement_purchases']['sum'] + $item['PaymentSaleReturns']['sum'] + $item['expenses']['sum'];
        $item['paiement_net'] = $item['payment_received'] - $item['payment_sent'];
        $item['total_revenue'] = $item['sales']['sum'] - $item['return_sales'];

        return response()->json(['data' => $item]);

    }

    // Calculating the cost of goods sold (COGS)
    public function CalculeCogsAndAverageCost(request $request)
    {

        // Initialize variable to store total COGS averageCost and for all products
        $total_cogs_products = 0;
        $total_average_cost = 0;

        // Get all distinct product IDs for sales between start and end date
        $productIds = SaleDetail::whereBetween('date', array($request->from, $request->to))->select('product_id')->distinct()->get();

        // Loop through each product
        foreach ($productIds as $productId) {

            $totalCogs = 0;
            $average_cost = 0;

            // Get the total cost and quantity for all adjustments of the product
            $adjustments = AdjustmentDetail::join('adjustments', 'adjustments.id', '=', 'adjustment_details.adjustment_id')
                ->where('adjustments.date', '<=', $request->to)
                ->where('product_id', $productId['product_id'])->get();

            $adjustment_quantity = 0;
            foreach ($adjustments as $adjustment) {
                $adjustment_quantity = $adjustment->type == 'add'
                    ? $adjustment_quantity + $adjustment->quantity
                    : $adjustment_quantity - $adjustment->quantity;
            }

            // Get total quantity sold before start date
            $totalQuantitySold = SaleDetail::where('product_id', $productId['product_id'])->where('date', '<', $request->from)->sum('quantity');

            // Get purchase details for current product, ordered by date in ascending date
            $purchases = PurchaseDetail::where('product_id', $productId['product_id'])
                ->join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                ->orderBy('purchases.date', 'asc')
                ->select('purchase_details.quantity as quantity', 'purchase_details.cost as cost')
                ->get();

            if (count($purchases) > 0) {
                $purchases_to_array = $purchases->toArray();
                $purchases_sum_qty = array_sum(array_column($purchases_to_array, 'quantity'));
            } else {
                $purchases_sum_qty = 0;
            }

            // Get sale details for current product between start and end date, ordered by date in ascending order
            $sales = SaleDetail::where('product_id', $productId['product_id'])
                ->whereBetween('date', array($request->from, $request->to))
                ->orderBy('date', 'asc')
                ->get();

            $sales_to_array = $sales->toArray();
            $sales_sum_qty = array_sum(array_column($sales_to_array, 'quantity'));

            $total_sum_sales = $totalQuantitySold + $sales_sum_qty;


            //calcule average Cost
            $average_cost = $this->averageCost($productId['product_id'], $request);

            if ($total_sum_sales > $purchases_sum_qty) {
                // Handle adjustments only case
                // $product = Product::find($productId['product_id']);
                // $product_cost = $product->cost;
                $totalCogs += $sales_sum_qty * $average_cost;
                $total_average_cost += $sales_sum_qty * $average_cost;

            } else {

                foreach ($sales as $sale) {

                    $saleQuantity = $sale->quantity;
                    $total_average_cost += $average_cost * $sale->quantity;

                    while ($saleQuantity > 0) {
                        $purchase = $purchases->first();

                        if ($purchase->quantity > 0) {
                            $totalQuantitySold += $saleQuantity;

                            if ($purchase->quantity >= $totalQuantitySold) {
                                $totalCogs += $saleQuantity * $purchase->cost;
                                $purchase->quantity -= $totalQuantitySold;
                                $saleQuantity = 0;
                                $totalQuantitySold = 0;

                                if ($purchase->quantity == 0) {
                                    $purchase->quantity = 0;
                                    $saleQuantity = 0;
                                    $totalQuantitySold = 0;
                                    $purchases->shift();
                                }

                            } else {

                                if ($purchase->quantity >= ($totalQuantitySold - $saleQuantity)) {
                                    $rest = $purchase->quantity - ($totalQuantitySold - $saleQuantity);
                                    if ($rest <= $saleQuantity) {
                                        $saleQuantity -= $rest;
                                        $totalCogs += $rest * $purchase->cost;
                                        $totalQuantitySold = 0;
                                        $purchase->quantity = 0;
                                        $purchases->shift();

                                    } else {
                                        $totalQuantitySold -= $saleQuantity;
                                        $purchase->quantity = $purchase->quantity - $totalQuantitySold;
                                        $totalCogs += $purchase->quantity * $purchase->cost;
                                        $saleQuantity -= $purchase->quantity;
                                        $purchase->quantity = 0;
                                        $purchases->shift();
                                    }

                                } else {
                                    $totalQuantitySold -= $saleQuantity;
                                    $totalQuantitySold -= $purchase->quantity;
                                    $purchase->quantity = 0;
                                    $purchases->shift();
                                }
                            }
                        } else {
                            $purchases->shift();
                        }


                    }

                }
            }
            $total_cogs_products += $totalCogs;

        }

        return [
            'total_cogs_products' => $total_cogs_products,
            'total_average_cost' => $total_average_cost
        ];


    }


    // Calculate the average cost of a product.
    public function averageCost($product_id, request $request)
    {
        // Get the cost of the product from the products table
        $product = Product::find($product_id);
        $product_cost = $product->cost;

        // Get the total cost and quantity for all purchases of the product
        $purchases = PurchaseDetail::with('purchase')
            ->where(function ($query) use ($request) {
                return $query->whereHas('purchase', function ($q) use ($request) {
                    $q->where('date', '<=', $request->to);
                });

            })->where('product_id', $product_id)->get();

        $purchase_cost = 0;
        $purchase_quantity = 0;
        foreach ($purchases as $purchase) {
            $purchase_cost += $purchase->quantity * $purchase->cost;
            $purchase_quantity += $purchase->quantity;
        }

        // Get the total cost and quantity for all adjustments of the product
        $adjustments = AdjustmentDetail::with('adjustment')
            ->where(function ($query) use ($request) {
                return $query->whereHas('adjustment', function ($q) use ($request) {
                    $q->where('date', '<=', $request->to);
                });

            })
            ->where('product_id', $product_id)->get();

        $adjustment_cost = 0;
        $adjustment_quantity = 0;
        foreach ($adjustments as $adjustment) {
            if ($adjustment->type == 'add') {
                $adjustment_cost += $adjustment->quantity * $product_cost;
                $adjustment_quantity += $adjustment->quantity;
            } else {
                $adjustment_cost -= $adjustment->quantity * $product_cost;
                $adjustment_quantity -= $adjustment->quantity;
            }
        }

        // Calculate the average cost
        $total_cost = $purchase_cost + $adjustment_cost;
        $total_quantity = $purchase_quantity + $adjustment_quantity;
        $average_cost = $total_cost / $total_quantity;

        return $average_cost;
    }


    //-------------------- report_top_products -------------\\

    public function report_top_products(request $request)
    {

//        $this->authorizeForUser($request->user('api'), 'Top_products', Product::class);

        $Role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($Role->id)->inRole('record_view');
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
        if (!$request->has(['from', 'to'])) {
            $request['from'] = Carbon::now();
            $request['to'] = Carbon::now();
        }

        $products_data = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
//            ->where(function ($query) use ($view_records) {
//                if (!$view_records) {
//                    return $query->where('sales.user_id', '=', Auth::user()->id);
//                }
//            })
            ->whereBetween('sale_details.date', array($request->from, $request->to))
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('products.name', 'LIKE', "%{$request->search}%")
                        ->orWhere('products.code', 'LIKE', "%{$request->search}%");
                });
            })
            ->select(
                DB::raw('products.name as name'),
                DB::raw('products.code as code'),
                DB::raw('count(*) as total_sales'),
                DB::raw('sum(total) as total'),
            )
            ->groupBy(['products.name', 'products.code']);

        $totalRows = $products_data->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }


        $products = $products_data
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('total_sales', 'desc')
            ->get();
        Inertia::share('titlePage', 'Top Productos Vendidos');
        return Inertia::render('Reports/top_selling_products', ['products' => $products,]);
    }


    //-------------------- report_top_customers -------------\\

    public function report_top_customers(request $request)
    {
        if (!helpers::checkPermission('Top_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $customers_count = Sale::where('sales.deleted_at', '=', null)
//            ->where(function ($query) use ($view_records) {
//                if (!$view_records) {
//                    return $query->where('sales.user_id', '=', Auth::user()->id);
//                }
//            })
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->select(DB::raw('clients.name'), DB::raw("count(*) as total_sales"))
            ->groupBy('clients.name')->get();

        $totalRows = $customers_count->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }

        $customers_data = Sale::where('sales.deleted_at', '=', null)
//            ->where(function ($query) use ($view_records) {
//                if (!$view_records) {
//                    return $query->where('sales.user_id', '=', Auth::user()->id);
//                }
//            })
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->select(
                DB::raw('clients.name as name'),
                DB::raw('clients.phone as phone'),
                DB::raw('clients.email as email'),
                DB::raw("count(*) as total_sales"),
                DB::raw('sum(GrandTotal) as total'),
            )
            ->groupBy(['clients.name', 'clients.phone', 'clients.email']);

        $customers = $customers_data
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('total_sales', 'desc')
            ->get();

        Inertia::share('titlePage', 'Top Clientes');
        return Inertia::render('Reports/top_customers', ['customers' => $customers,]);

    }


    //----------------- Users Report -----------------------\\

    public function users_Report(request $request)
    {
        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
//        $order = $request->SortField;
//        $dir = $request->SortType;

        $users = User::where(function ($query) use ($request) {
            return $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('username', 'LIKE', "%{$request->search}%");
            });
        });

        $totalRows = $users->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $users = $users
//            ->offset($offSet)
//            ->limit($perPage)
//            ->orderBy($order, $dir)
            ->get();

        $data = $users->map(function ($user) {
            $item['total_sales'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('user_id', $user->id)
                ->count();

            $item['total_purchases'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('user_id', $user->id)
                ->count();

//            $item['total_quotations'] = DB::table('quotations')
//                ->where('deleted_at', '=', null)
//                ->where('user_id', $user->id)
//                ->count();
//
//            $item['total_return_sales'] = DB::table('sale_returns')
//                ->where('deleted_at', '=', null)
//                ->where('user_id', $user->id)
//                ->count();
//
//            $item['total_return_purchases'] = DB::table('purchase_returns')
//                ->where('deleted_at', '=', null)
//                ->where('user_id', $user->id)
//                ->count();

            $item['total_transfers'] = DB::table('transfers')
                ->where('deleted_at', '=', null)
                ->where('user_id', $user->id)
                ->count();

            $item['total_adjustments'] = DB::table('adjustments')
                ->where('deleted_at', '=', null)
                ->where('user_id', $user->id)
                ->count();

            $item['id'] = $user->id;
            $item['username'] = $user->username;
            return $item;
        });

        return response()->json([
            'report' => $data,
            'totalRows' => $totalRows,
        ]);

    }


    //-------------------- Get Sales By user -------------\\

    public function get_sales_by_user(request $request)
    {

        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $sales = Sale::where('deleted_at', '=', null)->with('user', 'client', 'warehouse')
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            ->where('user_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'like', "%{$request->search}%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $sales->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $sales = $sales
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = $sales->map(function ($sale) {
            return [
                'username' => $sale['user']?->username,
                'client_name' => $sale['client']?->company_name,
                'warehouse_name' => $sale['warehouse']?->name,
                'date' => $sale->date,
                'Ref' => $sale->Ref,
                'sale_id' => $sale->id,
                'statut' => $sale->statut,
                'GrandTotal' => $sale->GrandTotal,
                'paid_amount' => $sale->paid_amount,
                'due' => $sale->GrandTotal - $sale->paid_amount,
                'payment_status' => $sale->payment_statut,
                'shipping_status' => $sale->shipping_status
            ];
        });
        return response()->json([
            'totalRows' => $totalRows,
            'sales' => $data,
        ]);

    }

    //-------------------- Get Quotations By user -------------\\

    public function get_quotations_by_user(request $request)
    {

        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');
        $data = array();

        $Quotations = Quotation::with('client', 'warehouse', 'user')
            ->where('deleted_at', '=', null)
            ->where('user_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            //Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $Quotations->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $Quotations = $Quotations->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($Quotations as $Quotation) {

            $item['id'] = $Quotation->id;
            $item['date'] = $Quotation->date;
            $item['Ref'] = $Quotation->Ref;
            $item['statut'] = $Quotation->statut;
            $item['username'] = $Quotation['user']->username;
            $item['warehouse_name'] = $Quotation['warehouse']->name;
            $item['client_name'] = $Quotation['client']->name;
            $item['GrandTotal'] = $Quotation->GrandTotal;

            $data[] = $item;
        }

        return response()->json([
            'quotations' => $data,
            'totalRows' => $totalRows,
        ]);
    }

    //-------------------- Get Purchases By user -------------\\

    public function get_purchases_by_user(request $request)
    {

        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $purchases = Purchase::where('deleted_at', '=', null)
            ->with('user', 'provider', 'warehouse')
            ->where('user_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $purchases->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $purchases = $purchases->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($purchases as $purchase) {
            $item['Ref'] = $purchase->Ref;
            $item['purchase_id'] = $purchase->id;
            $item['username'] = $purchase['user']->username;
            $item['provider_name'] = $purchase['provider']->name;
            $item['warehouse_name'] = $purchase['warehouse']->name;
            $item['statut'] = $purchase->statut;
            $item['GrandTotal'] = $purchase->GrandTotal;
            $item['paid_amount'] = $purchase->paid_amount;
            $item['due'] = $purchase->GrandTotal - $purchase->paid_amount;
            $item['payment_status'] = $purchase->payment_statut;

            $data[] = $item;
        }

        return response()->json([
            'totalRows' => $totalRows,
            'purchases' => $data,
        ]);

    }

    //-------------------- Get sale Returns By user -------------\\

    public function get_sales_return_by_user(request $request)
    {

        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        //  Check If User Has Permission Show All Records
        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $SaleReturn = SaleReturn::where('deleted_at', '=', null)->with('user', 'client', 'warehouse')
            ->where('user_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $SaleReturn->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $SaleReturn = $SaleReturn->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($SaleReturn as $Sale_Return) {
            $item['Ref'] = $Sale_Return->Ref;
            $item['return_sale_id'] = $Sale_Return->id;
            $item['statut'] = $Sale_Return->statut;
            $item['username'] = $Sale_Return['user']->username;
            $item['client_name'] = $Sale_Return['client']->name;
            $item['warehouse_name'] = $Sale_Return['warehouse']->name;
            $item['GrandTotal'] = $Sale_Return->GrandTotal;
            $item['paid_amount'] = $Sale_Return->paid_amount;
            $item['due'] = $Sale_Return->GrandTotal - $Sale_Return->paid_amount;
            $item['payment_status'] = $Sale_Return->payment_statut;

            $data[] = $item;
        }

        return response()->json([
            'totalRows' => $totalRows,
            'sales_return' => $data,
        ]);
    }

    //-------------------- Get purchase Returns By user -------------\\

    public function get_purchase_return_by_user(request $request)
    {

        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $data = array();

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $PurchaseReturn = PurchaseReturn::where('deleted_at', '=', null)
            ->with('user', 'provider', 'warehouse')
            ->where('user_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $PurchaseReturn->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $PurchaseReturn = $PurchaseReturn->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($PurchaseReturn as $Purchase_Return) {
            $item['Ref'] = $Purchase_Return->Ref;
            $item['return_purchase_id'] = $Purchase_Return->id;
            $item['statut'] = $Purchase_Return->statut;
            $item['username'] = $Purchase_Return['user']->username;
            $item['provider_name'] = $Purchase_Return['provider']->name;
            $item['warehouse_name'] = $Purchase_Return['warehouse']->name;
            $item['GrandTotal'] = $Purchase_Return->GrandTotal;
            $item['paid_amount'] = $Purchase_Return->paid_amount;
            $item['due'] = $Purchase_Return->GrandTotal - $Purchase_Return->paid_amount;
            $item['payment_status'] = $Purchase_Return->payment_statut;

            $data[] = $item;
        }

        return response()->json([
            'totalRows' => $totalRows,
            'purchases_return' => $data,
        ]);

    }

    //-------------------- Get transfers By user -------------\\

    public function get_transfer_by_user(request $request)
    {

        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
//         Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $transfers = Transfer::with('from_warehouse', 'to_warehouse')
            ->with('user')
            ->where('user_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('from_warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('to_warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $transfers->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $transfers = $transfers
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();
        $data = $transfers->map(function ($transfer) {
            return [
                'id' => $transfer->id,
                'date' => $transfer->date,
                'Ref' => $transfer->Ref,
                'username' => $transfer['user']->username,
                'from_warehouse' => $transfer['from_warehouse']->name,
                'to_warehouse' => $transfer['to_warehouse']->name,
                'GrandTotal' => $transfer->GrandTotal,
                'items' => $transfer->items,
                'statut' => $transfer->statut,
            ];
        });
        return response()->json([
            'totalRows' => $totalRows,
            'transfers' => $data,
        ]);

    }

    //-------------------- Get adjustment By user -------------\\

    public function get_adjustment_by_user(request $request)
    {

        if (!helpers::checkPermission('users_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $Adjustments = Adjustment::with('warehouse')
            ->with('user')
            ->where('user_id', $request->id)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $Adjustments->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $Adjustments = $Adjustments
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = $Adjustments->map(function ($Adjustment) {
            return [
                'id' => $Adjustment->id,
                'username' => $Adjustment['user']->username,
                'date' => $Adjustment->date,
                'Ref' => $Adjustment->Ref,
                'warehouse_name' => $Adjustment['warehouse']->name,
                'items' => $Adjustment->items,
            ];
        });

        return response()->json([
            'totalRows' => $totalRows,
            'adjustments' => $data,
        ]);

    }


    //----------------- stock Report -----------------------\\

    public function stock_Report(request $request)
    {
        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
//        $order = $request->SortField;
//        $dir = $request->SortType;

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());

        $products_data = Product::with('unit', 'category', 'brand')
            ->where('deleted_at', '=', null)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('products.name', 'LIKE', "%{$request->search}%")
                        ->orWhere('products.code', 'LIKE', "%{$request->search}%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('category', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $products_data->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $products = $products_data
//            ->offset($offSet)
//            ->limit($perPage)
//            ->orderBy($order, $dir)
            ->get();
        $data = $products->map(function ($product) use ($request, $warehouses) {
            $item['id'] = $product->id;
            $item['code'] = $product->code;
            $item['name'] = $product->name;
            $item['category'] = $product['category']->name;
            $item['price'] = $product->price;

            $product_warehouse_data = product_warehouse::where('product_id', $product->id)
                ->where('deleted_at', '=', null)
                ->whereIn('warehouse_id', $warehouses->pluck('id'))
                ->where(function ($query) use ($request) {
                    return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                        return $query->where('warehouse_id', $request->warehouse_id);
                    });
                })
                ->get();
            $total_qty = 0;
            foreach ($product_warehouse_data as $product_warehouse) {
                $total_qty += $product_warehouse->qty;
                $item['quantity'] = $total_qty . ' ' . $product['unit']->ShortName;
            }
            return $item;
        });
        return response()->json([
            'report' => $data,
            'totalRows' => $totalRows,
            'warehouses' => $warehouses->pluck('name', 'id'),
        ]);
    }

    //-------------------- Get Sales By product -------------\\

    public function get_sales_by_product(request $request)
    {

        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $sale_details_data = SaleDetail::with('product', 'sale', 'sale.client', 'sale.warehouse', 'sale.sales_type')
            ->where(function ($query) use ($request) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->whereHas('sale', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where('product_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('sale.client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $sale_details_data->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $sale_details = $sale_details_data
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = $sale_details->map(function ($detail) {
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

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail->date;
            $item['sale_type'] = $detail['sale']->sales_type?->name;
            $item['Ref'] = $detail['sale']->Ref;
            $item['sale_id'] = $detail['sale']->id;
            $item['client_name'] = $detail['sale']->client?->company_name;
            $item['warehouse_name'] = $detail['sale']->warehouse?->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_sale'] = $unit->ShortName;
            return $item;
        });

        return response()->json([
            'totalRows' => $totalRows,
            'sales' => $data,
        ]);

    }

    //-------------------- Get quotations By product -------------\\

    public function get_quotations_by_product(request $request)
    {

        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $quotation_details_data = QuotationDetail::with('product', 'quotation', 'quotation.client', 'quotation.warehouse')
            ->where(function ($query) use ($ShowRecord, $request) {
                if (!$ShowRecord) {
                    return $query->whereHas('quotation', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where('product_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('quotation.client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('quotation.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('quotation', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $quotation_details_data->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $quotation_details = $quotation_details_data->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($quotation_details as $detail) {

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

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['quotation']->date;
            $item['Ref'] = $detail['quotation']->Ref;
            $item['quotation_id'] = $detail['quotation']->id;
            $item['client_name'] = $detail['quotation']['client']->name;
            $item['warehouse_name'] = $detail['quotation']['warehouse']->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_sale'] = $unit->ShortName;

            $data[] = $item;
        }
        return response()->json([
            'totalRows' => $totalRows,
            'quotations' => $data,
        ]);

    }

    //-------------------- Get purchases By product -------------\\

    public function get_purchases_by_product(request $request)
    {

        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $purchase_details_data = PurchaseDetail::with('product', 'purchase', 'purchase.provider', 'purchase.warehouse')
            ->where(function ($query) use ($ShowRecord, $request) {
                if (!$ShowRecord) {
                    return $query->whereHas('purchase', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where('product_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('purchase.provider', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('purchase.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('purchase', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $purchase_details_data->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $purchase_details = $purchase_details_data->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($purchase_details as $detail) {

            //-------check if detail has purchase_unit_id Or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['purchase']->date;
            $item['Ref'] = $detail['purchase']->Ref;
            $item['purchase_id'] = $detail['purchase']->id;
            $item['provider_name'] = $detail['purchase']['provider']->name;
            $item['warehouse_name'] = $detail['purchase']['warehouse']->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_purchase'] = $unit->ShortName;

            $data[] = $item;
        }
        return response()->json([
            'totalRows' => $totalRows,
            'purchases' => $data,
        ]);

    }

    //-------------------- Get purchases return By product -------------\\

    public function get_purchase_return_by_product(request $request)
    {

        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $purchase_return_details_data = PurchaseReturnDetails::with('product', 'PurchaseReturn', 'PurchaseReturn.provider', 'PurchaseReturn.warehouse')
            ->where(function ($query) use ($ShowRecord, $request) {
                if (!$ShowRecord) {
                    return $query->whereHas('PurchaseReturn', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where('product_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('PurchaseReturn.provider', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('PurchaseReturn.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('PurchaseReturn', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $purchase_return_details_data->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $purchase_return_details = $purchase_return_details_data->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($purchase_return_details as $detail) {

            //-------check if detail has purchase_unit_id Or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['PurchaseReturn']->date;
            $item['Ref'] = $detail['PurchaseReturn']->Ref;
            $item['return_purchase_id'] = $detail['PurchaseReturn']->id;
            $item['provider_name'] = $detail['PurchaseReturn']['provider']->name;
            $item['warehouse_name'] = $detail['PurchaseReturn']['warehouse']->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_purchase'] = $unit->ShortName;

            $data[] = $item;
        }
        return response()->json([
            'totalRows' => $totalRows,
            'purchases_return' => $data,
        ]);

    }

    //-------------------- Get sales return By product -------------\\

    public function get_sales_return_by_product(request $request)
    {

        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $Sale_Return_details_data = SaleReturnDetails::with('product', 'SaleReturn', 'SaleReturn.client', 'SaleReturn.warehouse')
            ->where(function ($query) use ($ShowRecord, $request) {
                if (!$ShowRecord) {
                    return $query->whereHas('SaleReturn', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where('product_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('SaleReturn.client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('SaleReturn.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('SaleReturn', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $Sale_Return_details_data->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }
        $Sale_Return_details = $Sale_Return_details_data->offset($offSet)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        foreach ($Sale_Return_details as $detail) {

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

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['SaleReturn']->date;
            $item['Ref'] = $detail['SaleReturn']->Ref;
            $item['return_sale_id'] = $detail['SaleReturn']->id;
            $item['client_name'] = $detail['SaleReturn']['client']->name;
            $item['warehouse_name'] = $detail['SaleReturn']['warehouse']->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_sale'] = $unit->ShortName;

            $data[] = $item;
        }
        return response()->json([
            'totalRows' => $totalRows,
            'sales_return' => $data,
        ]);

    }

    //-------------------- Get transfers By product -------------\\

    public function get_transfer_by_product(request $request)
    {

        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $transfer_details_data = TransferDetail::with('product', 'transfer', 'transfer.from_warehouse', 'transfer.to_warehouse')
            ->where(function ($query) use ($request) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->whereHas('transfer', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where('product_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('transfer.from_warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('transfer.to_warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('transfer', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $transfer_details_data->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $transfer_details = $transfer_details_data
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = $transfer_details->map(function ($detail) {
            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();
                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['transfer']->date;
            $item['Ref'] = $detail['transfer']->Ref;
            $item['from_warehouse'] = $detail['transfer']['from_warehouse']->name;
            $item['to_warehouse'] = $detail['transfer']['to_warehouse']->name;
            $item['product_name'] = $product_name;
            return $item;
        });

        return response()->json([
            'totalRows' => $totalRows,
            'transfers' => $data,
        ]);

    }

    //-------------------- Get adjustments By product -------------\\

    public function get_adjustment_by_product(request $request)
    {

        if (!helpers::checkPermission('stock_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $adjustment_details_data = AdjustmentDetail::with('product', 'adjustment', 'adjustment.warehouse')
            ->where(function ($query) use ($request) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->whereHas('adjustment', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where('product_id', $request->id)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('adjustment.warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('adjustment', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $adjustment_details_data->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $adjustment_details = $adjustment_details_data
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = $adjustment_details->map(function ($detail) {
            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['adjustment']->date;
            $item['Ref'] = $detail['adjustment']->Ref;
            $item['warehouse_name'] = $detail['adjustment']['warehouse']->name;
            $item['product_name'] = $product_name;
            return $item;
        });

        return response()->json([
            'totalRows' => $totalRows,
            'adjustments' => $data,
        ]);
    }

    //------------- download_report_client_pdf -----------\\

    public function download_report_client_pdf(Request $request, $id)
    {
        if (!helpers::checkPermission('Reports_customers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $helpers = new helpers();
        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        $Sales = Sale::where('deleted_at', '=', null)
            ->where([
                ['payment_statut', '!=', 'paid'],
                ['client_id', $id]
            ])->get();

        $sales_details = [];

        foreach ($Sales as $Sale) {

            $item_sale['date'] = $Sale['date'];
            $item_sale['Ref'] = $Sale['Ref'];
            $item_sale['GrandTotal'] = number_format($Sale['GrandTotal'], 2, '.', '');
            $item_sale['paid_amount'] = number_format($Sale['paid_amount'], 2, '.', '');
            $item_sale['due'] = number_format($item_sale['GrandTotal'] - $item_sale['paid_amount'], 2, '.', '');
            $item_sale['payment_status'] = $Sale['payment_statut'];

            $sales_details[] = $item_sale;
        }

        $data['client_name'] = $client->name;
        $data['phone'] = $client->phone;

        $data['total_sales'] = DB::table('sales')->where('deleted_at', '=', null)->where('client_id', $id)->count();

        $data['total_amount'] = DB::table('sales')
            ->where('deleted_at', '=', null)
            ->where('client_id', $client->id)
            ->sum('GrandTotal');

        $data['total_paid'] = DB::table('sales')
            ->where('deleted_at', '=', null)
            ->where('client_id', $client->id)
            ->sum('paid_amount');

        $data['due'] = $data['total_amount'] - $data['total_paid'];

        $data['total_amount_return'] = DB::table('sale_returns')
            ->where('deleted_at', '=', null)
            ->where('client_id', $client->id)
            ->sum('GrandTotal');

        $data['total_paid_return'] = DB::table('sale_returns')
            ->where('deleted_at', '=', null)
            ->where('client_id', $client->id)
            ->sum('paid_amount');

        $data['return_Due'] = $data['total_amount_return'] - $data['total_paid_return'];

        $symbol = $helpers->Get_Currency();
        $settings = Setting::where('deleted_at', '=', null)->first();

        $pdf = PDF::loadView('pdf.report_client_pdf', [
            'symbol' => $symbol,
            'client' => $data,
            'sales' => $sales_details,
            'setting' => $settings,
        ]);

        return $pdf->download('report_client.pdf');

    }

    //------------- download_report_provider_pdf -----------\\

    public function download_report_provider_pdf(Request $request, $id)
    {
        if (!helpers::checkPermission('Reports_suppliers')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $helpers = new helpers();
        $provider = Provider::where('deleted_at', '=', null)->findOrFail($id);

        $purchases = Purchase::where('deleted_at', '=', null)
            ->where('payment_statut', '!=', 'paid')
            ->where('provider_id', $id)
            ->get();

        $purchases_details = [];

        foreach ($purchases as $purchase) {

            $item_purchase['date'] = $purchase['date'];
            $item_purchase['Ref'] = $purchase['Ref'];
            $item_purchase['GrandTotal'] = number_format($purchase['GrandTotal'], 2, '.', '');
            $item_purchase['paid_amount'] = number_format($purchase['paid_amount'], 2, '.', '');
            $item_purchase['due'] = number_format($item_purchase['GrandTotal'] - $item_purchase['paid_amount'], 2, '.', '');
            $item_purchase['payment_status'] = $purchase['payment_statut'];

            $purchases_details[] = $item_purchase;
        }

        $data['provider_name'] = $provider->name;
        $data['phone'] = $provider->phone;

        $data['total_purchase'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)->count();

        $data['total_amount'] = DB::table('purchases')->where('deleted_at', '=', null)->where('provider_id', $id)
            ->sum('GrandTotal');

        $data['total_paid'] = DB::table('purchases')
            ->where('deleted_at', '=', null)
            ->where('provider_id', $id)
            ->sum('paid_amount');

        $data['due'] = $data['total_amount'] - $data['total_paid'];

        $data['total_amount_return'] = DB::table('purchase_returns')
            ->where('deleted_at', '=', null)
            ->where('provider_id', $id)
            ->sum('GrandTotal');

        $data['total_paid_return'] = DB::table('purchase_returns')
            ->where('deleted_at', '=', null)
            ->where('provider_id', $id)
            ->sum('paid_amount');

        $data['return_Due'] = $data['total_amount_return'] - $data['total_paid_return'];

        $symbol = $helpers->Get_Currency();
        $settings = Setting::where('deleted_at', '=', null)->first();

        $pdf = PDF::loadView('pdf.report_provider_pdf', [
            'symbol' => $symbol,
            'provider' => $data,
            'purchases' => $purchases_details,
            'setting' => $settings,
        ]);

        return $pdf->download('report_provider.pdf');

    }


    //-------------------- product_report -------------\\

    public function product_report(request $request)
    {
        if (!helpers::checkPermission('product_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $Role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($Role->id)->inRole('record_view');
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());
        $array_warehouses_id = $warehouses->pluck('id');

        $products_data = Product::select('id', 'name', 'code', 'is_variant', 'unit_id')
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('code', 'LIKE', "%{$request->search}%");
                });
            });

        $totalRows = $products_data->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }


        $products = $products_data
//            ->offset($offSet)
//            ->limit($perPage)
            ->get();


        $product_details = collect();
        $total_sales = 0;
        foreach ($products as $product) {

            if ($product->is_variant) {
                $variant_id_all = ProductVariant::where('product_id', $product->id)->pluck('id');

                foreach ($variant_id_all as $key => $variant_id) {
                    $variant_data = ProductVariant::select('name')->find($variant_id);

                    $nestedData['id'] = $product->id;
                    $nestedData['name'] = ' [' . $variant_data->name . ']' . $product->name;
                    $nestedData['code'] = $product->code;

                    $nestedData['sold_amount'] = SaleDetail::with('sale')
                        ->where([
                            ['product_id', $product->id],
                            ['product_variant_id', $variant_id]
                        ])
                        ->where(function ($query) use ($request) {
                            if (!helpers::checkPermission('record_view')) {
                                return $query->whereHas('sale', function ($q) use ($request) {
                                    $q->where('user_id', '=', Auth::user()->id);
                                });
                            }
                        })
                        ->where(function ($query) use ($request, $array_warehouses_id) {
                            if ($request->warehouse_id) {
                                return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                    $q->where('warehouse_id', $request->warehouse_id);
                                });
                            } else {
                                return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                    $q->whereIn('warehouse_id', $array_warehouses_id);
                                });

                            }
                        })
                        ->whereBetween('date', array($request->from, $request->to))
                        ->sum('total');

                    $lims_product_sale_data = SaleDetail::select('sale_unit_id', 'quantity')->with('sale')
                        ->where(function ($query) use ($request) {
                            if (!helpers::checkPermission('record_view')) {
                                return $query->whereHas('sale', function ($q) use ($request) {
                                    $q->where('user_id', '=', Auth::user()->id);
                                });
                            }
                        })
                        ->where([
                            ['product_id', $product->id],
                            ['product_variant_id', $variant_id]
                        ])
                        ->where(function ($query) use ($request, $array_warehouses_id) {
                            if ($request->warehouse_id) {
                                return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                    $q->where('warehouse_id', $request->warehouse_id);
                                });
                            } else {
                                return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                    $q->whereIn('warehouse_id', $array_warehouses_id);
                                });

                            }
                        })
                        ->whereBetween('date', array($request->from, $request->to))
                        ->get();

                    $sold_qty = 0;
                    if (count($lims_product_sale_data)) {
                        foreach ($lims_product_sale_data as $product_sale) {
                            $unit = Unit::find($product_sale->sale_unit_id);
                            if ($unit->operator == '*') {
                                $sold_qty += $product_sale->quantity * $unit->operator_value;
                            } elseif ($unit->operator == '/') {
                                $sold_qty += $product_sale->quantity / $unit->operator_value;
                            }
                        }
                    }
                    $unit_shortname = Unit::where('id', $product->unit_id)->first();
                    $nestedData['sold_qty'] = $sold_qty . ' ' . $unit_shortname->ShortName;

                    $product_details->add($nestedData);

                }

            } else {
                $nestedData['id'] = $product->id;
                $nestedData['name'] = $product->name;
                $nestedData['code'] = $product->code;

                $nestedData['sold_amount'] = SaleDetail::with('sale')->where('product_id', $product->id)
                    ->where(function ($query) use ($request) {
                        if (!helpers::checkPermission('record_view')) {
                            return $query->whereHas('sale', function ($q) use ($request) {
                                $q->where('user_id', '=', Auth::user()->id);
                            });
                        }
                    })
                    ->where(function ($query) use ($request, $array_warehouses_id) {
                        if ($request->warehouse_id) {
                            return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                $q->where('warehouse_id', $request->warehouse_id);
                            });
                        } else {
                            return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                $q->whereIn('warehouse_id', $array_warehouses_id);
                            });

                        }
                    })
                    ->whereBetween('date', array($request->from, $request->to))
                    ->sum('total');

                $lims_product_sale_data = SaleDetail::select('sale_unit_id', 'quantity')
                    ->with('sale')->where('product_id', $product->id)
                    ->where(function ($query) use ($request) {
                        if (!helpers::checkPermission('record_view')) {
                            return $query->whereHas('sale', function ($q) use ($request) {
                                $q->where('user_id', '=', Auth::user()->id);
                            });
                        }
                    })
                    ->where(function ($query) use ($request, $array_warehouses_id) {
                        if ($request->warehouse_id) {
                            return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                $q->where('warehouse_id', $request->warehouse_id);
                            });
                        } else {
                            return $query->whereHas('sale', function ($q) use ($request, $array_warehouses_id) {
                                $q->whereIn('warehouse_id', $array_warehouses_id);
                            });

                        }
                    })
                    ->whereBetween('date', array($request->from, $request->to))
                    ->get();

                $sold_qty = 0;
                if (count($lims_product_sale_data)) {
                    foreach ($lims_product_sale_data as $product_sale) {
                        $unit = Unit::find($product_sale->sale_unit_id);

                        if ($unit->operator == '*') {
                            $sold_qty += $product_sale->quantity * $unit->operator_value;
                        } elseif ($unit->operator == '/') {
                            $sold_qty += $product_sale->quantity / $unit->operator_value;
                        }

                    }
                }

                $unit_shortname = Unit::where('id', $product->unit_id)->first();

                $nestedData['sold_qty'] = $sold_qty . ' ' . $unit_shortname->ShortName;

                $product_details->add($nestedData);
            }


        }


        return response()->json([
            'products' => $product_details,
            'totalRows' => $totalRows,
            'warehouses' => $warehouses,
        ]);

    }


    //-------------------- sale product details -------------\\

    public function sale_products_details(request $request)
    {
        if (!helpers::checkPermission('product_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        // How many items do you want to display.
//        $perPage = $request->limit;
//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;

        $sale_details_data = SaleDetail::with('product', 'sale', 'sale.client', 'sale.warehouse', 'sale.user')
            ->where(function ($query) use ($request) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->whereHas('sale', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->whereBetween('date', array($request->from, $request->to))
            ->where('product_id', $request->id)

            //Filters
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('Ref'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('sale', function ($q) use ($request) {
                            $q->where('Ref', 'LIKE', "{$request->Ref}");
                        });
                    });
                });
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('client_id'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('sale.client', function ($q) use ($request) {
                            $q->where('client_id', $request->client_id);
                        });
                    });
                });
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('sale.warehouse', function ($q) use ($request) {
                            $q->where('warehouse_id', $request->warehouse_id);
                        });
                    });
                });
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('user_id'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('sale.user', function ($q) use ($request) {
                            $q->where('user_id', $request->user_id);
                        });
                    });
                });
            })

            //search
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('sale.client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $sale_details_data->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }
        $sale_details = $sale_details_data
//            ->offset($offSet)
//            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get();

        $data = collect();
        foreach ($sale_details as $detail) {

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

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail->date;
            $item['Ref'] = $detail['sale']->Ref;
            $item['sale_id'] = $detail['sale']->id;
            $item['client_name'] = $detail['sale']['client']->name;
            $item['warehouse_name'] = $detail['sale']['warehouse']->name;
            $item['quantity'] = $detail->quantity . ' ' . $unit->ShortName;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_sale'] = $unit->ShortName;

            $data->add($item);
        }

        $customers = client::where('deleted_at', '=', null)->get(['id', 'name']);
        $users = User::get(['id', 'username']);

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());

        return response()->json([
            'totalRows' => $totalRows,
            'sales' => $data,
            'customers' => $customers,
            'warehouses' => $warehouses,
            'users' => $users,
        ]);

    }


    //-------------------- product_sales_report  -------------\\

    public function product_sales_report(request $request)
    {
        if (!helpers::checkPermission('product_sales_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        // How many items do you want to display.
//        $perPage = $request->limit;

//        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
//        $offSet = ($pageStart * $perPage) - $perPage;
//        $order = $request->SortField;
//        $dir = $request->SortType;
//        $helpers = new helpers();
        // Filter fields With Params to retrieve
//        $param = array(
//            0 => '=',
//            1 => '=',
//        );
//        $columns = array(
//            0 => 'client_id',
//            1 => 'warehouse_id',
//        );
        $data = collect();

        $sale_details_data = SaleDetail::with('product', 'sale', 'sale.client', 'sale.warehouse')
            ->where(function ($query) use ($request) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->whereHas('sale', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->whereBetween('date', array($request->from, $request->to));

        // Filter
        $sale_details_Filtred = $sale_details_data->where(function ($query) use ($request) {
            return $query->when($request->filled('client_id'), function ($query) use ($request) {
                return $query->whereHas('sale.client', function ($q) use ($request) {
                    $q->where('client_id', '=', $request->client_id);
                });
            });
        })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->whereHas('sale.warehouse', function ($q) use ($request) {
                        $q->where('warehouse_id', '=', $request->warehouse_id);
                    });
                });
            })

            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('sale.client', function ($q) use ($request) {
                            $q->where('username', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('sale', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });


        $totalRows = $sale_details_Filtred->count();
//        if ($perPage == "-1") {
//            $perPage = $totalRows;
//        }

        $sale_details = $sale_details_Filtred
//            ->offset($offSet)
//            ->limit($perPage)
//            ->orderBy($order, $dir)
            ->get();

        foreach ($sale_details as $detail) {

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

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail->date;
            $item['Ref'] = $detail['sale']->Ref;
            $item['client_name'] = $detail['sale']['client']->name;
            $item['warehouse_name'] = $detail['sale']['warehouse']->name;
            $item['quantity'] = $detail->quantity;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_sale'] = $unit->ShortName;

            $data->add($item);
        }


        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());

        $customers = client::where('deleted_at', '=', null)->get(['id', 'name']);

        return response()->json([
            'totalRows' => $totalRows,
            'sales' => $data,
            'customers' => $customers,
            'warehouses' => $warehouses,
        ]);

    }


    //-------------------- product_purchases_report  -------------\\

    public function product_purchases_report(request $request)
    {
        if (!helpers::checkPermission('product_purchases_report')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        // How many items do you want to display.
        $perPage = $request->limit;

        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;
        $helpers = new helpers();
        // Filter fields With Params to retrieve
        $param = array(
            0 => '=',
            1 => '=',
        );
        $columns = array(
            0 => 'provider_id',
            1 => 'warehouse_id',
        );
        $data = array();

        $purchase_details_data = PurchaseDetail::with('product', 'purchase', 'purchase.provider', 'purchase.warehouse')
            ->where(function ($query) use ($view_records, $request) {
                if (!$view_records) {
                    return $query->whereHas('purchase', function ($q) use ($request) {
                        $q->where('user_id', '=', Auth::user()->id);
                    });
                }
            })
            ->where(function ($query) use ($request) {
                return $query->whereHas('purchase', function ($q) use ($request) {
                    $q->whereBetween('date', array($request->from, $request->to));
                });
            });

        // Filter
        $purchase_details_Filtred = $purchase_details_data->where(function ($query) use ($request) {
            return $query->when($request->filled('provider_id'), function ($query) use ($request) {
                return $query->whereHas('purchase.provider', function ($q) use ($request) {
                    $q->where('provider_id', '=', $request->provider_id);
                });
            });
        })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse_id'), function ($query) use ($request) {
                    return $query->whereHas('purchase.warehouse', function ($q) use ($request) {
                        $q->where('warehouse_id', '=', $request->warehouse_id);
                    });
                });
            })

            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        return $query->whereHas('purchase.provider', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('purchase', function ($q) use ($request) {
                                $q->where('Ref', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('product', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('purchase.warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });


        $totalRows = $purchase_details_Filtred->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }

        $purchase_details = $purchase_details_Filtred
            ->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($purchase_details as $detail) {

            //-------check if detail has purchase_unit_id Or Null
            if ($detail->purchase_unit_id !== null) {
                $unit = Unit::where('id', $detail->purchase_unit_id)->first();
            } else {
                $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $product_name = $productsVariants->name . '-' . $detail['product']['name'];

            } else {
                $product_name = $detail['product']['name'];
            }

            $item['date'] = $detail['purchase']->date;
            $item['Ref'] = $detail['purchase']->Ref;
            $item['provider_name'] = $detail['purchase']['provider']->name;
            $item['warehouse_name'] = $detail['purchase']['warehouse']->name;
            $item['quantity'] = $detail->quantity;
            $item['total'] = $detail->total;
            $item['product_name'] = $product_name;
            $item['unit_purchase'] = $unit->ShortName;

            $data[] = $item;
        }

        //get warehouses assigned to user
        $user_auth = auth()->user();
        $warehouses = helpers::getWarehouses($user_auth);

        $suppliers = Provider::where('deleted_at', '=', null)->get(['id', 'name']);

        return response()->json([
            'totalRows' => $totalRows,
            'purchases' => $data,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);

    }

}