<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReciboController extends Controller
{
    public function getAll()
    {
        $recibos = Recibo::getAll(Auth::user()['sucursal']);
        $sucursales = Sucursal::getAll();
        return Inertia::render('Recibos/tabla', [
            'recibos' => $recibos,
        ]);
    }
}
