<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $table = 'clientes';
    public static $tables = 'clientes';
    protected $guarded = [];
    use SoftDeletes;

    public static function getAll(int $sucursal = null)
    {
        $clientes = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $clientes = $clientes->where('sucursal', '=', $sucursal);
        }
        $clientes = $clientes
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
        return $clientes->get();
    }

    public static function newCliente(string $responsable, string $telefono,int $sucursal)
    {
        $cliente = DB::table(self::$tables);
        if(!empty($responsable) && !empty($telefono))
        {
            return $cliente->insertGetId([
                'nombreResponsable' => $responsable,
                'telefono' => $telefono,
                'nombreCompleto' => '',
                'nombreNegocio' => '',
                'correo' => '',
                'direccion' => '',
                'nitCi' => '',
                'codigo' => '',
                'sucursal' => $sucursal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return null;
    }
}
