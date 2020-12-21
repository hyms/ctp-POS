<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    function index()
    {
        return Inertia::render('Welcome');
    }
}
