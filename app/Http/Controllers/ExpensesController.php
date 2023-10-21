<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Role;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExpensesController extends Controller
{

    //-------------- Show All  Expenses -----------\\

    public function index(request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'view', Expense::class);


//        $helpers = new helpers();
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $filter = collect($request->get('filter'));
//get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());
        // Check If User Has Permission View  All Records
        $Expenses = Expense::with('expense_category', 'warehouse')
            ->where('deleted_at', '=', null)
            ->whereIn('warehouse_id', $warehouses->pluck('id'));

        if ($filter->count() > 0) {
            $filterData = false;
            if (!empty($filter->get('start_date')) && $filter->get('end_date')) {
                $Expenses = $Expenses->whereBetween('date', [Carbon::parse($filter->get('start_date')), Carbon::parse($filter->get('end_date'))]);
                $filterData = true;
            }
            if (!empty($filter->get('ref'))) {
                $Expenses = $Expenses->where('Ref', 'like', "%{$filter->get('ref')}%");
                $filterData = true;
            }
            if (!empty($filter->get('warehouse'))) {
                $Expenses = $Expenses->where('warehouse_id', '=', $filter->get('warehouse'));
                $filterData = true;
            }
            if (!empty($filter->get('category'))) {
                $Expenses = $Expenses->where('expense_category_id', '=', $filter->get('category'));
                $filterData = true;
            }
            if (!$filterData) {
                $Expenses = $Expenses->limit(1000);
            }
        } else {
            $Expenses = $Expenses->limit(500);
        }
        $Expenses = $Expenses->orderByDesc('updated_at')->get();
        $data = collect();
        foreach ($Expenses as $Expense) {

            $item['id'] = $Expense->id;
            $item['date'] = $Expense->date;
            $item['Ref'] = $Expense->Ref;
            $item['details'] = $Expense->details;
            $item['amount'] = $Expense->amount;
            $item['warehouse_name'] = $Expense['warehouse']->name;
            $item['category_name'] = $Expense['expense_category']->name;
            $item['updated_at'] = Carbon::parse($Expense->updated_at)->format('Y-m-d');
            $data->add($item);
        }

        $Expenses_category = ExpenseCategory::where('deleted_at', '=', null)->get(['id', 'name']);


        Inertia::share('titlePage', 'Gastos');
        return Inertia::render('Expense/Index_Expense', [
            'expenses' => $data,
            'Expenses_category' => $Expenses_category,
            'warehouses' => $warehouses,
            'filter_form' => $filter
        ]);

    }

    //-------------- Store New Expense -----------\\

    public function store(Request $request)
    {
//        $this->authorizeForUser($request->user('api'), 'create', Expense::class);

        request()->validate([
            'expense.date' => 'required',
            'expense.warehouse_id' => 'required',
            'expense.category_id' => 'required',
            'expense.details' => 'required',
            'expense.amount' => 'required',
        ]);

        Expense::create([
            'user_id' => Auth::user()->id,
            'date' => $request['expense']['date'],
            'Ref' => $this->getNumberOrder(),
            'warehouse_id' => $request['expense']['warehouse_id'],
            'expense_category_id' => $request['expense']['category_id'],
            'details' => $request['expense']['details'],
            'amount' => $request['expense']['amount'],
        ]);

        return response()->json(['success' => true]);
    }

    //------------ function show -----------\\

    public function show($id)
    {
        //

    }

    //-------------- Update  Expense -----------\\

    public function update(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'update', Expense::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $expense = Expense::findOrFail($id);

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === expense->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $expense);
//        }

        request()->validate([
            'expense.date' => 'required',
            'expense.warehouse_id' => 'required',
            'expense.category_id' => 'required',
            'expense.details' => 'required',
            'expense.amount' => 'required',
        ]);

        $expense->update([
            'date' => $request['expense']['date'],
            'warehouse_id' => $request['expense']['warehouse_id'],
            'expense_category_id' => $request['expense']['category_id'],
            'details' => $request['expense']['details'],
            'amount' => $request['expense']['amount'],
        ]);

        return response()->json(['success' => true]);
    }

    //-------------- Delete Expense -----------\\

    public function destroy(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'delete', Expense::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $expense = Expense::findOrFail($id);

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//            // Check If User->id === expense->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $expense);
//        }

        $expense->update([
            'deleted_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true]);
    }

//    //-------------- Delete by selection  ---------------\\
//
//    public function delete_by_selection(Request $request)
//    {
//        $this->authorizeForUser($request->user('api'), 'delete', Expense::class);
//        $selectedIds = $request->selectedIds;
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
//
//        foreach ($selectedIds as $expense_id) {
//            $expense = Expense::findOrFail($expense_id);
//
//            // Check If User Has Permission view All Records
//            if (!$view_records) {
//                // Check If User->id === expense->id
//                $this->authorizeForUser($request->user('api'), 'check_record', $expense);
//            }
//            $expense->update([
//                'deleted_at' => Carbon::now(),
//            ]);
//        }
//        return response()->json(['success' => true]);
//    }

    //--------------- Reference Number of Expense ----------------\\

    public function getNumberOrder()
    {

        $last = DB::table('expenses')->latest('id')->first();
        return helpers::get_code($last?->Ref, "GA");
    }


    //---------------- Show Form Create Expense ---------------\\

    public function create(Request $request)
    {

//        $this->authorizeForUser($request->user('api'), 'create', Expense::class);

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());

        $Expenses_category = ExpenseCategory::where('deleted_at', '=', null)->get(['id', 'name']);

        Inertia::share('titlePage', 'Nuevo Gasto');
        return Inertia::render('Expense/Form_Expense', [
            'expense_category' => $Expenses_category,
            'warehouses' => $warehouses,
        ]);
    }

    //------------- Show Form Edit Expense -----------\\

    public function edit(Request $request, $id)
    {

//        $this->authorizeForUser($request->user('api'), 'update', Expense::class);
//        $role = Auth::user()->roles()->first();
//        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $Expense = Expense::where('deleted_at', '=', null)->findOrFail($id);

        // Check If User Has Permission view All Records
//        if (!$view_records) {
//             Check If User->id === Expense->id
//            $this->authorizeForUser($request->user('api'), 'check_record', $Expense);
//        }
        $data['warehouse_id'] = '';
        if ($Expense->warehouse_id) {
            if (Warehouse::where('id', $Expense->warehouse_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $data['warehouse_id'] = $Expense->warehouse_id;
            }
        }
        $data['category_id'] = '';
        if ($Expense->expense_category_id) {
            if (ExpenseCategory::where('id', $Expense->expense_category_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $data['category_id'] = $Expense->expense_category_id;
            }
        }

        $data['id'] = $Expense->id;
        $data['date'] = $Expense->date;
        $data['amount'] = $Expense->amount;
        $data['details'] = $Expense->details;

        //get warehouses assigned to user
        $warehouses = helpers::getWarehouses(auth()->user());

        $Expenses_category = ExpenseCategory::where('deleted_at', '=', null)->get(['id', 'name']);

        Inertia::share('titlePage', 'Modificar Gasto');
        return Inertia::render('Expense/Form_Expense', [
            'expense' => $data,
            'expense_category' => $Expenses_category,
            'warehouses' => $warehouses,
        ]);
    }

}
