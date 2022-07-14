@php
    $total = 0
@endphp
<html>
<head>
    <meta charset="utf-8"/>
</head>
<body
    @if($isVenta)
    style="font-size: 0.8em;"
    @else
    style="font-size: 0.9em;"
    @endif
>
<div style="text-align: right"><strong>{{$fechaOrden}}</strong></div>
<h2 style="margin: 5px 0;text-align: center">Orden <strong>{{$orden->codigoServicio}}</strong></h2>
<div><strong>Cliente:</strong> {{$orden->responsable}}</div>
<div><strong>Telefono:</strong> {{$orden->telefono}}</div>
<table style="width: 100%; margin-top: 10px; margin-bottom: 10px;">
    <thead>
    <tr>
        <th style="padding: 1px;">#</th>
        <th style="padding: 1px;">Producto</th>
        <th style="padding: 1px;">Cant.</th>
        @if($isVenta)
            <th>Precio</th>
            <th>Total</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach ($detalle as $key=>$item)
        <tr>
            <td style="padding: 1px;text-align: center">{{$key+1}}</td>
            <td style="padding: 1px;text-align: center">{{$item->stock}}</td>
            <td style="padding: 1px;text-align: center">{{$item->cantidad}}</td>
            @if($isVenta)
                <td style="text-align: center">{{$item->costo}}</td>
                <td style="text-align: center">{{$item->total}}</td>
            @endif
            @php
                $total = $total +$item->total
            @endphp
        </tr>
    @endforeach
    </tbody>
    @if($isVenta)
        <tfoot>
        <tr>
            <td colspan="4" style="text-align: right"><strong>Total </strong></td>
            <td style="text-align: center">{{$total}}</td>
        </tr>
        </tfoot>
    @endif
</table>
<div>
    <strong>Observaciones:</strong><br>
    {!!nl2br($orden->observaciones)!!}
</div>

</body>
</html>
