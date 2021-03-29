<html>
<head>
    <meta charset="utf-8"/>
</head>
<body style="font-size: 0.8em;height: auto">
<h3>Orden <strong>#{{$orden->correlativo}}</strong></h3>
<div><strong>Cliente:</strong> {{$orden->responsable}}</div>
<div><strong>Telefono:</strong> {{$orden->telefono}}</div>
<table style="width: 100%">
    <thead>
    <tr>
        <th>#</th>
        <th>Producto</th>
        <th>Cant.</th>
        <th>Precio</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($detalle as $key=>$item)
        <tr>
            <td style="text-align: center">{{$key+1}}</td>
            <td style="text-align: center">{{$item->stock}}</td>
            <td style="text-align: center">{{$item->cantidad}}</td>
            <td style="text-align: center">{{$item->costo}}</td>
            <td style="text-align: center">{{$item->total}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td style="text-align: center"></td>
        <td style="text-align: center"></td>
        <td style="text-align: center"></td>
        <td style="text-align: center"></td>
        <td style="text-align: center">{{$orden->montoVenta}}</td>
    </tr>
    </tfoot>
</table>
<div>
    <strong>Observaciones:</strong><br>
    {{$orden->observaciones}}
</div>

{{--<div>--}}
{{--    <img src="data:image/png;{{$QR}}" />--}}
{{--</div>--}}
</body>
</html>
