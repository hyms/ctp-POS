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
        Inertia::share(['title' => 'Permissions']);
        return Inertia::render('Settings/permissions/index');
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
        $this->authorizeForUser($request->user('api'), 'create', Role::class);

        try {
            request()->validate([
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
                $role->permissions()->detach();
                $permissions = $request->permissions;

                foreach ($permissions as $permission_slug) {
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

    //------------ function show -----------\\

    public function show($id)
    {
        //

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

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {

        $this->authorizeForUser($request->user('api'), 'delete', Role::class);

        $selectedIds = $request->selectedIds;
        foreach ($selectedIds as $role_id) {
            Role::whereId($role_id)->update([
                'deleted_at' => Carbon::now(),
            ]);
        }
        return response()->json(['success' => true]);
    }


    //----------- GET ALL Roles without paginate --------------\\

    public function getRoleswithoutpaginate()
    {
        $roles = Role::where('deleted_at', null)->get(['id', 'name']);
        return response()->json($roles);
    }

    //------------- Show Form Edit Permissions -----------\\

    public function edit(Request $request, $id)
    {

        $this->authorizeForUser($request->user('api'), 'update', Role::class);

        if ($id != '1') {
            $Role = Role::with('permissions')->where('deleted_at', '=', null)->findOrFail($id);
            if ($Role) {
                $item['name'] = $Role->name;
                $item['description'] = $Role->description;
                $data = [];
                if ($Role) {
                    foreach ($Role->permissions as $permission) {
                        $data[] = $permission->name;
                    }
                }
            }
            return response()->json([
                'permissions' => $data,
                'role' => $item,
            ]);

        } else {
            return response()->json([
                'success' => false,
            ], 401);
        }
    }

}
