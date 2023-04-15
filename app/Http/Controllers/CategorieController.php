<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategorieController extends Controller
{

    //-------------- Get All Categories ---------------\\

    public function index(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Category::class);

        $categories = Category::where('deleted_at', '=', null)->get();
        Inertia::share('titlePage', 'Categorias');
        return Inertia::render('Products/Categorie',[
            'categories' => $categories,
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

        Category::create([
            'code' => $request['code'],
            'name' => $request['name'],
        ]);
        return response()->json(['success' => true]);
    }

     //------------ function show -----------\\

    public function show($id){
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

        Category::whereId($id)->update([
            'code' => $request['code'],
            'name' => $request['name'],
        ]);
        return response()->json(['success' => true]);

    }

    //-------------- Remove Category ---------------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Category::class);

        Category::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => true]);
    }

}
