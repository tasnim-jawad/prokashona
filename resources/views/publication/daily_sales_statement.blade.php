<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body{
            font-family: sans-serif;
            font-size: 15px;
            width: 8.27in;
            height: 11.69in;
            margin: auto;
            padding-top: 50px;
        }
        .heading h1{
            letter-spacing: -1px;
            font-size: 20px;
        }
        .heading h4{
            font-size: 14px;
        }
        .heading h2{
            font-size: 20px;
        }
        .heading p{
            font-size: 14px;
        }
        .logo{
            position: absolute;
            height: 80px;
            left: 10%;
            top: 0;
        }
        .logo img{
            height: 100%;
        }
        .heading form{
            margin-bottom: 10px
        }
        .heading input{
            font-size: 10px;
        }
        .heading button{
            font-size: 10px;
            padding: 4px;
        }
        .line{
            width: 100%;
            border: 1px solid black;
            margin: 10px 0px;
        }
        table{
            border-collapse: separate;
            border-spacing: 0 0px;
            /* width: 100%; */
            width: 7in;
            margin: auto;
        }
        thead{
            border-top: 2px solid;
            border-bottom: 2px solid;
        }
        td{
            padding: 0px 5px 5px 5px;
            text-align: center

        }
        th{
            border-top: 2px solid;
            border-bottom: 2px solid;
            text-align: center;
            font-size: 12px;

        }
        .border_tr td{
            font-size: 10px;
            border-bottom: 1px solid black;
            height: 30px;
            vertical-align: baseline;
            font-family: monospace;
        }
        .total_tr td{
            font-size: 13px;
            border-top: 1px solid black;
            height: 30px;
            vertical-align: baseline;
            font-family: monospace;
            font-weight: 600;
        }
        .border_tr .font_bold{
            font-weight: 600;
            font-family: sans-serif;
            font-size: 11px;
        }

        .width-75px{
            width: 75px;
        }
        .width_70px{
            width: 70px;
        }
        .width_80px{
            width: 80px;
        }
        .width_90px{
            width: 90px;
        }
        .width_100px{
            width: 100px;
        }
        .width_110px{
            width: 110px;
        }

        @media print {
            .print_hidden {
                display: none;
            }
        }
    </style>
  </head>
  <body >
    <div class="heading text-center">
        <h1 class="fw-bolder m-0">I C S Publication</h1>
        <h4 class="mb-1 fw-semibold">48/1-A, Purana Palatan, Dhaka. Ph-9566440</h4>
        <h2 class="fw-semibold mb-3">Daily Sales Statement of : 09-jun-2024 </h2>
        <form class="print_hidden" action="{{route('daily_sales_statement_printout')}}" method="POST">
            @csrf
            <input class="" type="date" name="date">
            <button type="submit" class="btn btn-primary btn-sm">Small button</button>
        </form>
        <div class="logo d-none">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTANaTDpJ6NwWc5mGH1ZVNB0HdsNH2UjO57Lg&s" alt="">
        </div>
    </div>
    <div class="table_content">
        <table>
            <thead>
                <tr>
                    <th class="text-center">Sales ID</th>
                    <th>Client Name</th>
                    {{-- <th>Client Representative Name</th> --}}
                    <th class="text-end pe-5">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data) && is_iterable($data))
                    @foreach ($data as $single_data)
                        <tr class="border_tr">
                            <td class="font_bold">{{$single_data->sales_id}}</td>
                            <td class="text-start">{{$single_data->user->first_name}}</td>
                            {{-- <td>Online -- 5,000 /= </td> --}}
                            <td class="text-end">Tk.{{number_format($single_data->total_price,2, '.', ',')}}</td>
                        </tr>
                    @endforeach
                    <tr class="total_tr">
                        <td colspan="2" class="text-start">Total Sales ID: <span>{{$data->count()}}</span></td>
                        {{-- <td class="text-end"></td> --}}
                        <td class="text-end"><span class="me-3">Total Sales Amount:</span><span>Tk.{{number_format($data->sum('total_price'),2, '.', ',')}}</span></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
