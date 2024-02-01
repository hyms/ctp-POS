<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScreenController extends Controller
{

    //------------ Get Sales--------------\\

    public function ListSales(request $request)
    {
        if (!helpers::checkPermission('screen_view')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $warehouses = helpers::getWarehouses(auth()->user());
        $Sales = Sale::with('facture', 'client', 'warehouse', 'user', 'userpos', 'sales_type','details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->whereIn('warehouse_id', $warehouses->pluck('id'))
            ->orderBy('updated_at', 'desc');
        $Sales = $Sales->whereBetween('date', [Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d')]);
        $Sales = $Sales->limit(200);
        $Sales = $Sales->whereIn('statut', ['pending', 'completed'])->get();
        $data = $Sales->map(function ($item, $key) {
            $details = "";
            foreach ($item->details as $detail) {
                $details .= " | {$detail->quantity}({$detail['product']['name']})";
            }
            return [
                'id' => $item['id'],
                'date' => $item['date'],
                'Ref' => $item['Ref'],
                'statut' => $item['statut'],
                'warehouse_name' => $item->warehouse?->name,
                'client_id' => $item->client?->id,
                'client_name' => $item->client?->company_name,
                'detail' => $details,
                'updated_at' => Carbon::parse($item->updated_at)->format('Y-m-d'),
            ];
        });
        $data1 = $data->filter(function ($item) {
            return $item['statut'] == 'completed';
        });
        $data2 = $data->filter(function ($item) {
            return $item['statut'] == 'pending';
        });

        return response()->json([
            'sales' => $data1->values(),
            'sales_p' => $data2->values(),
            'warehouses' => $warehouses,
        ]);
    }

}
