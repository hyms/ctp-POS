<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\User;
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
    public function getAll()
    {
        $users = User::all();
        $users->transform(function ($item, $key) {
            $item->nombreRol = User::getRole($item->role);
            $item->nombreSucursal=$item->Sucursales?->nombre;
            $item->enableView=($item->enable === 1) ? "Si" : "No" ;
            return $item;
        });
        $sucursales = Sucursal::getAll();
        $sucursales = $sucursales->map(function ($item){ return ['value'=>$item->id,'text'=>$item->nombre];});
        $roles = User::getRole();
        Inertia::share('titlePage', 'Usuarios');
        return Inertia::render('Usuarios', ['users' => $users, 'sucursales' => $sucursales, 'roles' => $roles]);
    }

    public function post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|min:5',
                'enable' => 'required',
                'role' => 'required',
                'sucursal' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => -1,
                    'errors' => $validator->errors()
                ]);
            }

            $usuarios = !empty($request['id'])
                ? User::find($request['id'])
                : new User();
            if (!empty($request['password']) && (strcmp($usuarios->password, $request['password']) !== 0)) {
                $usuarios->password = Hash::make($request['password']);
            }
            unset($request['password']);
            $usuarios->fill($request->all());
            $usuarios->save();
            return response()->json(["status" => 0, 'path' => 'users']);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json(["status" => -1,
                'error' => $error,], 500);
        }
    }

    public function borrar($id)
    {
        $user = User::find($id);
        $user->delete();
        return back()->withInput();
    }

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

    public function backup()
    {
        /*set_time_limit(300);
        $tables = DB::select('SHOW TABLES');
        $str = 'Tables_in_' . env('DB_DATABASE');

        $sqlScript = "";
        foreach ($tables as $table) {
            // Prepare SQLscript for creating table structure
            $row = DB::select("SHOW CREATE TABLE {$table->$str}");
            $keyCreateTable = "Create Table";
            $sqlScript .= "\n\n{$row[0]->$keyCreateTable};\n\n";

            $result = DB::table($table->$str)->get();
            foreach ($result as $row) {
                $sqlScript .= "INSERT INTO {$table->$str} VALUES(";
                $columnCount = count((array)$row);
                $indexColumn = 0;
                foreach ($row as $item) {

                    $sqlScript = isset($item)
                        ? $sqlScript . '"' . $item . '"'
                        : $sqlScript . '""';
                    if ($indexColumn < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                    $indexColumn++;
                }
                $sqlScript .= ");\n";
            }

            $sqlScript .= "\n";
        }

        if (!empty($sqlScript)) {
            // Save the SQL script to a backup file
            $backup_file_name = public_path() . '/' . (env('DB_DATABASE')) . '_backup.sql';
            //return $backup_file_name;
            $fileHandler = fopen($backup_file_name, 'w+');
            $number_of_lines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler);
        }*/
        Artisan::call('backup:run --only-db');
        $directory = env('APP_NAME', 'ctp-backup');
        $file_path= Storage::disk('public')->files($directory);
        $file_path = collect($file_path);
        $file_path = $file_path->last();
        return Storage::disk('public')->download($file_path,'respaldo.zip');
    }
}
