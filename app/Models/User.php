<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\isNull;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'users';

    public function getNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenesTrabajo::class, 'user', 'id');
    }

    public function getOrdenCTPs0()
    {
        return $this->hasMany(OrdenesTrabajo::class, 'fk_idUserV', 'idUser');
    }

    public function getOrdenCTPs1()
    {
        return $this->hasMany(OrdenesTrabajo::class, 'fk_idUserD2', 'idUser');
    }

    public function getMovimientoCajas()
    {
        return $this->hasMany(MovimientoCaja::class, 'fk_idUser', 'idUser');
    }

    public function getMovimientoStocks()
    {
        return $this->hasMany(MovimientoStock::class, 'fk_idUser', 'idUser');
    }

    public function getNotas()
    {
        return $this->hasMany(Notas::class, 'fk_idUserCreador', 'idUser');
    }

    public function getRecibos()
    {
        return $this->hasMany(Recibo::class, 'fk_idUser', 'idUser');
    }

    public function getFkIdSucursal()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idSucursal');
    }

    public function getRole($int = null)
    {
        $roles = array(
            '0' => 'sadmin',
            '1' => 'admin',
            '2' => 'venta',
            '3' => 'operario',
            '4' => 'diseño',
            '5' => 'auxVenta'
        );
        if (isNull($int)) {
            return $roles;
        }
        return $roles[$int];
    }
}
