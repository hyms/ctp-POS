<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sale {{$sale['Ref']}}</title>
{{--    <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all"/>--}}
    @vite(['resources/css/pdf_style.css'])
</head>

<body>
<header class="clearfix">
    <div id="logo">
{{--        <img src="{{asset('/images/'.$setting['logo'])}}">--}}
    </div>
    <div id="company">
        <div><strong> Fecha : </strong>{{$sale['date']}}</div>
        <div><strong> Codigo : </strong> {{$sale['Ref']}}</div>
        <div><strong> Estado : </strong> {{$sale['statut']}}</div>
        <div><strong> Estado de Pago : </strong> {{$sale['payment_status']}}</div>
    </div>
    <div id="Title-heading">
        Orden : {{$sale['Ref']}}
    </div>
    </div>
</header>
<main>
    <div id="details" class="clearfix">
        <div id="client">
            <table class="table-sm">
                <thead>
                <tr>
                    <th class="desc">Info Cliente</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div><strong>Nombre :</strong> {{$sale['client_name']}}</div>
                        <div><strong>Empresa :</strong> {{$sale['client_company_name']}}</div>
                        <div><strong>Telefono :</strong> {{$sale['client_phone']}}</div>
                        <div><strong>Correo :</strong> {{$sale['client_email']}}</div>
                        <div><strong>Direccion :</strong> {{$sale['client_adr']}}</div>
                        @if($sale['client_tax'])
                            <div><strong>Nit :</strong> {{$sale['client_tax']}}</div>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="invoice">
{{--            <table class="table-sm">--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <th class="desc">Datos de la Empresa</th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                <tr>--}}
{{--                    <td>--}}
{{--                        <div id="comp">{{$setting['CompanyName']}}</div>--}}
{{--                        <div><strong>Telefono :</strong> {{$setting['CompanyPhone']}}</div>--}}
{{--                        <div><strong>Correo :</strong> {{$setting['email']}}</div>--}}
{{--                        <div><strong>Direccion :</strong> {{$setting['CompanyAdress']}}</div>--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--                </tbody>--}}
{{--            </table>--}}
        </div>
    </div>
    <div id="details_inv">
        <table class="table-sm">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Precio x Unidad</th>
                <th>Cantidad</th>
                <th>Descuento</th>
                <th>TOTAL</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($details as $detail)
                <tr>
                    <td>
                        <span>{{$detail['code']}} ({{$detail['name']}})</span>
                        @if($detail['is_imei'] && $detail['imei_number'] !==null)
                            <p>IMEI/SN : {{$detail['imei_number']}}</p>
                        @endif
                    </td>
                    <td>{{$detail['price']}} </td>
                    <td>{{$detail['quantity']}}/{{$detail['unitSale']}}</td>
                    <td>{{$detail['DiscountNet']}} </td>
                    <td>{{$detail['total']}} </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div id="total">
        <table>
            <tr>
                <td>Total</td>
                <td>{{$symbol}} {{$sale['GrandTotal']}} </td>
            </tr>

            <tr>
                <td>Paid Amount</td>
                <td>{{$symbol}} {{$sale['paid_amount']}} </td>
            </tr>

            <tr>
                <td>Due</td>
                <td>{{$symbol}} {{$sale['due']}} </td>
            </tr>
        </table>
    </div>
    <div id="signature">
        @if(isset($setting['is_invoice_footer']))
            <p>{{$setting['invoice_footer']}}</p>
        @endif
    </div>
</main>
</body>
</html>
