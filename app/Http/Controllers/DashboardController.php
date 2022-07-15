<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    function index()
    {
        $sucursal = Sucursal::find(Auth::user()->sucursal);
        $user = Auth::user()->nombre.' '.Auth::user()->apellido;
        return Inertia::render('Welcome',['usernames'=>$user,'sucursal'=>$sucursal]);
    }
}
