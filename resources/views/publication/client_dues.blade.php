<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>client dues</title>
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
        .line{
            width: 100%;
            border: 1px solid black;
            margin: 10px 0px;
        }
        table{
            border-collapse: separate;
            border-spacing: 0 10px;
            width: 100%;
        }
        thead{
            border-top: 2px solid;
            border-bottom: 2px solid;
        }
        .bg_gray{
            background-color: rgba(0, 0, 0, .3);
            text-align: center;
        }
        td{
            border-bottom: 2px solid white;
            padding: 5px 5px;
        }
        th{
            border-top: 2px solid;
            border-bottom: 2px solid;
            padding: 5px 5px;
        }

        .width-75px{
            width: 75px;
        }


    </style>
  </head>
  <body >
    <div class="heading text-center">
        <h1 class="fw-bolder m-0">I C S Publication</h1>
        <h4 class="mb-1 fw-semibold">48/1-A, Purana Palatan, Dhaka. Ph-9566440</h4>
        <h2 class="fw-semibold m-0">Client's Dues</h2>
        {{-- <p class="m-1"><span class="fw-semibold">Date:</span> <span>22-June-2024</span></p> --}}
        <div class="logo d-none">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTANaTDpJ6NwWc5mGH1ZVNB0HdsNH2UjO57Lg&s" alt="">
        </div>
    </div>
    <div class="table_content">
        <table>
            <thead>
                <tr>
                    <th  class="text-center width-75px">Client ID</th>
                    <th class="ps-5">Client Name</th>
                    <th class="text-end">Dues</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($user_due_info) )
                    @foreach ($user_due_info as $single_user_due)
                        <tr>
                            <td class="bg_gray">{{$single_user_due->client_id}}</td>
                            <td class="ps-5">{{$single_user_due->client_name}}</td>
                            <td class="text-end">Tk.{{number_format(abs($single_user_due->due),2, '.', ',')}}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
