<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\utils\helpers;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PermissionsController extends Controller
{

    //----------- GET ALL Roles --------------\\

    public function index(Request $request)
    {
        Inertia::share(['titlePage' => 'Permisos']);
        return Inertia::render('Settings/permissions/Permissions');
    }

    public function getTable(Request $request)
    {
        if(!helpers::checkPermission('permissions_view')){
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $roles = Role::get();
        return response()->json(['data' => $roles]);
    }

    //----------- Store new Role --------------\\

    public function store(Request $request)
    {
        if(!helpers::checkPermission('permissions_add')){
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $request->validate([
            'role.name' => 'required',
        ]);

        DB::transaction(function () use ($request) {

            //-- Create New Role
            $Role = new Role;
            $Role->name = $request['role']['name'];
            $Role->label = $request['role']['name'];
            $Role->status = 0;
            $Role->description = $request['role']['description'];
            $Role->save();

            $role = Role::findOrFail($Role->id);
            $permissions = collect($request->permissions);
            $permissions = $permissions->filter();
            $permissions = $permissions->map(function ($item, $key) {
                return $key;
            });
            $role->givePermissionTo($permissions);

        }, 10);

        return response()->json(['redirect' => '/roles']);
    }

    //------------ Show Form create Permissions -----------\\

    public function create()
    {
        $user = Auth::user();
        $user->can('permissions_add');
        Inertia::share(['titlePage' => 'Crear Permisos']);
        return Inertia::render('Settings/permissions/Form_permission');
    }

//------------- Show Form Edit Permissions -----------\\
    public function edit($id, Request $request)
    {

//        if ($id != '1') {
        $Role = Role::with('permissions')->where('deleted_at', '=', null)->findOrFail($id);
        $item = collect();
        $data = collect();
        if ($Role) {
            $item->put('id', $Role->id);
            $item->put('name', $Role->name);
            $item->put('description', $Role->description);
            foreach ($Role->permissions as $permission) {
                $data->put($permission->name, true);
            }
        }
        Inertia::share(['titlePage' => 'Editar Permisos']);
        return Inertia::render('Settings/permissions/Form_permission', [
            'permissionsItem' => $data,
            'roleItem' => $item,
        ]);
//        }

    }

    //----------- Update Role --------------\\

    public function update($id, Request $request)
    {
        if(!helpers::checkPermission('permissions_edit')){
            return response()->json(['message' => "No tiene permisos"], 406);
        }
        $request->validate([
            'role.name' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {

            Role::whereId($id)->update($request['role']);

            $role = Role::findOrFail($id);

            $permissions = collect($request->permissions);
            $permissions = $permissions->filter();
            $permissions = $permissions->map(function ($item, $key) {
                return $key;
            });
            $role->syncPermissions($permissions);

        }, 10);

        return response()->json(['redirect' => '/roles']);
    }

    //----------- Delete Role --------------\\

    public function destroy(Request $request, $id)
    {
        if(!helpers::checkPermission('permissions_delete')){
            return response()->json(['message' => "No tiene permisos"], 406);
        }

        Role::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['redirect' => '']);
    }

    //----------- GET ALL Roles without paginate --------------\\

    public function getRoleswithoutpaginate()
    {
        $roles = Role::where('deleted_at', null)->get(['id', 'name']);
        return response()->json($roles);
    }
}

