<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        *,
        body,
        table,thead,th,tr,td,tbody,
        h1,p{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body{
            font-family: sans-serif;
            font-size: 15px;
            width: 8.27in;
            height: 11.69in;
            margin: auto;
        }
        .title{
            font-size: 13px;
            font-weight: 600;
            font-family:  system-ui;
            margin-bottom: 2px
        }
        .sub_title{
            font-size: 12px;
            font-weight: 600;
            font-family: sans-serif;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            line-height: 13px;
            font-size: 10px;
            text-align: end;
            padding-inline: 10px;
            font-weight: 500;
            border-right: 2px solid black;
        }
        table{
            border: 2px solid black;
            width: 100%;

        }
        table th{
            font-weight: bold;
            text-align: center;
            font-family: system-ui;
        }
        tfoot tr,
        tfoot td{
            border: 2px solid black;
            text-align: center;
            font-weight: bold;
        }

        .bg_gray{
            background-color: rgba(0, 0, 0, .3);
            text-align: center;
        }
        .text_cap{
            text-transform: capitalize;
        }
        .width_50px{
            width: 50px;
        }
        /* form start */
        form{
            margin-bottom: 10px;
            text-align: center;
        }
        .date_form input{
            font-size: 10px;
        }
        .date_form button{
            font-size: 10px;
            padding: 4px;
        }

        @media print {
            .print_hidden {
                display: none;
            }
        }
        /* form end */
    </style>
  </head>
  <body >
    <div class="text-center mb-1">
        <h1 class="title">Supplier Summary</h1>
        <p class="sub_title">For The Year 2022 To {{isSet($date)? $date : '01.01.2000'}}</p>
    </div>
    <form class="print_hidden date_form" action="{{route('supplier_summary_printout')}}" method="POST">
        @csrf
        <input class="" type="date" name="date">
        <button type="submit" class="btn btn-primary btn-sm">submit Date</button>
    </form>
    <div class="table_content">
        {{-- @if (isset($data) && is_iterable($data)) --}}
        @php
            $srl = 1;
        @endphp
            <table>
                <thead>
                    <tr>
                        <th class="width_50px">SL No</th>
                        <th>Name</th>
                        <th>Opening</th>
                        <th>Bill Amount</th>
                        <th>Payment</th>
                        <th>Balance</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($binding_type_supplier_data) && is_iterable($binding_type_supplier_data))
                        @foreach ($binding_type_supplier_data as $i=>$data)
                            <tr>
                                <td class="text-center">{{$srl++}}</td>
                                <td class="text-start">{{$data->company_name}}</td>
                                <td>{{number_format($data->opening,2, '.', ',')}}</td>
                                <td>{{number_format($data->bill,2, '.', ',')}}</td>
                                <td>{{number_format($data->payment,2, '.', ',')}}</td>
                                <td>{{number_format($data->balance,2, '.', ',')}}</td>
                                <td class="text-center">{{$data->remarks}}</td>
                            </tr>
                        @endforeach
                    @endif
                    @if (isset($paper_type_supplier_data) && is_iterable($paper_type_supplier_data))
                        @foreach ($paper_type_supplier_data as $i=>$data)
                            <tr>
                                <td class="text-center">{{$srl++}}</td>
                                <td class="text-start">{{$data->company_name}}</td>
                                <td>{{number_format($data->opening,2, '.', ',')}}</td>
                                <td>{{number_format($data->bill,2, '.', ',')}}</td>
                                <td>{{number_format($data->payment,2, '.', ',')}}</td>
                                <td>{{number_format($data->balance,2, '.', ',')}}</td>
                                <td class="text-center">{{$data->remarks}}</td>
                            </tr>
                        @endforeach
                    @endif
                    @if (isset($print_type_supplier_data) && is_iterable($print_type_supplier_data))
                        @foreach ($print_type_supplier_data as $i=>$data)
                            <tr>
                                <td class="text-center">{{$srl++}}</td>
                                <td class="text-start">{{$data->company_name}}</td>
                                <td>{{number_format($data->opening,2, '.', ',')}}</td>
                                <td>{{number_format($data->bill,2, '.', ',')}}</td>
                                <td>{{number_format($data->payment,2, '.', ',')}}</td>
                                <td>{{number_format($data->balance,2, '.', ',')}}</td>
                                <td class="text-center">{{$data->remarks}}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tfoot>
                        @if(isSet($date))
                            <tr>
                                <td class="text-center" colspan="2">Total</td>
                                <td>{{number_format($total_opening,2, '.', ',')}}</td>
                                <td>{{number_format($total_bill,2, '.', ',')}}</td>
                                <td>{{number_format($total_payment,2, '.', ',')}}</td>
                                <td>{{number_format($total_balance,2, '.', ',')}}</td>
                                <td></td>
                                {{-- <td>{{number_format($single_data->total_price,2, '.', ',')}}</td> --}}
                            </tr>
                        @endif
                    </tfoot>
                </tbody>
            </table>
        {{-- @endif --}}
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
