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
        .line{
            width: 100%;
            border: 1px solid black;
            margin: 10px 0px;
        }
        table{
            border-collapse: separate;
            border-spacing: 0 0px;
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
            padding: 5px 5px;
            text-align: center

        }
        th{
            border-top: 2px solid;
            border-bottom: 2px solid;
            text-align: center;
            font-size: 12px;

        }
        .small_font_tr{
            height: 60px;
        }
        .small_font_tr td{
            font-size: 10px;
            border-bottom: 2px solid gray;
        }
        .big_font_tr,
        .big_font_tr td{
            font-weight: 700;
            font-size: 14px;
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


    </style>
  </head>
  <body >
    <div class="heading text-center">
        <h1 class="fw-bolder m-0">I C S Publication</h1>
        <h4 class="mb-1 fw-semibold">48/1-A, Purana Palatan, Dhaka. Ph-9566440</h4>
        <h2 class="fw-semibold mb-2">Daily Add Products Statement of : 09-jun-2024</h2>
        <div class="logo d-none">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTANaTDpJ6NwWc5mGH1ZVNB0HdsNH2UjO57Lg&s" alt="">
        </div>
    </div>
    <div class="table_content">
        <table>
            <thead>
                <tr>
                    <th  class="text-center">AddID</th>
                    <th class="width_110px">Product Name</th>
                    <th>Order No.</th>
                    <th>Supplier</th>
                    <th>Press</th>
                    <th>Binder</th>
                    <th>Add Date</th>
                    <th>Adding Qnt.</th>
                    <th class="width_90px">Qnt. Add Time</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 10; $i++)
                    <tr class="small_font_tr">
                        <td>8342</td>
                        <td class="width_110px">Islami Andolon Shafoller Shortaboli</td>
                        <td>45</td>
                        <td>joti book Binding</td>
                        <td>N/A</td>
                        <td>joti book Binding</td>
                        <td>09/06/2024</td>
                        <td>564</td>
                        <td>14267</td>
                    </tr>
                    <tr class="big_font_tr">
                        <td class="text-center" colspan="2">Count of Add:</td>
                        <td>1</td>
                        <td colspan="2">Total Quantity:</td>
                        <td>{{869 + ($i*20)}}</td>
                        <td colspan="2">Current Stock:</td>
                        <td> {{14259 * $i}}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
