<?php

use App\Models\Account\AccountLog;
use App\Models\Payment\UserPayment;
use App\Models\Payment\UserPaymentRemain;
use App\Models\Product\Product;
use App\Models\Product\ProductStockLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

function enToBn($NRS)
{
    $englDTN = array(
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
        'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
        'Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri',
        'am', 'pm', 'at', 'st', 'nd', 'rd', 'th',
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    );
    $bangDTN = array(
        '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০',
        'শনিবার', 'রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার',
        'শনি', 'রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহঃ', 'শুক্র',
        'পূর্বাহ্ণ', 'অপরাহ্ণ', '', '', '', '', '',
        'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',
        'জানু', 'ফেব্রু', 'মার্চ', 'এপ্রি', 'মে', 'জুন', 'জুলা', 'আগ', 'সেপ্টে', 'অক্টো', 'নভে', 'ডিসে'
    );
    $converted = str_replace($englDTN, $bangDTN, $NRS);
    return $converted;
}

function setting($key, $multiple = false)
{
    try {
        if (!$multiple) {
            $value = $GLOBALS['app_settings']->where("title", $key)->first();
            if ($value) {
                return $value->setting_value;
            } else {
                return '';
            }
        } else {
            $values = $GLOBALS['app_settings']->where("title", $key)->all();
            return $values;
        }
    } catch (\Throwable $th) {
        var_dump($th->getMessage());
    }
}

function set_stock_log($product_id, $qty, $type, $mr_no, $supplier_name, $order_id, $binder_name)
{
    // dd($product_id, $qty, $type, $mr_no, $supplier_name, $order_id, $binder_name);
    $dataStock = new ProductStockLog();
    $dataStock->product_id = $product_id;
    $dataStock->qty = $qty;
    $dataStock->type = $type;
    $dataStock->mr_no = $mr_no;

    $dataStock->supplier_name = $supplier_name;
    $dataStock->order_id = $order_id;
    $dataStock->binder_name = $binder_name;

    $dataStock->creator = Auth::user()?Auth::user()->id: 2;
    $dataStock->save();
}

/**
 ```
    store_account_log([
        'date' => "",
        "name" => "",
        'amount' => "",
        'category_id' => 1, // ponno theke ay
        'account_id' => "",
        'account_number_id' => "",
        'trx_id' => "",
        'is_income' => 1,
        'is_expense' => 0,
        'description' => 'admin received and inserted client payment',
    ]);
 ```
 */
function store_account_log($data)
{
    $data = (object) $data;
    $log = AccountLog::create([
        'date' => Carbon::now()->toDateTimeString(),
        "name" => $data->name,
        "customer_id" => $data->customer_id,
        'amount' => $data->amount,
        'category_id' => $data->category_id,
        'account_id' => $data->account_id,
        'account_number_id' => $data->account_number_id,
        'trx_id' => $data->trx_id,
        'is_income' => $data->is_income,
        'is_expense' => $data->is_expense,
        'description' => $data->description,
    ]);

    return $log;
}

/**
 ```
    credit_user_payment([
        'user_id' => '',
        'date' => '',
        'amount' => '',
        'type' => 'debit' | 'credit',
        'transaction_type' => 'customer_advance'| 'customer_order'| 'customer_refund',
        'account_log_id' => '',
        'payment_method' => '',
        'account_id' => '',
        'account_number_id' => '',
        'trx_id' => '',
    ]);
 ```
 */

function credit_user_payment($data){
    $data = (object) $data;
    $payment = new UserPayment();
    $payment->user_id = $data->user_id;
    $payment->date = $data->date;
    $payment->amount = $data->amount;
    $payment->type = "credit";
    $payment->transaction_type = $data->transaction_type;

    $payment->account_log_id = $data->account_log_id ?? null;
    $payment->payment_method = $data->payment_method;
    $payment->account_id = $data->account_id;
    $payment->account_number_id = $data->account_number_id;
    $payment->trx_id = $data->trx_id;

    $payment->save();
    return $payment;
}

/**
 ```
    debit_user_payment([
        'user_id' => '',
        'date' => '',
        'amount' => '',
        'reference_table_name' => 'orders',
        'reference_table_id' => '',
    ]);
 ```
 */

 function debit_user_payment($data){
    $data = (object) $data;
    $payment = new UserPayment();
    $payment->user_id = $data->user_id;
    $payment->date = $data->date;
    $payment->amount = $data->amount;
    $payment->type = "debit";
    $payment->reference_table_name = $data->reference_table_name;
    $payment->reference_table_id = $data->reference_table_id;
    $payment->save();
    return $payment;
}

/**
 ```
    calc_user_payment([
        'user_id' => '',
    ]);
 ```
 */
function calc_user_payment($data){
    $data = (object) $data;
    $debit = UserPayment::where('user_id', $data->user_id)->where('type','debit')->sum('amount');
    $credit = UserPayment::where('user_id', $data->user_id)->where('type','credit')->sum('amount');

    $remain = UserPaymentRemain::where('user_id',$data->user_id)->first();
    if(!$remain){
        $remain = new UserPaymentRemain();
    }
    $remain->user_id = $data->user_id;
    $remain->amount = $credit - $debit;
    $remain->save();

    return (object)[
        "debit" => $debit,
        "credit" => $credit,
        "remain" => $remain,
    ];
}
