<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;
    protected $table = 'user';
    protected $primaryKey = 'idUser';

    public function getNameAttribute()
    {
        return $this->nombre.' '.$this->apellido;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenCTP::class, 'fk_idUserD', 'idUser');
    }

    public function getOrdenCTPs0()
    {
        return $this->hasMany(OrdenCTP::class, 'fk_idUserV', 'idUser');
    }

    public function getOrdenCTPs1()
    {
        return $this->hasMany(OrdenCTP::class, 'fk_idUserD2', 'idUser');
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

    public function getNotas0()
    {
        return $this->hasMany(Notas::class, 'fk_idUserVisto', 'idUser');
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
            '1' => 'sadmin',
            '2' => 'admin',
            '3' => 'venta',
            '4' => 'operario',
            '5' => 'diseño',
            '6' => 'auxVenta'
        );
        if (is_null($int))
            return $roles;
        return $roles[$int];
    }
}
