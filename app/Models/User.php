<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Kutia\Larafirebase\Facades\Larafirebase;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'users';
    public static string $tables = 'users';
    protected $guarded = [];

    public function getNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    public static function getAll()
    {
        $users = DB::table(self::$tables)
            ->whereNull(self::$tables.'.deleted_at')
            ->select(self::$tables.'.*', Sucursal::$tables.'.nombre as nombreSucursal')
            ->leftJoin(Sucursal::$tables,Sucursal::$tables.'.id','=',self::$tables.'.sucursal');
        $users=$users->get();
        foreach ($users as $key=>$user) {
            $users[$key]->nombreRol = self::getRole($user->role);
        }
        return $users;
    }

    public static function getRole($int = null)
    {
        $roles = array(
            '0' => 'sadmin',
            '1' => 'admin',
            '2' => 'venta',
            '3' => 'operario',
            '4' => 'diseño',
            '5' => 'auxVenta'
        );
        if ($int===null) {
            return array(
                ['value' => '0', 'text' => 'sadmin'],
                ['value' => '1', 'text' => 'admin'],
                ['value' => '2', 'text' => 'venta'],
                ['value' => '3', 'text' => 'operario'],
                ['value' => '4', 'text' => 'diseño'],
                ['value' => '5', 'text' => 'auxVenta'],
            );
        }
        return $roles[$int];
    }

    public static function sendNotify($message, $title, $orden = 0)
    {
        $fcmTokens = DB::table(self::$tables)
            ->where('id', '!=', Auth::id())
            ->pluck('tokenpush')->toArray();
        Larafirebase::fromRaw([
            'registration_ids' => $fcmTokens,
            'data' => [
                'newOrden' => true,
                'orden' => $orden
            ],
            'notification' => [
                'title' => $title,
                'body' => $message
            ],
        ])->send();
    }
}
