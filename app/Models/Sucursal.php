<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected static $tables = 'sucursales';
    protected $guarded = [];

    public static function getAll(bool $isAdm=False)
    {
        $sucursales =DB::table(self::$tables);
        if(!$isAdm)
        {$sucursales = $sucursales->where('enable','=','1');}
        $sucursales = $sucursales
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        return $sucursales->get();
    }

    public function getOrdenCTPs()
    {
        return $this->hasMany(OrdenesTrabajo::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getCajas()
    {
        return $this->hasMany(Cajas::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getClientes()
    {
        return $this->hasMany(Cliente::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getNotas()
    {
        return $this->hasMany(Notas::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getProductoStocks()
    {
        return $this->hasMany(ProductoStock::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getRecibos()
    {
        return $this->hasMany(Recibo::class, 'fk_idSucursal', 'idSucursal');
    }

    public function getPadre()
    {
        return $this->hasOne(Sucursal::class, 'idSucursal', 'fk_idParent');
    }

    public function getSucursals()
    {
        return $this->hasMany(Sucursal::class, 'fk_idParent', 'idSucursal');
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, 'fk_idSucursal', 'idSucursal');
    }
}
