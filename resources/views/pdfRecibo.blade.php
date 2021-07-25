@php
    $total = 0
@endphp
<html>
<head>
    <meta charset="utf-8"/>
</head>
<body
    style="font-size: 0.9em;"
>
<div style="text-align: right"><strong>{{$fechaOrden}}</strong></div>
<h2 style="margin: 5px 0;text-align: center">Recibo de
    @if($recibo->tipo)
        Egreso
    @else
        Ingreso
    @endif
        <strong>{{$recibo->secuencia}}</strong></h2>
<div><strong>Nombre:</strong> {{$recibo->nombre}}</div>
<div><strong>CI/Nit:</strong> {{$recibo->ciNit}}</div>
<div>
    <strong>Detalle:</strong><br>
    {!!nl2br($recibo->detalle)!!}
</div>

@if($recibo->saldo>0)
    <div><strong>Saldo Anterior:</strong>{{$recibo->saldo}}</div>
@endif

<div><strong>Monto:</strong>{{$recibo->monto}}</div>
@if($recibo->acuenta>0)
    <div><strong>Saldo Actual:</strong>{{$recibo->acuenta}}</div>
@endif
<br>
<br>
<br>
<div style="text-align: center">
    <small>firma</small>
</div>
</body>
</html>
