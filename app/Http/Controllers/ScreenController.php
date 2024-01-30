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
        $Sales = Sale::with('facture', 'client', 'warehouse', 'user', 'userpos', 'sales_type')
            ->where('deleted_at', '=', null)
            ->whereIn('warehouse_id', $warehouses->pluck('id'))
            ->orderBy('updated_at', 'desc');
        $Sales = $Sales->whereBetween('date', [Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d')]);
        $Sales = $Sales->whereIn('statut', ['completed']);
        $Sales = $Sales->limit(100);
        $Sales = $Sales->get();
        $data = collect();
        foreach ($Sales as $Sale) {
            $item['id'] = $Sale['id'];
            $item['date'] = $Sale['date'];
            $item['Ref'] = $Sale['Ref'];
            $item['statut'] = $Sale['statut'];
            $item['warehouse_name'] = $Sale->warehouse?->name;
            $item['client_id'] = $Sale->client?->id;
            $item['client_name'] = $Sale->client?->company_name;
            $item['updated_at'] = Carbon::parse($Sale->updated_at)->format('Y-m-d');
            $data->add($item);
        }

        return response()->json([
            'sales' => $data,
            'warehouses' => $warehouses,
        ]);
    }

}
