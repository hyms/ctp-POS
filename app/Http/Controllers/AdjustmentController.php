<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
use App\Models\product_warehouse;
use App\Models\ProductVariant;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdjustmentController extends Controller
{

    //------------ Show All Adjustement  -----------\\

    public function index(request $request)
    {
        Inertia::share('titlePage', 'Ajustes en stock');
        return Inertia::render('Adjustment/Index_Adjustment');
    }

    public function getTable(request $request)
    {
        if (!helpers::checkPermission('adjustment_view')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
//get warehouses assigned to user
        $warehouses = $this->getWarehouses();
        // Check If User Has Permission View  All Records
        $Adjustments = Adjustment::with('warehouse')
            ->where('deleted_at', '=', null)
            ->whereIn('warehouse_id', $warehouses->map(function ($item,$key) { return $key; }))
            ->where(function ($query) {
                if (helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            });

        $Adjustments = $Adjustments->orderByDesc('updated_at')->get();
        $data = collect();
        foreach ($Adjustments as $Adjustment) {
            $data->add([
                'id' => $Adjustment->id,
                'date' => Carbon::parse($Adjustment->date)->format('d-m-Y'),
                'Ref' => $Adjustment->Ref,
                'warehouse_name' => $Adjustment['warehouse']->name,
                'items' => $Adjustment->items,
                'updated_at' => Carbon::parse($Adjustment->updated_at)->format('Y-m-d'),
            ]);
        }

        return response()->json([
            'adjustments' => $data,
            'warehouses' => $warehouses,
        ]);

    }

    //------------ Store New Adjustement -----------\\

    public function store(Request $request)
    {
        if (!helpers::checkPermission('adjustment_add')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $request->validate([
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

            $orderDetails = collect();
            foreach ($data as $key => $value) {
                $item = [
                    'adjustment_id' => $order->id,
                    'quantity' => $value['quantity'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'type' => $value['type'],
                ];
                $product_warehouse = $this->getProduct_warehouse($value, $order);
                if ($product_warehouse) {
                    if ($value['type'] == "add") {
                        $product_warehouse->qty += $value['quantity'];
                    } else {
                        $product_warehouse->qty -= $value['quantity'];
                    }
                    $product_warehouse->save();
                }
                $orderDetails->add($item);
            }
            AdjustmentDetail::insert($orderDetails->toArray());
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
        if (!helpers::checkPermission('adjustment_edit')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $current_adjustment = Adjustment::findOrFail($id);

        $request->validate([
            'warehouse_id' => 'required',
        ]);

        DB::transaction(function () use ($request, $id, $current_adjustment) {

            $old_adjustment_details = AdjustmentDetail::where('adjustment_id', $id)->get();
            $new_adjustment_details = collect($request['details']);

            foreach ($old_adjustment_details as $key => $value) {
                $product_warehouse = $this->getProduct_warehouse($value, $current_adjustment);

                if ($product_warehouse) {
                    if ($value['type'] == "add") {
                        $product_warehouse->qty -= $value['quantity'];
                    } else {
                        $product_warehouse->qty += $value['quantity'];
                    }
                    $product_warehouse->save();
                }

                // Delete Detail
                if ($new_adjustment_details->doesntContain('id', '=', $value->id)) {
                    $AdjustmentDetail = AdjustmentDetail::findOrFail($value->id);
                    $AdjustmentDetail->delete();
                }
            }

            // Update Data with New request
            foreach ($new_adjustment_details as $key => $product_detail) {
                $item = collect($product_detail);
                $product_warehouse = $this->getProduct_warehouse($item, $request);
                if ($product_warehouse) {
                    if ($item['type'] == "add") {
                        $product_warehouse->qty += $item['quantity'];
                    } else {
                        $product_warehouse->qty -= $item['quantity'];
                    }
                    $product_warehouse->save();
                }

                $orderDetails['adjustment_id'] = $id;
                $orderDetails['quantity'] = $item['quantity'];
                $orderDetails['product_id'] = $item['product_id'];
                $orderDetails['product_variant_id'] = $item['product_variant_id'];
                $orderDetails['type'] = $item['type'];
                if ($old_adjustment_details->doesntContain('id', '=', $item->get('id'))) {
                    AdjustmentDetail::Create($orderDetails);
                } else {
                    AdjustmentDetail::where('id', $item['id'])->update($orderDetails);
                }

            }

            $current_adjustment->update([
                'warehouse_id' => $request['warehouse_id'],
                'notes' => $request['notes'],
                'date' => $request['date'],
                'items' => $new_adjustment_details->count()
            ]);

        }, 10);

        return response()->json(['success' => true]);
    }

    //------------ Delete Adjustement -----------\\

    public function destroy(Request $request, $id)
    {
        if (!helpers::checkPermission('adjustment_delete')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        DB::transaction(function () use ($id, $request) {
            $current_adjustment = Adjustment::where(function ($query) {
                    if (helpers::checkPermission('record_view')) {
                        return $query->where('user_id', '=', Auth::user()->id);
                    }
                })->findOrFail($id);
            $old_adjustment_details = AdjustmentDetail::where('adjustment_id', $id)->get();

            // Init Data with old Parametre
            foreach ($old_adjustment_details as $key => $value) {
                $product_warehouse = $this->getProduct_warehouse($value, $current_adjustment);
                if ($product_warehouse) {
                    if ($value['type'] == "add") {
                        $product_warehouse->qty -= $value['quantity'];
                    } else {
                        $product_warehouse->qty += $value['quantity'];
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
        return helpers::get_code($last?->Ref, "AJ");
    }

    //---------------- Show Form Create Adjustment ---------------\\

    public function create(Request $request)
    {
        //get warehouses assigned to user
        $warehouses = $this->getWarehouses();
        Inertia::share('titlePage', 'Ajustes en stock');
        return Inertia::render('Adjustment/Form_Adjustment', ['warehouses' => $warehouses]);
    }

    //-------------Show Form Edit Adjustment-----------\\

    public function edit(Request $request, $id)
    {

        $Adjustment_data = Adjustment::with('details.product')
            ->where('deleted_at', '=', null)
            ->where(function ($query) {
                if (helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })->findOrFail($id);
        $details = collect();
        $adjustment['warehouse_id'] = '';
        if ($Adjustment_data->warehouse_id) {
            if (Warehouse::where('id', $Adjustment_data->warehouse_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $adjustment['warehouse_id'] = $Adjustment_data->warehouse_id;
            }
        }

        $adjustment['notes'] = $Adjustment_data->notes;
        $adjustment['date'] = $Adjustment_data->date;
        $adjustment['id'] = $Adjustment_data->id;

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
        Inertia::share('titlePage', 'Ajustes en stock');
        return Inertia::render('Adjustment/Form_Adjustment', [
            'details' => $details,
            'adjustment' => $adjustment,
            'warehouses' => $warehouses,
        ]);
    }

    //---------------- Get Details Adjustment-----------------\\

    public function Adjustment_detail(Request $request, $id)
    {

        $Adjustment_data = Adjustment::with('details.product.unit')
            ->where('deleted_at', '=', null)
            ->where(function ($query) {
                if (!helpers::checkPermission('record_view')) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })->findOrFail($id);

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
            $warehouses = Warehouse::where('deleted_at', '=', null)->pluck('name', 'id');
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->pluck('name', 'id');
        }
        return $warehouses;
    }


}
