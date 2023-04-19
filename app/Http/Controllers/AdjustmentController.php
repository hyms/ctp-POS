<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Role;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdjustmentController extends Controller
{

    //------------ Show All Adjustement  -----------\\

    public function index(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Adjustment::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        // Check If User Has Permission View  All Records
        $Adjustments = Adjustment::with('warehouse')
            ->where('deleted_at', '=', null)
            ->where(function ($query) {
                return $query->where('user_id', '=', Auth::user()->id);
            });

        $Adjustments = $Adjustments->get();
        $data = collect();
        foreach ($Adjustments as $Adjustment) {
            $item['id'] = $Adjustment->id;
            $item['date'] = $Adjustment->date;
            $item['Ref'] = $Adjustment->Ref;
            $item['warehouse_name'] = $Adjustment['warehouse']->name;
            $item['items'] = $Adjustment->items;
            $data->add($item);
        }

        //get warehouses assigned to user
        $warehouses = $this->getWarehouses();
        Inertia::share('titlePage', 'Ajustes en stock');
        return Inertia::render('Adjustment/Index_Adjustment', [
            'adjustments' => $data,
            'warehouses' => $warehouses,
        ]);

    }

    //------------ Store New Adjustement -----------\\

    public function store(Request $request)
    {

//        $this->authorizeForUser($request->user('api'), 'create', Adjustment::class);

        request()->validate([
            'warehouse_id' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $data = collect($request['details']);
            $order = new Adjustment;
            $order->date = $request->date;
            $order->Ref = $this->getNumberOrder();
            $order->warehouse_id = $request->warehouse_id;
            $order->notes = $request->notes;
            $order->items = $data->count();
            $order->user_id = Auth::user()->id;
            $order->save();

            $i = 0;
            $orderDetails = collect();
            foreach ($data as $key => $value) {
                $orderDetails->add([
                    'adjustment_id' => $order->id,
                    'quantity' => $value['quantity'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'type' => $value['type'],
                ]);
                $product_warehouse = $this->getProduct_warehouse($value, $order);
                if ($product_warehouse) {
                    if ($value['type'] == "add") {
                        $product_warehouse->qty += $value['quantity'];
                    } else {
                        $product_warehouse->qte -= $value['quantity'];
                    }
                    $product_warehouse->save();
                }
            }
            AdjustmentDetail::insert($orderDetails);
        }, 10);

        return response()->json(['success' => true]);
    }

    //------------ function show -----------\\

    public function show($id)
    {
        //

    }

    //--------------- Update Adjustment ----------------------\\

    public function update(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'update', Adjustment::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $current_adjustment = Adjustment::findOrFail($id);

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === Adjustment->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $current_adjustment);
//        }

        request()->validate([
            'warehouse_id' => 'required',
        ]);

        DB::transaction(function () use ($request, $id, $current_adjustment) {

            $old_adjustment_details = AdjustmentDetail::where('adjustment_id', $id)->get();
            $new_adjustment_details = collect($request['details']);

            // Get Ids for new Details
            $new_products_id = collect();
            foreach ($new_adjustment_details as $new_detail) {
                $new_products_id->add($new_detail['id']);
            }

            $old_products_id = collect();
            // Init Data with old Parametre
            foreach ($old_adjustment_details as $key => $value) {
                $old_products_id->add($value->id);
                $product_warehouse = $this->getProduct_warehouse($value,$current_adjustment);

                if ($product_warehouse) {
                    if ($value['type'] == "add") {
                        $product_warehouse->qte -= $value['quantity'];
                    } else {
                        $product_warehouse->qte += $value['quantity'];
                    }
                    $product_warehouse->save();
                }

                // Delete Detail
                if (!in_array($old_products_id[$key], $new_products_id)) {
                    $AdjustmentDetail = AdjustmentDetail::findOrFail($value->id);
                    $AdjustmentDetail->delete();
                }

            }

            // Update Data with New request
            foreach ($new_adjustment_details as $key => $product_detail) {
                $product_warehouse = $this->getProduct_warehouse($product_detail,$request);
                if ($product_warehouse) {
                    if ($product_detail['type'] == "add") {
                        $product_warehouse->qte += $product_detail['quantity'];
                    } else {
                        $product_warehouse->qte -= $product_detail['quantity'];
                    }
                    $product_warehouse->save();
                }

                $orderDetails['adjustment_id'] = $id;
                $orderDetails['quantity'] = $product_detail['quantity'];
                $orderDetails['product_id'] = $product_detail['product_id'];
                $orderDetails['product_variant_id'] = $product_detail['product_variant_id'];
                $orderDetails['type'] = $product_detail['type'];

                if (!$old_products_id->contains($product_detail['id'])) {
                    AdjustmentDetail::Create($orderDetails);
                } else {
                    AdjustmentDetail::where('id', $product_detail['id'])->update($orderDetails);
                }

            }

            $current_adjustment->update([
                'warehouse_id' => $request['warehouse_id'],
                'notes' => $request['notes'],
                'date' => $request['date'],
            ]);

        }, 10);

        return response()->json(['success' => true]);
    }

    //------------ Delete Adjustement -----------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Adjustment::class);

        DB::transaction(function () use ($id, $request) {
//            $role = Auth::user()->roles()->first();
//            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $current_adjustment = Adjustment::findOrFail($id);
            $old_adjustment_details = AdjustmentDetail::where('adjustment_id', $id)->get();

            // Check If User Has Permission view All Records
//            if (!$view_records) {
//                // Check If User->id === current_adjustment->id
//                $this->authorizeForUser($request->user('api'), 'check_record', $current_adjustment);
//            }

            // Init Data with old Parametre
            foreach ($old_adjustment_details as $key => $value) {
                $product_warehouse = $this->getProduct_warehouse($value, $current_adjustment);
                if ($product_warehouse) {
                    if ($value['type'] == "add") {
                        $product_warehouse->qte -= $value['quantity'];
                    } else {
                        $product_warehouse->qte += $value['quantity'];
                    }
                    $product_warehouse->save();
                }

            }
            $current_adjustment->details()->delete();

            $current_adjustment->update([
                'deleted_at' => Carbon::now(),
            ]);

        }, 10);

        return response()->json(['success' => true], 200);
    }

    //------------ Reference Number of Adjustement  -----------\\

    public function getNumberOrder()
    {
        $last = DB::table('adjustments')->latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = Str::of($item)->explode("_");
            $inMsg = $nwMsg[1] + 1;
            $code = "{$nwMsg[0]}_{$inMsg}";
        } else {
            $code = "AS_1111";
        }
        return $code;

    }

    //---------------- Show Form Create Adjustment ---------------\\

    public function create(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', Adjustment::class);

        //get warehouses assigned to user
        $warehouses = $this->getWarehouses();
        Inertia::share('titlePage', 'Ajustes en stock');
        return Inertia::render('Adjustment/Form_Adjustment', ['warehouses' => $warehouses]);
    }

    //-------------Show Form Edit Adjustment-----------\\

    public function edit(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'update', Adjustment::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $Adjustment_data = Adjustment::with('details.product')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);
        $details = collect();
        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === Adjustment->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $Adjustment_data);
//        }

        if ($Adjustment_data->warehouse_id) {
            if (Warehouse::where('id', $Adjustment_data->warehouse_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $adjustment['warehouse_id'] = $Adjustment_data->warehouse_id;
            } else {
                $adjustment['warehouse_id'] = '';
            }
        } else {
            $adjustment['warehouse_id'] = '';
        }

        $adjustment['notes'] = $Adjustment_data->notes;
        $adjustment['date'] = $Adjustment_data->date;

        $detail_id = 0;
        foreach ($Adjustment_data['details'] as $detail) {

            if ($detail->product_variant_id) {
                $item_product = product_warehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)
                    ->where('product_variant_id', $detail->product_variant_id)
                    ->where('warehouse_id', $Adjustment_data->warehouse_id)
                    ->first();

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $data['product_variant_id'] = $detail->product_variant_id;
                $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];


            } else {
                $item_product = product_warehouse::where('product_id', $detail->product_id)
                    ->where('deleted_at', '=', null)
                    ->where('warehouse_id', $Adjustment_data->warehouse_id)
                    ->where('product_variant_id', '=', null)
                    ->first();


                $data['product_variant_id'] = null;
                $data['code'] = $detail['product']['code'];
            }
            $data['id'] = $detail->id;
            $data['detail_id'] = $detail_id += 1;
            $data['quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['name'] = $detail['product']['name'];
            $data['current'] = $item_product ? $item_product->qty : 0;
            $data['type'] = $detail->type;
            $data['unit'] = $detail['product']['unit']->ShortName;
            $item_product ? $data['del'] = 0 : $data['del'] = 1;

            $details->add($data);
        }

        //get warehouses assigned to user
        $warehouses = $this->getWarehouses();

        return response()->json([
            'details' => $details,
            'adjustment' => $adjustment,
            'warehouses' => $warehouses,
        ]);
    }

    //---------------- Get Details Adjustment-----------------\\

    public function Adjustment_detail(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'view', Adjustment::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $Adjustment_data = Adjustment::with('details.product.unit')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);
        $details = array();
        // Check If User Has Permission view All Records
//        if (!$view_records) {
//             Check If User->id === Adjustment->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $Adjustment_data);
//        }

        $Adjustment['Ref'] = $Adjustment_data->Ref;
        $Adjustment['date'] = $Adjustment_data->date;
        $Adjustment['note'] = $Adjustment_data->notes;
        $Adjustment['warehouse'] = $Adjustment_data['warehouse']->name;
        $details = collect();
        foreach ($Adjustment_data['details'] as $detail) {
            $data['quantity'] = $detail->quantity;
            $data['name'] = $detail['product']['name'];
            $data['type'] = $detail->type;
            $data['unit'] = $detail['product']['unit']->ShortName;
            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)
                    ->first();
                $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];
            } else {
                $data['code'] = $detail['product']['code'];
            }

            $details->add($data);
        }

        return response()->json([
            'details' => $details,
            'adjustment' => $Adjustment,
        ]);
    }

    /**
     * @param mixed $value
     * @param $current_adjustment
     * @return mixed
     */
    public function getProduct_warehouse(mixed $value, $current_adjustment): mixed
    {
        if ($value['product_variant_id'] !== null) {
            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                ->where('warehouse_id', $current_adjustment->warehouse_id)
                ->where('product_id', $value['product_id'])
                ->where('product_variant_id', $value['product_variant_id'])
                ->first();
        } else {
            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                ->where('warehouse_id', $current_adjustment->warehouse_id)
                ->where('product_id', $value['product_id'])
                ->first();
        }
        return $product_warehouse;
    }

    /**
     * @return mixed
     */
    public function getWarehouses()
    {
        $user_auth = auth()->user();
        if ($user_auth->is_all_warehouses) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name'])->map(function ($item, $key) {
                return ['value' => $item->id, 'title' => $item->name];
            });
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name'])
                ->map(function ($item, $key) {
                    return ['value' => $item->id, 'title' => $item->name];
                });
        }
        return $warehouses;
    }


}
