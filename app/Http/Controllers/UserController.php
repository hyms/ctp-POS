<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use App\utils\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{


    //------------- GET ALL USERS---------\\

    public function index(request $request)
    {
        Inertia::share('titlePage', 'Usuarios');
        return Inertia::render('People/Users');
    }

    public function getTable(request $request)
    {
        if(!helpers::checkPermission('users_view')){
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $Role = Auth::user()->roles()->first();

        $users = User::where(function ($query) {
            if(!helpers::checkPermission('record_view')){
                return $query->where('id', '=', Auth::user()->id);
            }
        });
        $users = $users->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'firstname' => $item->firstname,
                'lastname' => $item->lastname,
                'username' => $item->username,
                'password' => $item->password,
                'email' => $item->email,
                'phone' => $item->phone,
                'ci' => $item->ci,
                'statut' => $item->statut,
                'role' => $item->role . "",
                'is_all_warehouses' => $item->is_all_warehouses,
            ];
        });
        $warehouses = Warehouse::get()->pluck('name', 'id');
        $roles = Role::get()->pluck('name', 'id');
        return response()->json(['users' => $users, 'warehouses' => $warehouses, 'roles' => $roles]);
    }

    //------------- GET USER Auth ---------\\

    public function GetUserAuth(Request $request)
    {
        $helpers = new helpers();
        $user['avatar'] = Auth::user()->avatar;
        $user['username'] = Auth::user()->username;
        $user['currency'] = $helpers->Get_Currency();
        $user['logo'] = Setting::first()->logo;
        $user['default_language'] = Setting::first()->default_language;
        $user['footer'] = Setting::first()->footer;
        $user['developed_by'] = Setting::first()->developed_by;
        $permissions = Auth::user()->roles()->first()->permissions->pluck('name');
        $products_alerts = product_warehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->whereRaw('qte <= stock_alert')
            ->where('product_warehouse.deleted_at', null)
            ->count();

        return response()->json([
            'success' => true,
            'user' => $user,
            'notifs' => $products_alerts,
            'permissions' => $permissions,
        ]);
    }

    //------------- GET USER ROLES ---------\\

    public function GetUserRole(Request $request)
    {

        $roles = Auth::user()->roles()->with('permissions')->first();

        $data = [];
        if ($roles) {
            foreach ($roles->permissions as $permission) {
                $data[] = $permission->name;

            }
            return response()->json(['success' => true, 'data' => $data]);
        }

    }

    //------------- STORE NEW USER ---------\\

    public function store(Request $request)
    {
        if(!helpers::checkPermission('users_add')){
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $request->validate([
            'username' => 'required|unique:users',
        ], [
            'username.unique' => 'Este Usuario ya existe.',
        ]);
        DB::transaction(function () use ($request) {
            $User = new User;
            $User->firstname = $request->get('firstname');
            $User->lastname = $request->get('lastname');
            $User->username = $request->get('username');
            $User->email = $request->get('email');
            $User->phone = $request->get('phone');
            $User->password = Hash::make($request->get('password'));
            $User->role = $request->get('role');
            $User->ci = $request->get('ci') ?? 0;
            $User->is_all_warehouses = $request->get('is_all_warehouses') ? 1 : 0;
            $User->statut = 1;
            $User->save();

            $role = Role::find($request->get('role'));
            $User->assignRole($role);

            if ($User->is_all_warehouses == 0) {
                $User->assignedWarehouses()->sync($request->get('assigned_to'));
            }

        }, 10);

        return response()->json(['redirect' => '']);
    }

    //------------ function show -----------\\

    public function show($id)
    {
        //

    }

    public function edit(Request $request, $id)
    {
        $assigned_warehouses = UserWarehouse::where('user_id', $id)->pluck('warehouse_id')->toArray();
        $warehouses = Warehouse::where('deleted_at', '=', null)
            ->whereIn('id', $assigned_warehouses)
            ->get()
            ->map(function ($item) {
                return $item->id;
            });

        return response()->json([
            'assigned_warehouses' => $warehouses,
        ]);
    }

    //------------- UPDATE  USER ---------\\

    public function update(Request $request, $id)
    {
        if(!helpers::checkPermission('users_edit')){
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
        ], [
            'username.unique' => 'Este Usuario ya existe.',
        ]);

        DB::transaction(function () use ($id, $request) {
            $user = User::findOrFail($id);
            $current = $user->password;
            $pass = $user->password;
            if ($request->filled('NewPassword') && $request->NewPassword != "null") {
                $password_new = Hash::make($request->NewPassword);
                if ($password_new != $current) {
                    $pass = $password_new;
                }
            }

            User::whereId($id)->update([
                'firstname' => $request->get('firstname'),
                'lastname' => $request->get('lastname'),
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'ci' => $request->get('ci') ?? '',
                'password' => $pass,
                'statut' => $request->get('statut'),
                'is_all_warehouses' => ($request->get('is_all_warehouses') == "true") ? 1 : 0,
                'role' => $request->get('role'),

            ]);

            $role = Role::find($request->get('role'));
            $user->syncRoles($role);

            $user_saved = User::findOrFail($id);
            $user_saved->assignedWarehouses()->sync($request->get('assigned_to'));

        }, 10);

        return response()->json(['redirect' => '']);

    }


    //------------- UPDATE PROFILE ---------\\

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        $current = $user->password;
        $pass = $user->password;
        if ($request->filled('NewPassword') && $request->NewPassword != 'undefined') {
            $password_new = Hash::make($request->NewPassword);
            if ($password_new != $current) {
                $pass = $password_new;
            }
        }

        User::whereId($id)->update([
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
//            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'password' => $pass,
        ]);

        return response()->json(['redirect' => '']);

    }

    //----------- IsActivated (Update Statut User) -------\\

    public function IsActivated(request $request, $id)
    {
        User::whereId($id)->update([
            'statut' => $request->get('statut'),
        ]);
        return response()->json(['success' => true]);
    }

//------------- GET USER Auth ---------\\
    public function GetInfoProfile(Request $request)
    {
        $data = Auth::user();
        Inertia::share('titlePage', 'Perfil');
        return Inertia::render('Profile', ['user' => $data]);
    }

}
