<?php

namespace App\Http\Controllers;

use App\Models\MovimientoStock;
use App\Models\ProductoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InventarioController extends Controller
{
    public function get(Request $request, bool $ingreso)
    {
        $productosAll = ProductoStock::getProducts(Auth::user()['sucursal']);
        $stocks = ProductoStock::getAll(Auth::user()['sucursal']);
        $fields = [
            'producto',
//        'stockOrigen',
//        'stockDestino',
            'observaciones',
            'cantidad',
            [
                'key' => 'created_at',
                'label' => 'Fecha'
            ],
        ];
        $movimientos = MovimientoStock::getAllTable($stocks->pluck('id')->toArray(), $ingreso);
        return Inertia::render('Inventario/tabla', [
            'productos' => $productosAll,
            'movimientos' => $movimientos,
            'fields' => $fields,
            'stocks' => $stocks,
        ]);
    }

    public function getIngreso(Request $request)
    {
        return $this->get($request, true);
    }

    public function getEgreso(Request $request)
    {
        return $this->get($request, false);
    }


    public function post()
    {
    }

    public function delete()
    {
    }

}
