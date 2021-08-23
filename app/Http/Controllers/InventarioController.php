<?php

namespace App\Http\Controllers;

use app\models\MovimientoStock;
use App\Models\ProductoStock;
use App\Models\TipoProductos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InventarioController extends Controller
{
    public function get(Request $request,bool $ingreso)
    {
        $productosAll = ProductoStock::getProducts(Auth::user()['sucursal']);
        $stocks = ProductoStock::getAll(Auth::user()['sucursal']);
        $movimientos = MovimientoStock::getAllTable($stocks->pluck('id')->toArray(),$ingreso);
        Inertia::render('Inventario/tabla',[
            'productos'=>$productosAll,
            'movimientos'=>$movimientos,
        ]);
    }


    public function post()
    {
    }

    public function delete()
    {
    }

}
