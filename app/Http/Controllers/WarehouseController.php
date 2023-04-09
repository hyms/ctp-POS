<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WarehouseController extends Controller
{

    //----------- GET ALL  Warehouse --------------\\

    public function index(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Warehouse::class);

        // How many items do you want to display.

        $warehouses = Warehouse::get();
        Inertia::share('titlePage', 'Almacenes');
        return Inertia::render('Warehouses',
            ['warehouses' => $warehouses]);
    }

    //----------- Store new Warehouse --------------\\

    public function store(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', Warehouse::class);

        request()->validate([
            'name' => 'required',
        ]);

        DB::transaction(function () use ($request) {

            $Warehouse = new Warehouse;
            $Warehouse->name = $request['name'];
            $Warehouse->mobile = $request['mobile']??'';
//            $Warehouse->country = $request['country'];
            $Warehouse->city = $request['city']??'';
            $Warehouse->email = $request['email']??'';
            $Warehouse->save();

            $products = Product::get()
                ->pluck('id')
                ->toArray();

            if ($products) {
                foreach ($products as $product) {
                    $product_warehouse = [];
                    $Product_Variants = ProductVariant::where('product_id', $product)
                        ->where('deleted_at', null)
                        ->get();

                    if ($Product_Variants->isNotEmpty()) {
                        foreach ($Product_Variants as $product_variant) {

                            $product_warehouse[] = [
                                'product_id' => $product,
                                'warehouse_id' => $Warehouse->id,
                                'product_variant_id' => $product_variant->id,
                            ];
                        }
                    } else {
                        $product_warehouse[] = [
                            'product_id' => $product,
                            'warehouse_id' => $Warehouse->id,
                            'product_variant_id' => null,
                        ];
                    }

                    product_warehouse::insert($product_warehouse);
                }
            }

        }, 10);

        return response()->json(['success' => true]);
    }

    //------------ function show -----------\\

    public function show($id){
        //
        
        }

    //-----------Update Warehouse --------------\\

    public function update(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', Warehouse::class);

        request()->validate([
            'name' => 'required',
        ]);

        Warehouse::whereId($id)->update([
            'name' => $request['name'],
            'mobile' => $request['mobile']??'',
            'country' => $request['country']??'',
            'city' => $request['city']??'',
            'email' => $request['email']??'',
        ]);
        return response()->json(['success' => true]);
    }

    //----------- Delete  Warehouse --------------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Warehouse::class);

        DB::transaction(function () use ($id) {

            Warehouse::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            product_warehouse::where('warehouse_id', $id)->update([
                'deleted_at' => Carbon::now(),
            ]);

        }, 10);

        return response()->json(['success' => true]);
    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {

        $this->authorizeForUser($request->user('api'), 'delete', Warehouse::class);

        \DB::transaction(function () use ($request) {
            $selectedIds = $request->selectedIds;
            foreach ($selectedIds as $warehouse_id) {
                Warehouse::whereId($warehouse_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);

                product_warehouse::where('warehouse_id', $warehouse_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
            }

        }, 10);

        return response()->json(['success' => true]);
    }

}
