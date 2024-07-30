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
            height: 8.27in;
            width: 11.69in;
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
            font-size: 16px;
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
        .line{
            width: 100%;
            border: 1px solid black;
            margin: 10px 0px;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: end;
            padding-inline: 10px;
            font-weight: 500;
        }
        table{
            width: 100%
        }
        .total_tr td{
            font-weight: 700;
            border-top: 2px solid black;
            border-bottom: 2px solid black;
        }
        .total_tr .border_right{
            border-right: 2px solid black;
        }
        .total_tr .border_left{
            border-left: 2px solid black;
        }
        thead{
            border-top: 2px solid;
            border-bottom: 2px solid;
        }
        td{
            padding: 5px 10px;
            text-align: center

        }
        th{
            border-top: 3px solid;
            border-bottom: 3px solid;
            text-align: center;
            font-size: 14px;
            font-weight: 700;

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
    <div class="heading text-center">
        <h1 class="fw-bolder m-0">I C S Publication</h1>
        <h4 class="mb-1 fw-semibold">48/1-A, Purana Palatan, Dhaka. Ph-9566440</h4>
        <h2 class="fw-semibold mb-2">Datewise Add Stock</h2>
        {{-- <div class="logo d-none">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTANaTDpJ6NwWc5mGH1ZVNB0HdsNH2UjO57Lg&s" alt="">
        </div> --}}
        <form class="print_hidden date_form" action="{{route('datewise_add_stock_printout')}}" method="POST">
            @csrf
            <input class="" type="date" name="date">
            <button type="submit" class="btn btn-primary btn-sm">submit Date</button>
        </form>
    </div>
    <div class="table_content">
        <table>
            <thead>
                <tr>
                    <th>srl</th>
                    <th>Product Name</th>
                    <th>Product stock</th>
                    <th>Product seles</th>
                    <th>New stock</th>
                    <th>Stock Update Date</th>
                    <th>Order id</th>
                    <th>Supplier Name</th>
                    <th>Binder Name</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($product_stock_data))
                    @foreach ($product_stock_data as $i=>$single_data)
                        <tr>
                            <td>{{$i + 1}}</td>
                            <td>{{$single_data->product_name}}</td>
                            <td>{{$single_data->product_stock}}</td>
                            <td>{{$single_data->product_seles}}</td>
                            <td>{{$single_data->new_stock}}</td>
                            <td>{{$single_data->stock_update_date}}</td>
                            <td></td>
                            <td>----</td>
                            <td>----</td>
                        </tr>
                    @endforeach
                    <tr class="total_tr">
                        <td class="border_left"></td>
                        <td></td>
                        <td>{{$total_stock}}</td>
                        <td>{{$total_seles}}</td>
                        <td>{{$total_new_stock}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="border_right"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
