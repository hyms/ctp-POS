<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleReturn;
use App\Models\PaymentSaleReturns;
use App\Models\Sale;
use App\Models\PaymentSale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{

    //------------- Get ALL Customers -------------\\

    public function index(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Client::class);

        $clients = Client::where('deleted_at', '=', null)
            ->get();
        $data = collect();
        foreach ($clients as $client) {

            $item['total_amount'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->sum('GrandTotal');

            $item['total_paid'] = DB::table('sales')
                ->where('sales.deleted_at', '=', null)
                ->where('sales.client_id', $client->id)
                ->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = DB::table('sale_returns')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->sum('GrandTotal');

            $item['total_paid_return'] = DB::table('sale_returns')
                ->where('sale_returns.deleted_at', '=', null)
                ->where('sale_returns.client_id', $client->id)
                ->sum('paid_amount');

            $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

            $item['id'] = $client->id;
            $item['name'] = $client->name;
            $item['phone'] = $client->phone;
            $item['nit_ci'] = $client->nit_ci;
            $item['code'] = $client->code;
            $item['email'] = $client->email;
            $item['company_name'] = $client->company_name;
            $item['city'] = $client->city;
            $item['adresse'] = $client->adresse;
            $data->add($item);
        }

        $company_info = Setting::where('deleted_at', '=', null)->first();

        Inertia::share('titlePage', 'Clientes');
        return Inertia::render('People/Clients',[
            'clients' => $data,
            'company_info' => $company_info,
        ]);
    }

    //------------- Store new Customer -------------\\

    public function store(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', Client::class);

        $this->validate($request, [
            'name' => 'required',
            ]
        );

        Client::create([
            'name' => $request['name'],
            'code' => $this->getNumberOrder(),
            'adresse' => $request['adresse']??'',
            'phone' => $request['phone']??'',
            'email' => $request['email']??'',
            'company_name' => $request['company_name']??'',
            'city' => $request['city']??'',
            'nit_ci' => $request['nit_ci']??'',
        ]);
        return response()->json(['success' => true]);
    }

    //------------ function show -----------\\

    public function show($id){
        //
        
    }

    //------------- Update Customer -------------\\

    public function update(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', Client::class);
        
        $this->validate($request, [
            'name' => 'required',
            ]
        );

        Client::whereId($id)->update([
            'name' => $request['name'],
            'adresse' => $request['adresse']??'',
            'phone' => $request['phone']??'',
            'email' => $request['email']??'',
            'company_name' => $request['company_name']??'',
            'city' => $request['city']??'',
            'nit_ci' => $request['nit_ci']??'',
        ]);
        return response()->json(['success' => true]);

    }

    //------------- delete client -------------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Client::class);

        Client::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => true]);
    }

    //------------- get Number Order Customer -------------\\

    public function getNumberOrder()
    {
        $last = DB::table('clients')->latest('id')->first();

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }

    //------------- Get Clients Without Paginate -------------\\

    public function Get_Clients_Without_Paginate()
    {
        $clients = Client::where('deleted_at', '=', null)->get(['id', 'name']);
        return response()->json($clients);
    }

    // import clients
    public function import_clients(Request $request)
    {
        $file_upload = $request->file('clients');
        $ext = pathinfo($file_upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if ($ext != 'csv') {
            return response()->json([
                'msg' => 'must be in csv format',
                'status' => false,
            ]);
        } else {
            $data = array();
            $rowcount = 0;
            if (($handle = fopen($file_upload, "r")) !== false) {
                $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
                $header = fgetcsv($handle, $max_line_length);
                $header_colcount = count($header);
                while (($row = fgetcsv($handle, $max_line_length)) !== false) {
                    $row_colcount = count($row);
                    if ($row_colcount == $header_colcount) {
                        $entry = array_combine($header, $row);
                        $data[] = $entry;
                    } else {
                        return null;
                    }
                    $rowcount++;
                }
                fclose($handle);
            } else {
                return null;
            }
           
            $rules = array('name' => 'required');

            //-- Create New Client
            foreach ($data as $key => $value) {
                $input['name'] = $value['name'];

                $validator = Validator::make($input, $rules);
                if (!$validator->fails()) {
                    
                    Client::create([
                        'name' => $value['name'],
                        'code' => $this->getNumberOrder(),
                        'adresse' => $value['adresse'],
                        'phone' => $value['phone'],
                        'email' => $value['email'],
                        'company_name' => $value['company_name'],
                        'city' => $value['city'],
                        'nit_ci' => $value['nit_ci'],
                    ]);

                }
               

            }

            return response()->json([
                'status' => true,
            ], 200);
        }

    }


     //------------- clients_pay_due -------------\\

     public function clients_pay_due(Request $request)
     {
//         $this->authorizeForUser($request->user('api'), 'pay_due', Client::class);
        
         if($request['amount'] > 0){
            $client_sales_due = Sale::where('deleted_at', '=', null)
            ->where([
                ['payment_statut', '!=', 'paid'],
                ['client_id', $request->client_id]
            ])->get();

            $paid_amount_total = $request->amount;
             $payment_codes="";
            foreach($client_sales_due as $key => $client_sale){
                if($paid_amount_total == 0)
                break;
                $due = $client_sale->GrandTotal  - $client_sale->paid_amount;

                if($paid_amount_total >= $due){
                    $amount = $due;
                    $payment_status = 'paid';
                }else{
                    $amount = $paid_amount_total;
                    $payment_status = 'partial';
                }

                $payment_sale = new PaymentSale();
                $payment_sale->sale_id = $client_sale->id;
                $payment_sale->Ref = app('App\Http\Controllers\PaymentSalesController')->getNumberOrder();
                $payment_sale->date = Carbon::now();
                $payment_sale->Reglement = $request['Reglement'];
                $payment_sale->montant = $amount;
                $payment_sale->change = 0;
                $payment_sale->notes = $request['notes'];
                $payment_sale->user_id = Auth::user()->id;
                $payment_codes .= $client_sale->Ref . " ";
                $payment_sale->save();
                $client_sale->paid_amount += $amount;
                $client_sale->payment_statut = $payment_status;
                $client_sale->save();

                $paid_amount_total -= $amount;
            }
        }
        
         return response()->json(['success' => true,'payment_codes'=>$payment_codes]);
 
     }

    //------------- clients_pay_sale_return_due -------------\\

    public function pay_sale_return_due(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'pay_sale_return_due', Client::class);
        
        if($request['amount'] > 0){
            $client_sell_return_due = SaleReturn::where('deleted_at', '=', null)
            ->where([
                ['payment_statut', '!=', 'paid'],
                ['client_id', $request->client_id]
            ])->get();

            $paid_amount_total = $request->amount;

            foreach($client_sell_return_due as $key => $client_sale_return){
                if($paid_amount_total == 0)
                break;
                $due = $client_sale_return->GrandTotal  - $client_sale_return->paid_amount;

                if($paid_amount_total >= $due){
                    $amount = $due;
                    $payment_status = 'paid';
                }else{
                    $amount = $paid_amount_total;
                    $payment_status = 'partial';
                }

                $payment_sale_return = new PaymentSaleReturns();
                $payment_sale_return->sale_return_id = $client_sale_return->id;
                $payment_sale_return->Ref = app('App\Http\Controllers\PaymentSaleReturnsController')->getNumberOrder();
                $payment_sale_return->date = Carbon::now();
                $payment_sale_return->Reglement = $request['Reglement'];
                $payment_sale_return->montant = $amount;
                $payment_sale_return->change = 0;
                $payment_sale_return->notes = $request['notes'];
                $payment_sale_return->user_id = Auth::user()->id;
                $payment_sale_return->save();

                $client_sale_return->paid_amount += $amount;
                $client_sale_return->payment_statut = $payment_status;
                $client_sale_return->save();

                $paid_amount_total -= $amount;
            }
        }
        
        return response()->json(['success' => true]);

    }

}
