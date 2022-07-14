<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $table = 'clientes';
    public static string $tables = 'clientes';
    protected $guarded = [];
    use SoftDeletes;

    public static function getAll(int $sucursal = null): \Illuminate\Support\Collection
    {
        $clientes = new Generic(self::$tables);
        if (!empty($sucursal)) {
            return $clientes->getAll(['sucursal' => $sucursal]);
        }
        return $clientes->getAll();
    }

    public static function saveCliente(string $responsable, string $telefono, int $sucursal): ?int
    {
        $cliente = DB::table(self::$tables);
        if (empty($responsable) || empty($telefono)) {
            return null;
        }
        $cliente->where('nombreResponsable', $responsable);
        $cliente->where('sucursal', $sucursal);
        if ($cliente->count() > 0) {
            return $cliente->get()->first()->id;
        }
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
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
