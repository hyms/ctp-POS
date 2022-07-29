<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $table = 'users';
    public static string $tables = 'users';
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getNameAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public static function getAll(): Collection
    {
        $users = DB::table(self::$tables)
            ->whereNull(self::$tables . '.deleted_at')
            ->select(self::$tables . '.*', Sucursal::$tables . '.nombre as nombreSucursal')
            ->leftJoin(Sucursal::$tables, Sucursal::$tables . '.id', '=', self::$tables . '.sucursal');
        $users = $users->get();
        $users->transform(function ($item, $key) {
            $item->nombreRol = self::getRole($item->role);
            $item->enableView=($item->enable === 1) ? "Si" : "No" ;
            return $item;
        });
        return $users;
    }

    public static function getRole(int $id = null): array|string
    {
        $roles = collect([
            ['value' => 0, 'text' => 'sadmin'],
            ['value' => 1, 'text' => 'admin'],
            ['value' => 2, 'text' => 'venta'],
            ['value' => 3, 'text' => 'operario']
        ]);

        if ($id === null) {
            return $roles->all();
        }

        $first = $roles->firstWhere('value', '=', $id);
        return $first['text'];
    }
}
