<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'recibo';

    public function getMovimientoCaja()
    {
        return $this->hasOne(MovimientoCaja::class, 'idMovimientoCaja', 'fk_idMovimientoCaja');
    }

    public function getSucursal()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idSucursal');
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'idUser', 'fk_idUser');
    }
}
