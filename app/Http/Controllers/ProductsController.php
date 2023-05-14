<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Unit;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ProductsController extends Controller
{

    //------------ Get ALL Products --------------\\

    public function index(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Product::class);
        $products = Product::with('unit', 'category')
            ->where('deleted_at', '=', null)
            ->get();

        $data = collect();
        foreach ($products as $product) {
            $item['id'] = $product->id;
            $item['code'] = $product->code;
            $item['name'] = $product->name;
            $item['category'] = $product->category?->name;
//            $item['brand'] = $product['brand'] ? $product['brand']->name : 'N/D';
            $item['unit'] = $product->unit?->ShortName;
            $item['price'] = $product->price;

            $product_warehouse_data = product_warehouse::where('product_id', $product->id)
                ->where('deleted_at', '=', null)
                ->get();
            $total_qty = 0;
            foreach ($product_warehouse_data as $product_warehouse) {
                $total_qty += $product_warehouse->qty;
                $item['quantity'] = $total_qty;
            }

//            $firstimage = explode(',', $product->image);
//            $item['image'] = $firstimage[0];

            $data->add($item);
        }

        //get warehouses assigned to user
        $user_auth = auth()->user();
        if ($user_auth->is_all_warehouses) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }

        $categories = Category::where('deleted_at', null)->get(['id', 'name']);
        Inertia::share('titlePage', 'Productos');
        return Inertia::render('Products/Index_Products', [
            'warehouses' => $warehouses,
            'categories' => $categories,
            'products' => $data,
        ]);
    }

    //-------------- Store new  Product  ---------------\\

    public function store(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', Product::class);

        try {
            $this->validate($request, [
                'code' => 'required|unique:products',
                'code' => Rule::unique('products')->where(function ($query) {
                    return $query->where('deleted_at', '=', null);
                }),
                'name' => 'required',
                'price' => 'required',
                'category_id' => 'required',
                'cost' => 'required',
                'unit_id' => 'required',
            ], [
                'code.unique' => 'This code already used. Generate Now',
                'code.required' => 'This field is required',
            ]);

            DB::transaction(function () use ($request) {

                //-- Create New Product
                $Product = new Product;

                //-- Field Required
                $Product->name = $request['name'];
                $Product->code = $request['code'];
                $Product->price = $request['price'];
                $Product->category_id = $request['category_id'];
                $Product->TaxNet = $request['TaxNet'] ? $request['TaxNet'] : 0;
                $Product->tax_method = $request['tax_method'];
                $Product->note = $request['note'];
                $Product->cost = $request['cost'];
                $Product->unit_id = $request['unit_id'];
                $Product->unit_sale_id = $request['unit_sale_id'];
                $Product->unit_purchase_id = $request['unit_purchase_id'];
                $Product->stock_alert = $request['stock_alert'] ?? 0;
                $Product->is_variant = $request['is_variant'] == 'true' ? 1 : 0;
                $Product->not_selling = $request['not_selling'] == 'true' ? 1 : 0;

                $Product->save();

                // Store Variants Product
                if ($request['is_variant'] == 'true') {
                    $Product_variants_data = collect();
                    foreach ($request['variants'] as $variant) {
                        $Product_variants_data->add([
                            'product_id' => $Product->id,
                            'name' => $variant,
                        ]);
                    }
                    ProductVariant::created($Product_variants_data);
                }

                //--Store Product Warehouse
                $warehouses = Warehouse::where('deleted_at', null)->pluck('id')->toArray();
                if ($warehouses) {
                    $Product_variants = ProductVariant::where('product_id', $Product->id)
                        ->where('deleted_at', null)
                        ->get();
                    foreach ($warehouses as $warehouse) {
                        $product_warehouse = collect();
                        if ($request['is_variant'] == 'true') {
                            foreach ($Product_variants as $product_variant) {
                                $product_warehouse->add([
                                    'product_id' => $Product->id,
                                    'warehouse_id' => $warehouse,
                                    'product_variant_id' => $product_variant->id,
                                ]);
                            }
                        } else {
                            $product_warehouse->add([
                                'product_id' => $Product->id,
                                'warehouse_id' => $warehouse,
                            ]);
                        }
                    }
                    product_warehouse::created($product_warehouse);
                }

            }, 10);

            return response()->json(['success' => true]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'error',
                'errors' => $e->errors(),
            ], 422);
        }

    }

    //-------------- Update Product  ---------------\\
    //-----------------------------------------------\\

    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'code' => 'required|unique:products',
                'code' => Rule::unique('products')->ignore($id)->where(function ($query) {
                    return $query->where('deleted_at', '=', null);
                }),
                'name' => 'required',
                'price' => 'required',
                'category_id' => 'required',
                'cost' => 'required',
                'unit_id' => 'required',
            ], [
                'code.unique' => 'This code already used. Generate Now',
                'code.required' => 'This field is required',
            ]);

            DB::transaction(function () use ($request, $id) {

                $Product = Product::where('id', $id)
                    ->where('deleted_at', '=', null)
                    ->first();

                //-- Update Product
                $Product->name = $request['name'];
                $Product->code = $request['code'];
                $Product->price = $request['price'];
                $Product->category_id = $request['category_id'];
                $Product->TaxNet = $request['TaxNet'];
                $Product->tax_method = $request['tax_method'];
                $Product->note = $request['note'];
                $Product->cost = $request['cost'];
                $Product->unit_id = $request['unit_id'];
                $Product->unit_sale_id = $request['unit_sale_id'] ? $request['unit_sale_id'] : $request['unit_id'];
                $Product->unit_purchase_id = $request['unit_purchase_id'] ? $request['unit_purchase_id'] : $request['unit_id'];
                $Product->stock_alert = $request['stock_alert'];
                $Product->is_variant = $request['is_variant'] == 'true' ? 1 : 0;
                $Product->not_selling = $request['not_selling'] == 'true' ? 1 : 0;
                // Store Variants Product
                $oldVariants = ProductVariant::where('product_id', $id)
                    ->where('deleted_at', null)
                    ->get();

                $warehouses = Warehouse::where('deleted_at', null)
                    ->pluck('id')
                    ->toArray();


                if ($request['is_variant'] == 'true') {
                    $variants = collect($request['variants']);
                    if ($oldVariants->isNotEmpty()) {
                        $old_variants_delete = $oldVariants->whereNotIn('name', $variants);
                        foreach ($old_variants_delete as $variant_delete) {
                            $ProductVariant = ProductVariant::findOrFail($variant_delete->id);
                            $ProductVariant->deleted_at = Carbon::now();
                            $ProductVariant->save();
                            $ProductWarehouse = product_warehouse::where('product_variant_id', $variant_delete->id)
                                ->update(['deleted_at' => Carbon::now()]);
                        }

                        foreach ($variants as $variant) {
                            if (!$oldVariants->contains('name', $variant)) {

                                $ProductVariantDT = new ProductVariant();
                                //-- Field Required
                                $ProductVariantDT->product_id = $id;
                                $ProductVariantDT->name = $variant;
                                $ProductVariantDT->qty = 0.00;
                                $ProductVariantDT->save();

                                //--Store Product warehouse
                                if ($warehouses) {
                                    $product_warehouse = collect();
                                    foreach ($warehouses as $warehouse) {

                                        $product_warehouse->add([
                                            'product_id' => $id,
                                            'warehouse_id' => $warehouse,
                                            'product_variant_id' => $ProductVariantDT->id,
                                        ]);

                                    }
                                    product_warehouse::created($product_warehouse);
                                }
                            }
                        }

                    } else {
                        $ProducttWarehouse = product_warehouse::where('product_id', $id)
                            ->update([
                                'deleted_at' => Carbon::now(),
                            ]);

                        foreach ($variants as $variant) {
                            $ProductVarDT = new ProductVariant;

                            //-- Field Required
                            $ProductVarDT->product_id = $id;
                            $ProductVarDT->name = $variant;
                            $ProductVarDT->save();

                            //-- Store Product warehouse
                            if ($warehouses) {
                                $product_warehouse_DT = collect();
                                foreach ($warehouses as $warehouse) {

                                    $product_warehouse_DT->add([
                                        'product_id' => $id,
                                        'warehouse_id' => $warehouse,
                                        'product_variant_id' => $ProductVarDT->id,
                                    ]);
                                }
                                product_warehouse::created($product_warehouse_DT);
                            }
                        }

                    }
                } else {
                    if ($oldVariants->isNotEmpty()) {
                        $ids = $oldVariants->map(function ($item, $key) {
                            return $item->id;
                        })->toArray();
                        $ProducttWarehouse = ProductVariant::whereIn('id', $ids)
                            ->update([
                                'deleted_at' => Carbon::now(),
                            ]);

                        $ProducttWarehouse = product_warehouse::whereIn('product_variant_id', $ids)
                            ->update([
                                'deleted_at' => Carbon::now(),
                            ]);

                        if ($warehouses) {
                            $product_warehouse = collect();
                            foreach ($warehouses as $warehouse) {

                                $product_warehouse->add([
                                    'product_id' => $id,
                                    'warehouse_id' => $warehouse,
                                    'product_variant_id' => null,
                                ]);

                            }
                            product_warehouse::created($product_warehouse);
                        }
                    }
                }

                $Product->save();

            }, 10);

            return response()->json(['success' => true]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'error',
                'errors' => $e->errors(),
            ], 422);
        }

    }

    //-------------- Remove Product  ---------------\\
    //-----------------------------------------------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Product::class);

        DB::transaction(function () use ($id) {

            $Product = Product::findOrFail($id);
            $Product->deleted_at = Carbon::now();
            $Product->save();

            product_warehouse::where('product_id', $id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            ProductVariant::where('product_id', $id)->update([
                'deleted_at' => Carbon::now(),
            ]);

        }, 10);

        return response()->json(['success' => true]);

    }


    //--------------  Show Product Details ---------------\\

    public function Get_Products_Details(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'view', Product::class);

        $Product = Product::where('deleted_at', '=', null)->findOrFail($id);
        //get warehouses assigned to user
        $user_auth = auth()->user();
        if ($user_auth->is_all_warehouses) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }

        $item['id'] = $Product->id;
        $item['code'] = $Product->code;
        $item['name'] = $Product->name;
        $item['note'] = $Product->note;
        $item['category'] = $Product->category?->name;
//        $item['brand'] = $Product['brand'] ? $Product['brand']->name : 'N/D';
        $item['unit'] = $Product->unit?->ShortName;
        $item['price'] = $Product->price;
        $item['cost'] = $Product->cost;
        $item['stock_alert'] = $Product->stock_alert;
        $item['taxe'] = $Product->TaxNet;
        $item['tax_method'] = $Product->tax_method == '1' ? 'Exclusivo' : 'Inclusivo';

        if ($Product->is_variant) {
            $item['is_variant'] = 'yes';
            $productsVariants = ProductVariant::where('product_id', $id)
                ->where('deleted_at', null)
                ->get();
            foreach ($productsVariants as $variant) {
                $item['ProductVariant'][] = $variant->name;

                foreach ($warehouses as $warehouse) {
                    $product_warehouse = DB::table('product_warehouse')
                        ->where('product_id', $id)
                        ->where('deleted_at', '=', null)
                        ->where('warehouse_id', $warehouse->id)
                        ->where('product_variant_id', $variant->id)
                        ->select(DB::raw('SUM(product_warehouse.qty) AS sum'))
                        ->first();

                    $war_var['mag'] = $warehouse->name;
                    $war_var['variant'] = $variant->name;
                    $war_var['qty'] = $product_warehouse->sum;
                    $item['CountQTY_variants'][] = $war_var;
                }

            }
        } else {
            $item['is_variant'] = 'no';
            $item['CountQTY_variants'] = [];
        }

        foreach ($warehouses as $warehouse) {
            $product_warehouse_data = DB::table('product_warehouse')
                ->where('deleted_at', '=', null)
                ->where('product_id', $id)
                ->where('warehouse_id', $warehouse->id)
                ->select(DB::raw('SUM(product_warehouse.qty) AS sum'))
                ->first();

            $war['mag'] = $warehouse->name;
            $war['qty'] = $product_warehouse_data->sum;
            $item['CountQTY'][] = $war;
        }

//        if ($Product->image != '') {
//            foreach (explode(',', $Product->image) as $img) {
//                $item['images'][] = $img;
//            }
//        }

        Inertia::share('titlePage', 'Detalles del producto');
        return Inertia::render('Products/Detail_Product', [
            'product' => $item,
        ]);
    }

    //------------ Get products By Warehouse -----------------\\

    public function Products_by_Warehouse(request $request, $id)
    {
        $data = [];
        $product_warehouse_data = product_warehouse::with('warehouse', 'product', 'productVariant')
            ->where(function ($query) use ($request, $id) {
                return $query->where('warehouse_id', $id)
                    ->where('deleted_at', '=', null)
                    ->where(function ($query) use ($request) {
                        return $query->whereHas('product', function ($q) use ($request) {
                            if ($request->is_sale == '1') {
                                $q->where('not_selling', '=', 0);
                            }
                        });
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->stock == '1') {
                            return $query->where('qty', '>', 0);
                        }
                    });
            })->get();

        foreach ($product_warehouse_data as $product_warehouse) {

            if ($product_warehouse->product_variant_id) {
                $item['product_variant_id'] = $product_warehouse->product_variant_id;
                $item['code'] = $product_warehouse['productVariant']->name . '-' . $product_warehouse['product']->code;
                $item['Variant'] = $product_warehouse['productVariant']->name;
            } else {
                $item['product_variant_id'] = null;
                $item['Variant'] = null;
                $item['code'] = $product_warehouse['product']->code;
            }

            $item['id'] = $product_warehouse->product_id;
            $item['name'] = $product_warehouse['product']->name;
            $item['barcode'] = $product_warehouse['product']->code;
            $item['Type_barcode'] = $product_warehouse['product']->Type_barcode;
            $firstimage = explode(',', $product_warehouse['product']->image);
            $item['image'] = $firstimage[0];

            if ($product_warehouse['product']['unitSale']->operator == '/') {
                $item['qte_sale'] = $product_warehouse->qty * $product_warehouse['product']['unitSale']->operator_value;
                $price = $product_warehouse['product']->price / $product_warehouse['product']['unitSale']->operator_value;
            } else {
                $item['qte_sale'] = $product_warehouse->qty / $product_warehouse['product']['unitSale']->operator_value;
                $price = $product_warehouse['product']->price * $product_warehouse['product']['unitSale']->operator_value;
            }

            if ($product_warehouse['product']['unitPurchase']->operator == '/') {
                $item['qte_purchase'] = round($product_warehouse->qty * $product_warehouse['product']['unitPurchase']->operator_value, 5);
            } else {
                $item['qte_purchase'] = round($product_warehouse->qty / $product_warehouse['product']['unitPurchase']->operator_value, 5);
            }

            $item['qty'] = $product_warehouse->qty;
            $item['unitSale'] = $product_warehouse['product']['unitSale']->ShortName;
            $item['unitPurchase'] = $product_warehouse['product']['unitPurchase']->ShortName;

            if ($product_warehouse['product']->TaxNet !== 0.0) {
                //Exclusive
                if ($product_warehouse['product']->tax_method == '1') {
                    $tax_price = $price * $product_warehouse['product']->TaxNet / 100;
                    $item['Net_price'] = $price + $tax_price;
                    // Inxclusive
                } else {
                    $item['Net_price'] = $price;
                }
            } else {
                $item['Net_price'] = $price;
            }

            $data[] = $item;
        }

        return response()->json($data);
    }

    //------------ Get product By ID -----------------\\

    public function show($id)
    {

        $Product_data = Product::with('unit')
            ->where('id', $id)
            ->where('deleted_at', '=', null)
            ->first();

        $data = collect();
        $item['id'] = $Product_data['id'];
        $item['name'] = $Product_data['name'];
        $item['Type_barcode'] = $Product_data['Type_barcode'];
        $item['unit_id'] = $Product_data['unit']->id;
        $item['unit'] = $Product_data['unit']->ShortName;
        $item['purchase_unit_id'] = $Product_data['unitPurchase']->id;
        $item['unitPurchase'] = $Product_data['unitPurchase']->ShortName;
        $item['sale_unit_id'] = $Product_data['unitSale']->id;
        $item['unitSale'] = $Product_data['unitSale']->ShortName;
        $item['tax_method'] = $Product_data['tax_method'];
        $item['tax_percent'] = $Product_data['TaxNet'];
        $item['is_imei'] = $Product_data['is_imei'];
        $item['not_selling'] = $Product_data['not_selling'];

        if ($Product_data['unitSale']->operator == '/') {
            $price = $Product_data['price'] / $Product_data['unitSale']->operator_value;

        } else {
            $price = $Product_data['price'] * $Product_data['unitSale']->operator_value;
        }

        if ($Product_data['unitPurchase']->operator == '/') {
            $cost = $Product_data['cost'] / $Product_data['unitPurchase']->operator_value;
        } else {
            $cost = $Product_data['cost'] * $Product_data['unitPurchase']->operator_value;
        }

        $item['Unit_cost'] = $cost;
        $item['fix_cost'] = $Product_data['cost'];
        $item['Unit_price'] = $price;
        $item['fix_price'] = $Product_data['price'];

        if ($Product_data->TaxNet !== 0.0) {
            //Exclusive
            if ($Product_data['tax_method'] == '1') {
                $tax_price = $price * $Product_data['TaxNet'] / 100;
                $tax_cost = $cost * $Product_data['TaxNet'] / 100;

                $item['Total_cost'] = $cost + $tax_cost;
                $item['Total_price'] = $price + $tax_price;
                $item['Net_cost'] = $cost;
                $item['Net_price'] = $price;
                $item['tax_price'] = $tax_price;
                $item['tax_cost'] = $tax_cost;

                // Inxclusive
            } else {
                $item['Total_cost'] = $cost;
                $item['Total_price'] = $price;
                $item['Net_cost'] = $cost / (($Product_data['TaxNet'] / 100) + 1);
                $item['Net_price'] = $price / (($Product_data['TaxNet'] / 100) + 1);
                $item['tax_cost'] = $item['Total_cost'] - $item['Net_cost'];
                $item['tax_price'] = $item['Total_price'] - $item['Net_price'];
            }
        } else {
            $item['Total_cost'] = $cost;
            $item['Total_price'] = $price;
            $item['Net_cost'] = $cost;
            $item['Net_price'] = $price;
            $item['tax_price'] = 0;
            $item['tax_cost'] = 0;
        }

        $data->add($item);

        return response()->json($data[0]);
    }

    //--------------  Product Quantity Alerts ---------------\\

    public function Products_Alert(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'Stock_Alerts', Product::class);

        $product_warehouse_data = product_warehouse::with('warehouse', 'product', 'productVariant')
            ->join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->whereRaw('qty <= stock_alert')
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('warehouse'), function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse);
                });
            })->where('product_warehouse.deleted_at', null)->get();

        $data = [];

        if ($product_warehouse_data->isNotEmpty()) {

            foreach ($product_warehouse_data as $product_warehouse) {
                if ($product_warehouse->qty <= $product_warehouse['product']->stock_alert) {
                    if ($product_warehouse->product_variant_id !== null) {
                        $item['code'] = $product_warehouse['productVariant']->name . '-' . $product_warehouse['product']->code;
                    } else {
                        $item['code'] = $product_warehouse['product']->code;
                    }
                    $item['quantity'] = $product_warehouse->qty;
                    $item['name'] = $product_warehouse['product']->name;
                    $item['warehouse'] = $product_warehouse['warehouse']->name;
                    $item['stock_alert'] = $product_warehouse['product']->stock_alert;
                    $data[] = $item;
                }
            }
        }

        $perPage = $request->limit; // How many items do you want to display.
        $pageStart = Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $collection = collect($data);
        // Get only the items you need using array_slice
        $data_collection = $collection->slice($offSet, $perPage)->values();

        $products = new LengthAwarePaginator($data_collection, count($data), $perPage, Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));

        //get warehouses assigned to user
        $user_auth = auth()->user();
        if ($user_auth->is_all_warehouses) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }

        return response()->json([
            'products' => $products,
            'warehouses' => $warehouses,
        ]);
    }

    //---------------- Show Form Create Product ---------------\\

    public function create(Request $request)
    {

//        $this->authorizeForUser($request->user('api'), 'create', Product::class);

        $categories = Category::where('deleted_at', null)->get(['id', 'name'])
            ->map(function ($item, $key) {
                return ['value' => $item->id, 'title' => $item->name];
            });
        $units = Unit::where('deleted_at', null)->where('base_unit', null)->get()
            ->map(function ($item, $key) {
                return ['value' => $item->id, 'title' => $item->name];
            });
        Inertia::share('titlePage', 'Añadir producto');
        return Inertia::render('Products/Form_product', [
            'categories' => $categories,
            'units' => $units,
            'product' => null
        ]);

    }

//    //---------------- Show Elements Barcode ---------------\\
//
//    public function Get_element_barcode(Request $request)
//    {
////        $this->authorizeForUser($request->user('api'), 'barcode', Product::class);
//
//         //get warehouses assigned to user
//         $user_auth = auth()->user();
//         if($user_auth->is_all_warehouses){
//             $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
//         }else{
//             $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
//             $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
//         }
//
//        return response()->json(['warehouses' => $warehouses]);
//
//    }

    //---------------- Show Form Edit Product ---------------\\

    public function edit(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'update', Product::class);

        $Product = Product::where('deleted_at', '=', null)->findOrFail($id);

        $item['id'] = $Product->id;
        $item['code'] = $Product->code;
        $item['name'] = $Product->name;
        if ($Product->category_id) {
            if (Category::where('id', $Product->category_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $item['category_id'] = $Product->category_id;
            } else {
                $item['category_id'] = '';
            }
        } else {
            $item['category_id'] = '';
        }

        if ($Product->unit_id) {
            if (Unit::where('id', $Product->unit_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $item['unit_id'] = $Product->unit_id;
            } else {
                $item['unit_id'] = '';
            }

            if (Unit::where('id', $Product->unit_sale_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $item['unit_sale_id'] = $Product->unit_sale_id;
            } else {
                $item['unit_sale_id'] = '';
            }

            if (Unit::where('id', $Product->unit_purchase_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $item['unit_purchase_id'] = $Product->unit_purchase_id;
            } else {
                $item['unit_purchase_id'] = '';
            }

        } else {
            $item['unit_id'] = '';
        }

        $item['tax_method'] = $Product->tax_method;
        $item['price'] = $Product->price;
        $item['cost'] = $Product->cost;
        $item['stock_alert'] = $Product->stock_alert;
        $item['TaxNet'] = $Product->TaxNet;
        $item['note'] = $Product->note ? $Product->note : '';

        if ($Product->is_variant) {
            $item['is_variant'] = true;
            $productsVariants = ProductVariant::where('product_id', $id)
                ->where('deleted_at', null)
                ->get();
            foreach ($productsVariants as $variant) {
                $variant_item['id'] = $variant->id;
                $variant_item['text'] = $variant->name;
                $variant_item['qty'] = $variant->qty;
                $variant_item['product_id'] = $variant->product_id;
                $item['ProductVariant'][] = $variant_item;
            }
        } else {
            $item['is_variant'] = false;
            $item['ProductVariant'] = [];
        }

        $item['not_selling'] = $Product->not_selling ? true : false;

        $data = $item;
        $categories = Category::where('deleted_at', null)->get(['id', 'name'])
            ->map(function ($item, $key) {
                return ['value' => $item->id, 'title' => $item->name];
            });
        $units = Unit::where('deleted_at', null)->where('base_unit', null)->get()
            ->map(function ($item, $key) {
                return ['value' => $item->id, 'title' => $item->name];
            });

        $product_units = Unit::where('id', $Product->unit_id)
            ->orWhere('base_unit', $Product->unit_id)
            ->where('deleted_at', null)
            ->get()->map(function ($item, $key) {
                return ['value' => $item->id, 'title' => $item->name];
            });;


        Inertia::share('titlePage', 'Editar producto');
        return Inertia::render('Products/Form_product', [
            'product' => $data,
            'categories' => $categories,
            'units' => $units,
//            'units_sub' => $product_units,
        ]);

    }

    // import Products
    public function import_products(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $file_upload = $request->file('products');
                $ext = pathinfo($file_upload->getClientOriginalName(), PATHINFO_EXTENSION);
                if ($ext != 'csv') {
                    return response()->json([
                        'message' => 'must be in csv format',
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


                    $warehouses = Warehouse::where('deleted_at', null)->pluck('id')->toArray();

                    //-- Create New Product
                    foreach ($data as $key => $value) {
                        $category = Category::firstOrCreate(['name' => $value['category']]);
                        $category_id = $category->id;

                        $unit = Unit::where(['ShortName' => $value['unit']])
                            ->orWhere(['name' => $value['unit']])->first();
                        $unit_id = $unit->id;

                        $Product = new Product;
                        $Product->name = $value['name'] == '' ? null : $value['name'];
                        $Product->code = $value['code'] == '' ? '11111111' : $value['code'];
                        $Product->Type_barcode = 'CODE128';
                        $Product->price = $value['price'];
                        $Product->cost = $value['cost'];
                        $Product->category_id = $category_id;
                        $Product->TaxNet = 0;
                        $Product->tax_method = 1;
                        $Product->note = $value['note'] ? $value['note'] : '';
                        $Product->unit_id = $unit_id;
                        $Product->unit_sale_id = $unit_id;
                        $Product->unit_purchase_id = $unit_id;
                        $Product->stock_alert = $value['stock_alert'] ? $value['stock_alert'] : 0;
                        $Product->is_variant = 0;
                        $Product->image = 'no-image.png';
                        $Product->save();

                        if ($warehouses) {
                            foreach ($warehouses as $warehouse) {
                                $product_warehouse[] = [
                                    'product_id' => $Product->id,
                                    'warehouse_id' => $warehouse,
                                ];
                            }
                        }
                    }
                    if ($warehouses) {
                        product_warehouse::created($product_warehouse);
                    }
                }
            }, 10);
            return response()->json([
                'status' => true,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'error',
                'errors' => $e->errors(),
            ]);
        }

    }

    // Generate_random_code
    public function generate_random_code($value_code)
    {
        if ($value_code == '') {
            $gen_code = substr(number_format(time() * mt_rand(), 0, '', ''), 0, 8);
            $this->check_code_exist($gen_code);
        } else {
            $this->check_code_exist($value_code);
        }
    }


    // check_code_exist
    public function check_code_exist($code)
    {
        $check_code = Product::where('code', $code)->first();
        if ($check_code) {
            $this->generate_random_code($code);
        } else {
            return $code;
        }


    }


}
