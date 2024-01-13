<?php

namespace App\Http\Controllers;

use App\Models\product_warehouse;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ScreenController extends Controller
{

    //------------ Get Sales--------------\\

    public function ListSales(request $request)
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
                })
                    ->where(function ($query) use ($request) {
                        if ($request->stock == '1') {
                            return $query->where('qty', '>', 0);
                        }
                    });
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

}
