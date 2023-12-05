<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
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
        $user = Auth::user();
        $user->can('permissions_view');
        $roles = Role::get();
        return response()->json(['data' => $roles]);
    }

    //----------- Store new Role --------------\\

    public function store(Request $request)
    {

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
            $permissions = $request->permissions;
            $role->syncPermissions($permissions);

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
        $user = Auth::user();
        $user->can('permissions_edit');

        if ($id != '1') {
            $Role = Role::with('permissions')->where('deleted_at', '=', null)->findOrFail($id);
            $item = collect();
            $data = collect();
            if ($Role) {
                $item->put('name', $Role->name);
                $item->put('description', $Role->description);
                foreach ($Role->permissions as $permission) {
                    $data->put($permission->name, true);
                }
            }
            Inertia::share(['titlePage' => 'Crear Permisos']);
            return Inertia::render('Settings/permissions/Form_permission', [
                'permissionsItem' => $data,
                'roleItem' => $item,
            ]);
        }

    }

    //----------- Update Role --------------\\

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Role::class);

        try {
            request()->validate([
                'role.name' => 'required',
            ]);

            DB::transaction(function () use ($request, $id) {

                Role::whereId($id)->update($request['role']);

                $role = Role::findOrFail($id);
                $role->permissions()->detach();
                $permissions = $request->permissions;

                foreach ($permissions as $permission_slug) {

                    //get the permission object by name
                    $perm = Permission::firstOrCreate(['name' => $permission_slug]);
                    $data[] = $perm->id;
                }

                $role->permissions()->attach($data);

            }, 10);

            return response()->json(['success' => true]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'msg' => 'error',
                'errors' => $e->errors(),
            ], 422);
        }

    }

    //----------- Delete Role --------------\\

    public function destroy(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Role::class);

        Role::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => true]);
    }

    //----------- GET ALL Roles without paginate --------------\\

    public function getRoleswithoutpaginate()
    {
        $roles = Role::where('deleted_at', null)->get(['id', 'name']);
        return response()->json($roles);
    }
}

