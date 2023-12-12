<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryExpenseController extends Controller
{

    //-------------- Get All Expense Categories ---------------\\

    public function index(Request $request)
    {
        Inertia::share('titlePage', 'Categoria de Gastos');
        return Inertia::render('Expense/Category_Expense',);
    }

    public function getTable(Request $request)
    {
        if (!helpers::checkPermission('expense_category_view')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $ExpenseCategory = ExpenseCategory::where('deleted_at', '=', null)->get();
        return response()->json([
            'Expenses_category' => $ExpenseCategory,
        ]);
    }

    //-------------- Store New Category ---------------\\

    public function store(Request $request)
    {
        if (!helpers::checkPermission('expense_category_add')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $request->validate([
            'name' => 'required|unique:expense_categories',
        ]);

        ExpenseCategory::create([
            'user_id' => Auth::user()->id,
            'description' => $request['description'] ?? "",
            'name' => $request['name'],
        ]);

        return response()->json(['success' => true], 200);
    }

    //------------ function show -----------\\

    public function show($id)
    {
        //

    }

    //-------------- Update Category ---------------\\

    public function update(Request $request, $id)
    {
        if (!helpers::checkPermission('expense_category_edit')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $ExpenseCategory = ExpenseCategory::findOrFail($id);

        $request->validate([
            'name' => ['required',Rule::unique('expense_categories')->ignore($id)],
        ]);

        $ExpenseCategory->update([
            'name' => $request['name'],
            'description' => $request['description'],
        ]);

        return response()->json(['success' => true], 200);

    }

    //-------------- Delete Category ---------------\\

    public function destroy(Request $request, $id)
    {
        if (!helpers::checkPermission('expense_category_delete')) {
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $ExpenseCategory = ExpenseCategory::find($id);
        $ExpenseCategory->update([
            'deleted_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true], 200);
    }

}
