<html>
<head>
    <meta charset="utf-8"/>
    <style>
        @media print {
            .change {
                font-size: 10px !important;
            }
            * {
                font-size: 12px;
                line-height: 18px;
            }
            body {
                margin: 0.5cm;
                margin-bottom: 1.6cm;
            }
            td, th {
                padding: 1px 0;
            }
            #invoice-POS table tr {
                border-bottom: 2px dotted #05070b;
            }
        }

        .pos_page {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            min-height: 100vh;
        }

        #invoice-POS h1,
        #invoice-POS h2,
        #invoice-POS h3,
        #invoice-POS h4,
        #invoice-POS h5,
        #invoice-POS h6 {
            color: #05070b;
            font-weight: bold;
            text-align: center;
            margin: 0px;
            padding: 0px;
        }

        #pos .pos-detail {
            height: 42vh !important;
        }

        #pos .pos-detail .table-responsive {
            max-height: 40vh !important;
            height: 40vh !important;
            border-bottom: none !important;
        }

        #pos .pos-detail .table-responsive tr {
            font-size: 14px
        }

        #pos .card-order {
            min-height: 100%;
        }

        #pos .card-order .main-header {
            position: relative;
        }

        #pos .grandtotal {
            text-align: center;
            height: 40px;
            background-color: #7ec8ca;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 800;
            padding: 5px;
        }

        #pos .list-grid .list-item .list-thumb img {
            width: 100% !important;
            height: 100px !important;
            max-height: 100px !important;
            object-fit: cover;
        }

        #pos .list-grid {
            height: 100%;
            min-height: 100%;
            overflow: scroll;
        }

        #pos .brand-Active {
            border: 2px solid;
        }

        .centred {
            text-align: center;
            align-content: center;
        }

        @media (min-width: 1024px) {
            #pos .list-grid {
                height: 100vh;
                min-height: 100vh;
                overflow: scroll;
            }
        }
        #pos .card.o-hidden {
            width: 19%;;
            max-width: 19%;;
            min-width: 130px;
        }
        #pos .input-customer {
            position: relative;
            display: flex;
            flex-wrap: unset;
            align-items: stretch;
            width: 100%;
        }
        #pos .card.o-hidden:hover {
            cursor: pointer;
            border: 1px solid;
        }
        * {
            font-size: 14px;
            font-family: 'Ubuntu', sans-serif;
            text-transform: capitalize;

        }
        td,
        th,
        tr,
        table {
            border-collapse: collapse;
        }
        tr {
            border-bottom: 2px dotted #05070b;
        }
        table {
            width: 100%;
        }
        tfoot tr th:first-child {
            text-align: left;
        }
        .total {
            font-weight: bold;
            font-size: 12px;
        }
        .change {
            font-size: 10px;
            margin-top: 25px;
        }
        .centered {
            text-align: center;
            align-content: center;
        }
        #invoice-POS {
            max-width: 400px;
            margin: 0px auto;
        }
        #top .logo {
            height: 100px;
            width: 100px;
            background-size: 100px 100px;
        }
        .info {
            margin-bottom: 20px;
        }
        .title {
            float: right;
        }
        .title p {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        #invoice-POS table tr {
            border-bottom: 2px dotted #05070b;
        }
        .tabletitle {
            font-size: .5em;
            background: #EEE;
        }
        #legalcopy {
            margin-top: 5mm;
        }
        #legalcopy p {
            text-align: center;
        }
        #bar {
            text-align: center;
        }
        .quantity {
            max-width: 95px;
            width: 95px;
        }
        .quantity input {
            text-align: center;
            border: none;
        }
        .quantity .form-control:focus {
            color: #374151;
            background-color: unset;
            border-color: #e1d5fd;
            outline: 0;
            box-shadow: unset;
        }
        .quantity span {
            padding: 8px;
        }
    </style>
</head>
<body style="font-size: 0.8em;">
<div id="invoice-POS">
    {!! $body !!}
</div>
</body>
</html>
