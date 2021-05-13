<html>
<head>
    <meta charset="utf-8"/>
    <link href="{{ mix('/css/all.css') }}" rel="stylesheet"/>
</head>
<style>
    @page {
        margin: 0.2cm 0.5cm
    }

    @font-face {
        font-family: 'Open Sans', sans-serif;
    }

    body {
        font-family: 'Open Sans', sans-serif;
        font-size: 0.9em
    }

    table {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    table th, table td {
        vertical-align: top;
        padding: 1px;
    }
</style>
<body>
<div style="margin-top: 5px; text-align: right"><strong>{{$fechaAhora}}</strong></div>
<h2 style="margin: 5px 0;text-align: center">Orden <strong>#{{$orden->correlativo}}</strong></h2>
<div><strong>Cliente:</strong> {{$orden->responsable}}</div>
<div><strong>Telefono:</strong> {{$orden->telefono}}</div>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Producto</th>
        <th>Cant.</th>
        {{--        <th>Precio</th>--}}
        {{--        <th>Total</th>--}}
    </tr>
    </thead>
    <tbody>
    @foreach ($detalle as $key=>$item)
        <tr>
            <td style="text-align: center">{{$key+1}}</td>
            <td style="text-align: center">{{$item->stock}}</td>
            <td style="text-align: center">{{$item->cantidad}}</td>
            {{--            <td style="text-align: center">{{$item->costo}}</td>--}}
            {{--            <td style="text-align: center">{{$item->total}}</td>--}}
        </tr>
    @endforeach
    </tbody>

</table>
<div>
    <strong>Observaciones:</strong><br>
    {{$orden->observaciones}}
</div>

</body>
</html>
