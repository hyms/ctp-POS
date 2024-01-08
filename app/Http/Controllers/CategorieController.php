<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategorieController extends Controller
{

    //-------------- Get All Categories ---------------\\

    public function index(Request $request)
    {
        $categories = Category::where('deleted_at', '=', null)->get();
        Inertia::share('titlePage', 'Categorias');
        return Inertia::render('Products/Categorie', [
            'categories' => $categories,
        ]);
    }

    public function getTable(Request $request)
    {
        if (!helpers::checkPermission('category')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        $categories = Category::where('deleted_at', '=', null)->get();
        return response()->json([
            'categories' => $categories,
        ]);
    }

    //-------------- Store New Category ---------------\\

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:categories',
        ]);

        Category::create([
            'code' => $request['code'],
            'name' => $request['name'],
        ]);
        return response()->json([]);
    }

    //------------ function show -----------\\

    public function show($id)
    {
    }

    //-------------- Update Category ---------------\\

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required',
            'code' => Rule::unique('categories')->ignore($id)->where(function ($query) {
                return $query->where('deleted_at', '=', null);
            }),
        ]);

        Category::whereId($id)->update([
            'code' => $request['code'],
            'name' => $request['name'],
        ]);
        return response()->json([]);
    }

    //-------------- Remove Category ---------------\\

    public function destroy(Request $request, $id)
    {
        Category::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json([]);
    }

}
