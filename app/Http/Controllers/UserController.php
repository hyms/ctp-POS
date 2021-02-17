<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserController extends Controller
{
    public function getAll()
    {
        $users = User::getAll();
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->pluck('nombre','id');
        $roles = User::getRole();
        return Inertia::render('Usuarios/tabla', ['users' => $users,'sucursales'=>$sucursales,'roles'=>$roles]);
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|min:5',
                'enable' => 'required',
                'role' => 'required',
                'sucursal'=>'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }
            $usuarios = new User();
            if (!empty($request['id'])) {
                $usuarios = User::find($request['id']);
            }
            if (!empty($request['password']) && (strcmp($usuarios->password, $request['password']) !== 0)) {
                $usuarios->password = Hash::make($request['password']);
            }
            unset($request['password']);
            $usuarios->fill($request->all());
            $usuarios->save();
            return response()->json(["status" => 0, 'path' => 'users']);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $producto = User::find($id);
        $producto->delete();
        return back()->withInput();
    }
}