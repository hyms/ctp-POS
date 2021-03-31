<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class ProductoStock extends Model
{
    protected $table = 'stock';
    public static $tables = 'stock';
    protected $guarded = [];

    public static function getAll(int $sucursal = null)
    {
        $stock = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $stock = $stock->where('sucursal', '=', $sucursal);
        }
        return $stock->get();
    }

    public static function get(int $sucursal, int $producto)
    {
        return DB::table(self::$tables)
            ->where('sucursal', '=', $sucursal)
            ->where('producto', '=', $producto);
    }

    public static function more(array $request, bool $mov = true)
    {
        $stock = self::get($request['sucursal'], $request['producto']);
        $cantidad = 0;
        if ($stock->count() > 0) {
            $cantidad = $stock->get()[0]->cantidad;
        }
        $cantidad += $request['cantidad'];
        $sucursalPadre = DB::table(Sucursal::$tables)
            ->where('id', '=', $request['sucursal'])
            ->get('dependeDe')[0]->dependeDe;
        $values = array(
            'cantidad' => $cantidad,
        );
        if (!empty($request['orden'])) {
            $values['orden'] = $request['orden'];
        }
        if (!empty($request['precioUnidad'])) {
            $values['precioUnidad'] = $request['precioUnidad'];
        }
        $stock->updateOrInsert([
            'producto' => $request['producto'],
            'sucursal' => $request['sucursal']
        ], $values);
        $stockOrigen = null;
        if (!empty($sucursalPadre)) {
            $stockOrigen = self::less(array(
                'sucursal' => $sucursalPadre,
                'producto' => $request['producto'],
                'cantidad' => $request['cantidad']
            ), false);
        }
        if ($mov) {
            $movimiento = DB::table(MovimientoStock::$tables);
            $movimiento->insert([
                'producto' => $request['producto'],
                'stockOrigen' => ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? $stockOrigen->get()[0]->id : null),
                'stockDestino' => $stock->get()[0]->id,
                'cantidad' => $request['cantidad'],
                'observaciones' => ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? "Traspaso de insumos" : "Compra de insumos"),
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return $stock;
    }

    public static function less(array $request, bool $mov = true)
    {
        $stock = self::get($request['sucursal'], $request['producto']);
        $cantidad = 0;
        if ($stock->count() > 0) {
            $cantidad = $stock->get()[0]->cantidad;

        }
        $cantidad -= $request['cantidad'];
        $sucursalPadre = DB::table(Sucursal::$tables)
            ->where('id', '=', $request['sucursal'])
            ->get('dependeDe')[0]->dependeDe;
        $values = array(
            'cantidad' => $cantidad,
        );
        if (!empty($request['orden'])) {
            $values['orden'] = $request['orden'];
        }
        if (!empty($request['precioUnidad'])) {
            $values['precioUnidad'] = $request['precioUnidad'];
        }
        $stock->updateOrInsert([
            'producto' => $request['producto'],
            'sucursal' => $request['sucursal']
        ], $values);
        $stockOrigen = null;
        if (!empty($sucursalPadre)) {
            $stockOrigen = self::more(array(
                'sucursal' => $sucursalPadre,
                'producto' => $request['producto'],
                'cantidad' => $request['cantidad']
            ), false);
        }

        if ($mov) {
            $movimiento = DB::table(MovimientoStock::$tables);
            $movimiento->insert([
                'producto' => $request['producto'],
                'stockOrigen' => $stock->get()[0]->id,
                'stockDestino' => ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? $stockOrigen->get()[0]->id : null),
                'cantidad' => $request['cantidad'],
                'observaciones' => ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? "Devolucion de insumos" : "devolucion de compra"),
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return $stock;
    }

    public static function sell(array $request, bool $mov = true)
    {
        $stock = self::get($request['sucursal'], $request['producto']);
        $cantidad = 0;
        if ($stock->count() > 0) {
            $cantidad = $stock->get()[0]->cantidad;
        }
        $cantidad -= $request['cantidad'];
        $stock->updateOrInsert([
            'producto' => $request['producto'],
            'sucursal' => $request['sucursal']
        ], ['cantidad' => $cantidad]);

        if ($mov) {
            $movimiento = DB::table(MovimientoStock::$tables);
            $movimiento->insert([
                'producto' => $request['producto'],
                'stockOrigen' => $stock->get()[0]->id,
                'stockDestino' => null,
                'cantidad' => $request['cantidad'],
                'observaciones' => "venta de insumos",
                'detalleOrden' => !empty($request['detalleOrden']) ? $request['detalleOrden'] : "",
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return $stock;
    }

    public static function getTableAdmin($suscursales, $productos)
    {
        $stock = [];
        foreach ($suscursales as $sucursal) {
            $stockItem = [];
            foreach ($productos as $producto) {
                $tmp = DB::table(self::$tables)->where([
                    ['sucursal', '=', $sucursal->id],
                    ['producto', '=', $producto->id]
                ]);
                if ($tmp->count() > 0) {
                    $stockItem[$producto->id] = $tmp->get()[0];
                } else {
                    $stockItem[$producto->id] = null;
                }
            }
            $stock[$sucursal->id] = $stockItem;
        }
        return $stock;
    }

    public static function getProducts($sucursal = null)
    {
        $stock = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $stock = $stock->where('sucursal', '=', $sucursal);
        }
        $stock = $stock->leftJoin(Producto::$tables, 'producto', '=', 'productos.id');
        $stock = $stock->whereNull('productos.deleted_at');
        $stock= $stock->select(self::$tables.'.*',Producto::$tables.'.codigo',Producto::$tables.'.formato',Producto::$tables.'.dimension');
        return $stock->get();
    }

    public static function getProduct($sucursal, $id)
    {
        $stock = DB::table(self::$tables);
        $stock = $stock->where('sucursal', '=', $sucursal);
        $stock = $stock->where(self::$tables . '.id', '=', $id);
        $stock = $stock->leftJoin(Producto::$tables, 'producto', '=', 'productos.id');
        $stock = $stock->whereNull('productos.deleted_at');
        if ($stock->count() > 0) {
            return $stock->get()[0];
        }
        return null;
    }


}
