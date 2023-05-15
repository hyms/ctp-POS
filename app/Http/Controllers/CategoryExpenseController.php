<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryExpenseController extends Controller
{

    //-------------- Get All Expense Categories ---------------\\

    public function index(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', ExpenseCategory::class);
        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $ExpenseCategory = ExpenseCategory::where('deleted_at', '=', null)->get();

        Inertia::share('titlePage', 'Gastos');
        return Inertia::render('Expense/Category_Expense',[
            'Expenses_category' => $ExpenseCategory,
        ]);
    }

    //-------------- Store New Category ---------------\\

    public function store(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', ExpenseCategory::class);

        request()->validate([
            'name' => 'required',
        ]);

        ExpenseCategory::created([
            'user_id' => Auth::user()->id,
            'description' => $request['description'],
            'name' => $request['name'],
        ]);

        return response()->json(['success' => true], 200);
    }

    //------------ function show -----------\\

    public function show($id){
    //
    
    }

    //-------------- Update Category ---------------\\

    public function update(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', ExpenseCategory::class);
        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $ExpenseCategory = ExpenseCategory::findOrFail($id);

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === ExpenseCategory->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $ExpenseCategory);
//        }

        request()->validate([
            'name' => 'required',
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
//        $this->authorizeForUser($request->user('api'), 'delete', ExpenseCategory::class);
        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $ExpenseCategory = ExpenseCategory::findOrFail($id);

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === ExpenseCategory->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $ExpenseCategory);
//        }
        $ExpenseCategory->update([
            'deleted_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true], 200);
    }

}
