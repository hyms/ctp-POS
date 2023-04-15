<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\role_user;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserController extends Controller
{

    public function savePush(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $user = User::find(Auth::id());
            $user->tokenpush = $request['token'];
            $user->save();
            return response()->json([
                'status' => 0,
                'errors' => []
            ]);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    //------------- GET ALL USERS---------\\

    public function index(request $request)
    {
        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->exists();
//        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        $users = User::where(function ($query) use ($ShowRecord) {
            if (!$ShowRecord) {
                return $query->where('id', '=', Auth::user()->id);
            }
        });
        $users = $users->get();
        $warehouses = Warehouse::get(['id', 'name'])->map(function ($item, $key) {
            return ['value' => $item->id, 'title' => $item->name];
        });
        $roles = Role::get(['id', 'name'])->map(function ($item, $key) {
            return ['value' => $item->id, 'title' => $item->name];
        });
        Inertia::share('titlePage', 'Usuarios');
        return Inertia::render('People/Users',
            ['users' => $users, 'warehouses' => $warehouses, 'roles' => $roles]);
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
//        $this->authorizeForUser($request->user('api'), 'create', User::class);
//        $this->validate($request, [
//            'email' => 'required|unique:users',
//        ], [
//            'email.unique' => 'This Email already taken.',
//        ]);
        DB::transaction(function () use ($request) {
            $User = new User;
            $User->firstname = $request['firstname'];
            $User->lastname = $request['lastname'];
            $User->username = $request['username'];
            $User->email = $request['email'];
            $User->phone = $request['phone'];
            $User->password = Hash::make($request['password']);
            $User->role = $request['role'];
            $User->ci = $request['ci'];
            $User->is_all_warehouses = $request['is_all_warehouses'] ? 1 : 0;
            $User->statut = 1;
            $User->save();

            $role_user = new role_user;
            $role_user->user_id = $User->id;
            $role_user->role_id = $request['role'];
            $role_user->save();

            if (!$User->is_all_warehouses) {
                $User->assignedWarehouses()->sync($request['assigned_to']);
            }

        }, 10);

        return response()->json(['success' => true]);
    }

    //------------ function show -----------\\

    public function show($id)
    {
        //

    }

    public function edit(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', User::class);

        $assigned_warehouses = UserWarehouse::where('user_id', $id)->pluck('warehouse_id')->toArray();
        $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $assigned_warehouses)->pluck('id')->toArray();

        return response()->json([
            'assigned_warehouses' => $warehouses,
        ]);
    }

    //------------- UPDATE  USER ---------\\

    public function update(Request $request, $id)
    {
//        $this->authorizeForUser($request->user('api'), 'update', User::class);

//        $this->validate($request, [
//            'email' => 'required|email|unique:users',
//            'email' => Rule::unique('users')->ignore($id),
//        ], [
//            'email.unique' => 'This Email already taken.',
//        ]);

        DB::transaction(function () use ($id, $request) {
            $user = User::findOrFail($id);
            $current = $user->password;

            if (!empty($request->NewPassword) || $request->NewPassword != "null") {
                if ($request->NewPassword != $current) {
                    $pass = Hash::make($request->NewPassword);
                } else {
                    $pass = $user->password;
                }

            } else {
                $pass = $user->password;
            }

            User::whereId($id)->update([
                'firstname' => $request['firstname'],
                'lastname' => $request['lastname'],
                'username' => $request['username'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'ci' => $request['ci'] ?? '',
                'password' => $pass,
                'statut' => $request['statut'],
                'is_all_warehouses' => ($request['is_all_warehouses'] == "true") ? 1 : 0,
                'role' => $request['role'],

            ]);

            role_user::where('user_id', $id)->update([
                'user_id' => $id,
                'role_id' => $request['role'],
            ]);

            $user_saved = User::findOrFail($id);
            $user_saved->assignedWarehouses()->sync($request['assigned_to']);

        }, 10);

        return response()->json(['success' => true]);

    }


    //------------- UPDATE PROFILE ---------\\

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        $current = $user->password;

        if (!empty($request->NewPassword) && $request->NewPassword != 'undefined') {
            if ($request->NewPassword != $current) {
                $pass = Hash::make($request->NewPassword);
            } else {
                $pass = $user->password;
            }

        } else {
            $pass = $user->password;
        }

        User::whereId($id)->update([
            'firstname' => $request['firstname'],
            'lastname' => $request['lastname'],
//            'username' => $request['username'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'password' => $pass,
        ]);

        return response()->json(['user' => $request['username']]);

    }

    //----------- IsActivated (Update Statut User) -------\\

    public function IsActivated(request $request, $id)
    {
        $user = Auth::user();
        if ($request['id'] !== $user->id) {
            User::whereId($id)->update([
                'statut' => $request['statut'],
            ]);
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function GetPermissions()
    {
        $roles = Auth::user()->roles()->with('permissions')->first();
        $data = [];
        if ($roles) {
            foreach ($roles->permissions as $permission) {
                $item[$permission->name]['slug'] = $permission->name;
                $item[$permission->name]['id'] = $permission->id;

            }
            $data[] = $item;
        }
        return $data[0];

    }

    //------------- GET USER Auth ---------\\

    public function GetInfoProfile(Request $request)
    {
        $data = Auth::user();
        Inertia::share('titlePage', 'Perfil');
        return Inertia::render('Profile', ['user' => $data]);
    }

}
