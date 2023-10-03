<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductoStock extends Model
{
    protected $table = 'stock';
    public static string $tables = 'stock';
    protected $guarded = [];

    public static function getAll(int $sucursal = null): Collection
    {
        $stock = new Generic(self::$tables);
        $stock->isDelete = false;
        return !empty($sucursal)
            ? $stock->getAll(['sucursal' => $sucursal])
            : $stock->getAll();
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
            ->where('sucursal', $request['sucursal'])
            ->where('producto', $request['producto']);
        $cantidad = 0;
        if ($stock->count() > 0) {
            $cantidad = $stock->get()->first()->cantidad;
        }
        $cantidad = $more
            ? $cantidad + $request['cantidad']
            : $cantidad - $request['cantidad'];

        $sucursalPadre = DB::table(Sucursal::$tables)
            ->where('id', $request['sucursal'])
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
                $observaciones = ((!empty($stockOrigen) && $stockOrigen->count() > 0) ? "Devolucion de insumos" : "Devolucion de compra");
            }

            $movimiento->insert([
                'producto' => $request['producto'],
                'stockOrigen' => $origen,
                'stockDestino' => $destino,
                'cantidad' => $request['cantidad'],
                'observaciones' => $observaciones,
                'user' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        return $stock;
    }

    public static function getTableAdmin($suscursales, $productos)
    {
        $productoStock = Collection::empty();
        foreach ($suscursales as $sucursal) {
            $stockItem = Collection::empty();
            foreach ($productos as $producto) {
                $item = Collection::empty();
                $item->put('sucursalId', $sucursal->id);
                $item->put('productoId', $producto->id);
                $item->put('productoView', "{$producto->formato} {$producto->dimension}");
                $tmp = DB::table(self::$tables)->where([
                    ['sucursal', '=', $sucursal->id],
                    ['producto', '=', $producto->id]
                ]);
                if ($tmp->count() > 0) {
                    $tmp = $tmp->get()->first();
                    $item->put('cantidad', $tmp->cantidad);
                    $item->put('precio', $tmp->precioUnidad);
                    $item->put('stockId', $tmp->id);
                } else {
                    $item->put('cantidad', null);
                    $item->put('precio', null);
                    $item->put('stockId', null);
                }
                $stockItem->add($item);
            }
            $productoStock->add(['sucursalId' => $sucursal->id, 'sucursalView' => $sucursal->nombre, 'productos' => $stockItem->all()]);
        }
        return $productoStock->all();
    }

    public static function getProducts($sucursal = null, array $tiposProductos = []): Collection
    {
        $stock = self::product($sucursal);
        $stock->orderBy('productos.formato', 'asc');
        $stock->select(self::$tables . '.*', Producto::$tables . '.codigo', Producto::$tables . '.formato', Producto::$tables . '.dimension');

        if ($tiposProductos) {
            $stocks = Collection::empty();
            foreach ($tiposProductos as $tiposProducto) {
                $stockTmp = $stock->clone();
                $productosTipos = DB::table(TipoProductos::$tablesAlter)
                    ->where('tipoProducto', '=', $tiposProducto->id)
                    ->pluck('producto');
                $stocks->put($tiposProducto->id, $stockTmp
                    ->whereIn(Producto::$tables . '.id', $productosTipos)
                    ->get()
                    ->toArray());
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
            $stock->where(self::$tables . '.sucursal', '=', $sucursal);
        }
        if (!empty($id)) {
            $stock->where(self::$tables . '.id', '=', $id);
        }
        $stock->leftJoin(Producto::$tables, self::$tables . '.producto', '=', Producto::$tables . '.id');
        $stock->whereNull(Producto::$tables . '.deleted_at');
        return $stock;
    }
}
