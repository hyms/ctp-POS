<?php

namespace App\Http\Controllers;

use App\Mail\Payment_Sale;
use App\Models\Client;
use App\Models\PaymentSale;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Setting;
use App\utils\helpers;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\PaymentWithCreditCard;
use Inertia\Inertia;
use Nexmo\Client\Credentials\Basic;
use Nwidart\Modules\Facades\Module;
use App\Models\sms_gateway;

class PaymentSalesController extends Controller
{

    //------------- Get All Payments Sales --------------\\

    public function index(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'Reports_payments_Sales', PaymentSale::class);

//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $data = collect();
        $filter = collect($request->get('filter'));
//        if ($request->collect()->count() == 0) {
//            $request['from'] = Carbon::now()->subMonth(5);
//            $request['to'] = Carbon::now();
//        }
        // Check If User Has Permission View  All Records
        $Payments = PaymentSale::with('sale', 'sale.client')
            ->where('deleted_at', '=', null);
//            ->where(function ($query) use ($view_records) {
//                if (!$view_records) {
//                    return $query->where('user_id', '=', Auth::user()->id);
//                }
//            })

        $Payments = helpers::getFilter($filter,
            $Payments,
            collect([
                collect(['type' => 'date', 'key' => 'date', 'filter' => 'startDate', 'filter2' => 'endDate']),
                collect(['type' => 'string', 'key' => 'Reglement', 'filter' => 'Reg']),
                collect(['type' => 'string', 'key' => 'Ref', 'filter' => 'Ref']),
                collect(['type' => 'rel_string', 'key' => 'sale', 'key2' => 'Ref', 'filter' => 'sale']),
                collect(['type' => 'rel', 'key' => 'sale', 'key2' => 'client_id', 'filter' => 'client']),
            ])
        );

        $Payments = $Payments->orderByDesc('date')
            ->get();

        foreach ($Payments as $Payment) {

            $item['date'] = $Payment->date;
            $item['Ref'] = $Payment->Ref;
            $item['Ref_Sale'] = $Payment['sale']?->Ref;
            $item['client_name'] = $Payment['sale']?->client?->company_name;
            $item['Reglement'] = $Payment->Reglement;
            $item['montant'] = $Payment->montant;
            // $item['montant'] = number_format($Payment->montant, 2, '.', '');
            $data->add($item);
        }

        $clients = Client::where('deleted_at', '=', null)->get(['id', 'company_name as name']);
        $sales = Sale::whereIn('id', $Payments->pluck('sale_id'))->get(['Ref', 'id']);

        Inertia::share('titlePage', 'Pagos de Ventas');
        return Inertia::render('Reports/payments/payments_sales', [
            'payments' => $data,
            'sales' => $sales,
            'clients' => $clients,
        ]);

    }

    //----------- Store new Payment Sale --------------\\

    public function store(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', PaymentSale::class);

        DB::transaction(function () use ($request) {
            $helpers = new helpers();
//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $sale = Sale::findOrFail($request['sale_id']);

            // Check If User Has Permission view All Records
//            if (!$view_records) {
            // Check If User->id === sale->id
//                $this->authorizeForUser($request->user('api'), 'check_record', $sale);
//            }

            try {

                $total_paid = $sale->paid_amount + $request['montant'];
                $due = $sale->GrandTotal - $total_paid;

                if ($due === 0.0 || $due < 0.0) {
                    $payment_statut = 'paid';
                } else if ($due !== $sale->GrandTotal) {
                    $payment_statut = 'partial';
                } else if ($due === $sale->GrandTotal) {
                    $payment_statut = 'unpaid';
                }

                if ($request['montant'] > 0) {

                    PaymentSale::create([
                        'sale_id' => $request['sale_id'],
                        'Ref' => $this->getNumberOrder(),
                        'date' => $request['date'],
                        'Reglement' => $request['Reglement'],
                        'montant' => $request['montant'],
                        'change' => $request['change'],
                        'notes' => $request['notes'],
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

        }, 10);

        return response()->json(['success' => true, 'message' => 'Payment Create successfully'], 200);
    }

    //------------ function show -----------\\

    public function show($id)
    {
        //

    }

    //----------- Update Payments Sale --------------\\

    public function update(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', PaymentSale::class);

        DB::transaction(function () use ($id, $request) {
            $helpers = new helpers();
//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $payment = PaymentSale::findOrFail($id);

            // Check If User Has Permission view All Records
//            if (!$view_records) {
            // Check If User->id === payment->id
//                $this->authorizeForUser($request->user('api'), 'check_record', $payment);
//            }

            $sale = Sale::find($payment->sale_id);
            $old_total_paid = $sale->paid_amount - $payment->montant;
            $new_total_paid = $old_total_paid + $request['montant'];

            $due = $sale->GrandTotal - $new_total_paid;
            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due !== $sale->GrandTotal) {
                $payment_statut = 'partial';
            } else if ($due === $sale->GrandTotal) {
                $payment_statut = 'unpaid';
            }

            try {


                $payment->update([
                    'date' => $request['date'],
                    'Reglement' => $request['Reglement'],
                    'montant' => $request['montant'],
                    'change' => $request['change'],
                    'notes' => $request['notes'],
                ]);

                $sale->update([
                    'paid_amount' => $new_total_paid,
                    'payment_statut' => $payment_statut,
                ]);

            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

        }, 10);

        return response()->json(['success' => true, 'message' => 'Payment Update successfully'], 200);
    }

    //----------- Delete Payment Sales --------------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', PaymentSale::class);

        DB::transaction(function () use ($id, $request) {
//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $payment = PaymentSale::findOrFail($id);

            // Check If User Has Permission view All Records
//            if (!$view_records) {
            // Check If User->id === payment->id
//                $this->authorizeForUser($request->user('api'), 'check_record', $payment);
//            }

            $sale = Sale::find($payment->sale_id);
            $total_paid = $sale->paid_amount - $payment->montant;
            $due = $sale->GrandTotal - $total_paid;

            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due !== $sale->GrandTotal) {
                $payment_statut = 'partial';
            } else if ($due === $sale->GrandTotal) {
                $payment_statut = 'unpaid';
            }

            PaymentSale::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            $sale->update([
                'paid_amount' => $total_paid,
                'payment_statut' => $payment_statut,
            ]);

        }, 10);

        return response()->json(['success' => true, 'message' => 'Payment Delete successfully'], 200);

    }

    //----------- Reference order Payment Sales --------------\\

    public function getNumberOrder()
    {
        $last = DB::table('payment_sales')->latest('id')->first();
        return helpers::get_code($last?->Ref, 'RO');
    }

    //----------- Payment Sale PDF --------------\\

    public function payment_sale(Request $request, $id)
    {
        $payment = PaymentSale::with('sale', 'sale.client')->findOrFail($id);

        $payment_data['sale_Ref'] = $payment['sale']->Ref;
        $payment_data['client_name'] = $payment['sale']['client']->name;
        $payment_data['client_phone'] = $payment['sale']['client']->phone;
        $payment_data['client_adr'] = $payment['sale']['client']->adresse;
        $payment_data['client_email'] = $payment['sale']['client']->email;
        $payment_data['montant'] = $payment->montant;
        $payment_data['Ref'] = $payment->Ref;
        $payment_data['date'] = $payment->date;
        $payment_data['Reglement'] = $payment->Reglement;

        $helpers = new helpers();
        $settings = Setting::where('deleted_at', '=', null)->first();
        $symbol = $helpers->Get_Currency_Code();

        $pdf = PDF::loadView('pdf.payment_sale', [
            'symbol' => $symbol,
            'setting' => $settings,
            'payment' => $payment_data,
        ]);

        return $pdf->download('Payment_Sale.pdf');

    }

    //------------- Send Payment Sale on Email -----------\\

    public function SendEmail(Request $request)
    {

        $this->authorizeForUser($request->user('api'), 'view', PaymentSale::class);

        $payment['id'] = $request->id;
        $payment['Ref'] = $request->Ref;
        $settings = Setting::where('deleted_at', '=', null)->first();
        $payment['company_name'] = $settings->CompanyName;

        $pdf = $this->payment_sale($request, $payment['id']);
        $this->Set_config_mail(); // Set_config_mail => BaseController
        $mail = Mail::to($request->to)->send(new Payment_Sale($payment, $pdf));
        return $mail;
    }

    //-------------------Sms Notifications -----------------\\
    public function Send_SMS(Request $request)
    {
        $payment = PaymentSale::with('sale', 'sale.client')->findOrFail($request->id);
        $settings = Setting::where('deleted_at', '=', null)->first();
        $gateway = sms_gateway::where('id', $settings->sms_gateway)
            ->where('deleted_at', '=', null)->first();

        $url = url('/api/payment_sale_pdf/' . $request->id);
        $receiverNumber = $payment['sale']['client']->phone;
        $message = "Dear" . ' ' . $payment['sale']['client']->name . " \n We are contacting you in regard to a Payment #" . $payment['sale']->Ref . ' ' . $url . ' ' . "that has been created on your account. \n We look forward to conducting future business with you.";

        //twilio
        if ($gateway->title == "twilio") {
            try {

                $account_sid = env("TWILIO_SID");
                $auth_token = env("TWILIO_TOKEN");
                $twilio_number = env("TWILIO_FROM");

                $client = new Client_Twilio($account_sid, $auth_token);
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number,
                    'body' => $message]);

            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }

            //nexmo
        } elseif ($gateway->title == "nexmo") {
            try {

                $basic = new Basic(env("NEXMO_KEY"), env("NEXMO_SECRET"));
                $client = new \Nexmo\Client($basic);
                $nexmo_from = env("NEXMO_FROM");

                $message = $client->message()->send([
                    'to' => $receiverNumber,
                    'from' => $nexmo_from,
                    'text' => $message
                ]);

            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
    }

}
