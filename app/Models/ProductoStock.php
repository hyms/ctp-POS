<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\This;

class ProductoStock extends Model
{
    protected $table = 'stock';
    public static string $tables = 'stock';
    protected $guarded = [];

    public static function getAll(int $sucursal = null)
    {
        $stock = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $stock = $stock->where('sucursal', '=', $sucursal);
        }
        return $stock->get();
    }

    public static function more(array $request, bool $mov = true)
    {
        return self::moreLess($request, $mov, true);
    }

    public static function less(array $request, bool $mov = true)
    {
        return self::moreLess($request, $mov, false);
    }

    private static function moreLess(array $request, bool $mov, bool $more)
    {
        $stock = DB::table(self::$tables)
            ->where('sucursal', '=', $request['sucursal'])
            ->where('producto', '=', $request['producto']);
        $cantidad = 0;
        if ($stock->count() > 0) {
            $cantidad = $stock->get()->first()->cantidad;
        }
        $cantidad = $more
            ? $cantidad + $request['cantidad']
            : $cantidad - $request['cantidad'];

        $sucursalPadre = DB::table(Sucursal::$tables)
            ->where('id', '=', $request['sucursal'])
            ->get('dependeDe')->first()->dependeDe;
        $values = array(
            'cantidad' => $cantidad,
        );
        if (!empty($request['orden'])) {
            $values['orden'] = $request['orden'];
        }
        if (!empty($request['precioUnidad']) && $request['precioUnidad'] != 0) {
            $values['precioUnidad'] = $request['precioUnidad'];
        }
        $stock->updateOrInsert([
            'producto' => $request['producto'],
            'sucursal' => $request['sucursal']
        ], $values);
        $stockOrigen = null;
        if (!empty($sucursalPadre)) {
            $moreLess = array(
                'sucursal' => $sucursalPadre,
                'producto' => $request['producto'],
                'cantidad' => $request['cantidad']
            );
            $stockOrigen = $more
                ? self::less($moreLess, false)
                : self::more($moreLess, false);
        }

        if ($mov) {
            $movimiento = DB::table(MovimientoStock::$tables);
            if ($more) {
                $origen = ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? $stockOrigen->get()->first()->id : null);
                $destino = $stock->get()->first()->id;
                $observaciones = ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? "Traspaso de insumos" : "Compra de insumos");
            } else {
                $origen = $stock->get()->first()->id;
                $destino = ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? $stockOrigen->get()->first()->id : null);
                $observaciones = ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? "Devolucion de insumos" : "devolucion de compra");
            }

            $movimiento->insert([
                'producto' => $request['producto'],
                'stockOrigen' => $origen,
                'stockDestino' => $destino,
                'cantidad' => $request['cantidad'],
                'observaciones' => $observaciones,
                'user' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return $stock;
    }

    public static function sell(array $request, bool $mov = true, bool $reposicion = false)
    {
        $stock = DB::table(self::$tables)
            ->where('sucursal', '=', $request['sucursal'])
            ->where('producto', '=', $request['producto']);
        $cantidad = 0;
        if ($stock->count() > 0) {
            $cantidad = $stock->get()->first()->cantidad;
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
                'stockOrigen' => $stock->get()->first()->id,
                'stockDestino' => null,
                'cantidad' => $request['cantidad'],
                'observaciones' => (($reposicion) ? "reposicion de insumos" : "venta de insumos"),
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
                    $stockItem[$producto->id] = $tmp->get()->first();
                } else {
                    $stockItem[$producto->id] = null;
                }
            }
            $stock[$sucursal->id] = $stockItem;
        }
        return $stock;
    }

    public static function getProducts($sucursal = null, array $tiposProductos = [])
    {
        $stock = self::product($sucursal);
        $stock->orderBy('productos.formato', 'asc');
        $stock->select(self::$tables . '.*', Producto::$tables . '.codigo', Producto::$tables . '.formato', Producto::$tables . '.dimension');

        if ($tiposProductos) {
            $stocks = array();
            foreach ($tiposProductos as $tiposProducto) {
                $stockTmp = $stock->clone();
                $productosTipos = DB::table('productoTipo')
                    ->where('tipoProducto', '=', $tiposProducto->id);
                $productosTipos = $productosTipos->pluck('producto');
                $stocks[$tiposProducto->id] = $stockTmp->whereIn('productos.id', $productosTipos)->get()->toArray();
            }
            return $stocks;
        }
        return $stock->get();
    }

    public static function getProduct($sucursal, $id)
    {
        $stock = self::product($sucursal, $id);
        if ($stock->count() > 0) {
            return $stock->get()->first();
        }
        return null;
    }

    public static function getTipos($sucursal)
    {
        $stock = self::product($sucursal);
        return $stock->get('tipo');
    }

    private static function product($sucursal, $id = null)
    {
        $stock = DB::table(self::$tables);
        if (!empty($sucursal)) {
            $stock->where('sucursal', '=', $sucursal);
        }
        $stock->where('enable', '=', true);
        if (!empty($id)) {
            $stock->where(self::$tables . '.id', '=', $id);
        }
        $stock->leftJoin(Producto::$tables, 'producto', '=', 'productos.id');
        $stock->whereNull('productos.deleted_at');
        return $stock;
    }
}
