<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DisenoController extends Controller
{

    public function index()
    {
        return Inertia::render('Desing/Index');
    }
}
