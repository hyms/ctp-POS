<?php

namespace App\Http\Controllers;

use App\Models\OrdenesTrabajo;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrdenesController extends Controller
{
    public function getAll()
    {
        $ordenes = OrdenesTrabajo::getAll(Auth::user()['sucursal'], Auth::user()['id']);
        $productos = Producto::getAll(Auth::user()['sucursal']);
        return Inertia::render('Ordenes/tabla', [
            'ordenes' => $ordenes,
            'productos' => $productos
        ]);
    }
}
