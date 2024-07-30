<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body{
            font-family: sans-serif;
            font-size: 15px;
            width: 8.27in;
            height: 11.69in;
            margin: auto;
            padding-top: 100px;
        }
        .heading{
            position: relative;
        }
        .heading h1{
            font-style: italic;
            letter-spacing: -2px;
            font-size: 32px;
        }
        .heading h4{
            font-size: 20px;
        }
        .heading h2{
            font-size: 26px;
        }
        .heading p{
            font-size: 18px;
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
            line-height: 30px;
        }
        .table_layout_fixed{
            table-layout: fixed ;
        }
        .table_point{
            font-size: 16px;
            font-weight: 600;
            width: 20%;
        }
        .table_point div{
            display: flex;
            justify-content: space-between;
        }
        .summery{
            width: 30%;
            margin-left: 50%;

        }
        .summery p{
            line-height: 30px;
            margin-bottom: 0px;
            display: flex;
            justify-content: space-evenly;
        }
        .line_sm{
            width: 100%;
            border: 1px solid black;
        }

        .signature{
            width: 30%;
            text-align: center;
        }
        .signature .sig_img{
            height: 40px;
            text-align: center;
        }
        .signature .sig_img img{
            height: 100%;
        }




    </style>
  </head>
  <body >
    <div class="heading text-center">
        <h1 class="fw-bolder">I C S Publication</h1>
        <h4 class="mb-3 fw-semibold">48/1-A, Purana Palatan, Dhaka. Ph-9566440</h4>
        <h2 class="fw-semibold mb-3">Due Collection Receipt</h2>
        <div class="logo">
            <img src="" alt="">
        </div>
        <p class="text-end"><span class="fw-semibold">Receipt Date :</span> <span>22-June-2024</span></p>
    </div>
    <div class="line"></div>
    <div class="table_content">
        <table class="w-100">
            <tbody>
                <tr>
                    <td class="table_point">
                        <div>
                            <span>Due C. ID</span>
                            <span class="text-end">:</span>
                        </div>
                    </td>
                    <td>2322323</td>
                    <td class="table_point">
                        <div class="justify-content-end">
                            <span>Collection Date</span>
                            <span class="text-end">:</span>
                        </div>
                    </td>
                    <td>15/06/2024</td>
                </tr>
                <tr>
                    <td class="table_point">
                        <div>
                            <span>Client Name</span>
                            <span class="text-end">:</span>
                        </div>
                    </td>
                    <td>Faridpur District</td>
                </tr>
                <tr>
                    <td class="table_point">
                        <div>
                            <span>Representative</span>
                            <span class="text-end">:</span>
                        </div>
                    </td>
                    <td>Bank</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="line"></div>

    <div class="summery">
        <p><span class="fw-semibold">Total Dues</span><span class="text-end">Tk.8,788.00</span></p>
        <p><span class="fw-semibold">Paid Amount</span><span class="text-end">Tk.8,800.00</span></p>
        <div class="line_sm"></div>
        <p><span class="fw-semibold">Current Due</span><span class="text-end">(Tk.12.00)</span></p>
    </div>

    <div class="signature">
        <p class="fw-semibold m-0">Received With Thanks.</p>
        <div class="sig_img">
            <img src="https://w7.pngwing.com/pngs/86/846/png-transparent-signature-signature-angle-text-monochrome-thumbnail.png" alt="">
        </div>
        <div class="line_sm"></div>
        <p class="fw-semibold">Signature</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
