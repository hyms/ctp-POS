<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes,HasRoles;

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
        'role_id' => 'integer',
        'statut' => 'integer',
        'is_all_warehouses' => 'integer',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }


    public static function getRole(int $id = null): array|string
    {
        $roles = collect([
            ['value' => 0, 'text' => 'sadmin'],
            ['value' => 1, 'text' => 'admin'],
            ['value' => 2, 'text' => 'venta'],
            ['value' => 4, 'text' => 'operario']
        ]);

        if ($id === null) {
            return $roles->all();
        }

        $first = $roles->firstWhere('value', '=', $id);
        return $first['text']??'';
    }

    public function assignedWarehouses()
    {
        return $this->belongsToMany('App\Models\Warehouse');
    }
}
