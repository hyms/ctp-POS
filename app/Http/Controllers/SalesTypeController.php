<?php

namespace App\Http\Controllers;

use App\Models\SalesType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesTypeController extends Controller
{

    //-------------- Get All Categories ---------------\\

    public function index(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Category::class);

        $salesType = SalesType::where('deleted_at', '=', null)->get();
        Inertia::share('titlePage', 'Tipos de Venta');
        return Inertia::render('Settings/Sales_Type', [
            'sales_types' => $salesType,
        ]);
    }

    //-------------- Store New Category ---------------\\

    public function store(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', Category::class);

        request()->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        SalesType::create([
            'code' => $request['code'],
            'name' => $request['name'],
        ]);
        return response()->json(['redirect' => '']);
    }

    //------------ function show -----------\\

    public function show($id)
    {
        //

    }

    //-------------- Update Category ---------------\\

    public function update(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', Category::class);

        request()->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        SalesType::whereId($id)->update([
            'code' => $request['code'],
            'name' => $request['name'],
        ]);
        return response()->json(['redirect' => '']);

    }

    //-------------- Remove Category ---------------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Category::class);

        SalesType::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['redirect' => '']);
    }

}
