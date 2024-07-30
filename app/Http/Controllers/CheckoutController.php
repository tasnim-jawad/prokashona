<?php

namespace App\Http\Controllers;

use App\Models\Order\Order;
use App\Models\Order\OrderCoupon;
use App\Models\Order\OrderDeliveryInfo;
use App\Models\Order\OrderDetails;
use App\Models\Order\OrderPayment;
use App\Models\Product\Product;
use App\Models\Settings\AppSettingTitle;
use App\Models\User\Address;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public $message = "";

    public function checkout()
    {
        $carts = json_decode(request()->carts);
        $data = request()->except('carts');
        $data['carts'] = $carts;
        $rules = [
            'full_name' => ['required', 'string'],
            // 'last_name' => ['required'],
            'address' => ['required'],
            'mobile_number' => ['required', 'string'],
            // 'email' => ['email'],
            // 'city' => ['required', 'string'],
            // 'zone' => ['required', 'string'],
            // 'payment_method' => ['required'],
            // 'bkash_number' => ['required_if:payment_method,==,bkash'],
            // 'bkash_trx_id' => ['required_if:payment_method,==,bkash'],
            // 'bank_account_no' => ['required_if:payment_method,==,bank'],
            // 'bank_transaction_id' => ['required_if:payment_method,==,bank'],
            'shipping_method' => ['required'],
            'divisions' => ['required'],
            'districts' => ['required'],
            'police_stations' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'err_message' => 'validation error',
                'data' => $errors,
            ], 422);
        }

        if(request()->police_stations == 'Enter police station name'){
            return response()->json([
                'err_message' => 'validation error',
                'data' => ["police_stations"=>["enter your police station name."]],
            ], 422);
        }

        $name = request()->full_name;
        $address = request()->address;
        $mobile_number = request()->mobile_number;
        $shipping_method = request()->shipping_method;
        $delivery_cost = HelperController::delivery_cost();
        $shipping_cost = 0;
        $sub_total_cost = 0;
        $total_cost = 0;
        $message_products = "";
        $total_discount = 0;
        $coupon_info = ["order_coupon_id" => null, "coupon_discount" => 0];

        if ($shipping_method == "home_delivery") {
            $shipping_cost = $delivery_cost->home_delivery_cost;
        }

        if ($shipping_method == "outside_dhaka") {
            $shipping_cost = $delivery_cost->out_dhaka_home_delivery_cost;
        }

        $products = [];
        foreach ($carts as $key => $item) {
            $si = $key + 1;
            $product = Product::findById($item->id);
            $product->qty = $item->qty;
            $products[] = $product;
            $main_price = $product->sales_price;
            $discount_percent = $product->discount_info->discount_percent;
            $price = $product->discount_info->discount_amount ? $product->discount_info->discount_price : $product->sales_price;
            $total = $item->qty * $price;
            $sub_total_cost += $total;
            $total_discount += $product->discount_info->discount_amount;

            $bn_price = enToBn("৳ $price x $item->qty	= ৳ $total \n\t\t\t (৳ $main_price - $discount_percent%)");
            $message_products .= "$si. $item->product_name - \n\t\t\t $bn_price \n";
        }

        $total_cost = $sub_total_cost + $shipping_cost;

        if (request()->coupon) {
            $coupon_info = $this->validate_coupon(request()->coupon, $total_cost);
        }

        $total_cost -= $coupon_info["coupon_discount"];

        $order = $this->save_order([
            "products" => $products,
            "request" => request()->except('carts'),
            "sub_total_cost" => $sub_total_cost,
            "total_cost" => $total_cost,
            "total_discount" => $total_discount,
            "shipping_cost" => $shipping_cost,
            "coupon_info" => $coupon_info,
        ]);
        $this->make_message($message_products, $sub_total_cost, $shipping_cost, $coupon_info["coupon_discount"], $total_cost, $name, $mobile_number, $address, $order->invoice_id);

        return response()->json([
            "message" => "Order Completed Successfully",
            "order" => $order->invoice_id,
        ], 200);
    }

    public function save_order($data = [])
    {
        $products = $data["products"];
        $request = $data["request"];
        $sub_total_cost = $data["sub_total_cost"];
        $total_cost = $data["total_cost"];
        $total_discount = $data["total_discount"];
        $shipping_cost = $data["shipping_cost"];
        $coupon_info = $data["coupon_info"];
        $request = (object) $request;
        $auth_user = auth()->check() ? auth()->user() : null;
        $address = $this->save_address($request);
        $variant_price = 0;
        $invoice_prefix = AppSettingTitle::getValue("invoice_prefix");

        $order = Order::create([
            'user_id' => $auth_user ? $auth_user->id : null, // user id
            "customer_id" => null, //customer id
            "address_id" => $address->id, // user address id, customer
            "invoice_id" => $invoice_prefix . "-" . Carbon::now()->format("Ymd"),
            "invoice_date" => Carbon::now()->toDateTimeString(),
            "order_type" => "ecommerce", // Quotation, Pos order, Ecomerce order
            "order_status" => "pending",
            "order_coupon_id" => $coupon_info["order_coupon_id"],

            "sub_total" => $sub_total_cost,
            "discount" => $total_discount,
            "coupon_discount" => $coupon_info["coupon_discount"],
            "delivery_charge" => $shipping_cost,
            "variant_price" => $variant_price, // extra charge for product variants
            "total_price" => $total_cost + $variant_price,

            "payment_status" => "pending", // pending, partially paid, paid
            "delivery_method" => $request->shipping_method,
        ]);

        $order->invoice_id .= $order->id;
        $order->save();

        foreach ($products as $product) {
            OrderDetails::create([
                "order_id" => $order->id,
                "product_id" => $product->id,
                "product_name" => $product->product_name,
                "product_code" => $product->sku,
                "product_price" => $product->sales_price,
                "discount_price" => $product->discount_info->discount_amount,
                "sales_price" => $product->discount_info->discount_price,
                "qty" => $product->qty,
            ]);
        }

        $this->save_delivery_info($order, $request, $shipping_cost, $address);
        $this->save_order_payments($order, $request);

        return $order;
    }

    public function save_delivery_info($order, $request, $shipping_cost, $address)
    {
        $auth_user = auth()->check() ? auth()->user() : null;
        OrderDeliveryInfo::create([
            "order_id" => $order->id,
            "user_id" => $auth_user ? $auth_user->id : null,
            "customer_id" => null,
            "delivery_method" => $request->shipping_method,
            "delivery_cost" => $shipping_cost,
            "courier_name" => "",
            "address_id" => $address->id,
            "location_id" => $address->id, // shipping id
        ]);
    }

    public function save_order_payments($order, $request)
    {
        $auth_user = auth()->check() ? auth()->user() : null;
        // $payment = OrderPayment::create([
        //     "order_id" => $order->id,
        //     "user_id" => $auth_user ? $auth_user->id : null,
        //     "customer_id" => null,
        //     "payment_method" => $request->payment_method,
        //     "amount" => 0,
        //     // "bkash_number" => $request->bkash_number,
        //     // "trx_id" => $request->bkash_trx_id,
        //     // "amount" => rand($order->total_price - round($order->total_price / 2), $order->total_price),
        //     // "amount" => $order->total,
        // ]);

        // if ($request->payment_method == 'bkash') {
        //     $payment->number = $request->bkash_number;
        //     $payment->trx_id = $request->bkash_trx_id;
        //     $payment->amount = $order->total_price;
        //     $payment->save();
        // }

        // if ($request->payment_method == 'bank') {
        //     $payment->account_no = $request->bank_account_no;
        //     $payment->trx_id = $request->bank_transaction_id;
        //     $payment->amount = $order->total_price;
        //     $payment->save();
        // }
    }

    public function save_address($request)
    {
        $auth_user = auth()->check() ? auth()->user() : null;
        $address = null;
        $request = (object) $request;
        if ($auth_user) {
            $address = Address::where('table_name', 'users')->where('table_id', $auth_user->id)->orderBy('id', 'DESC')->first();
            if (!$address) {
                $address = new Address();
            }
        }

        $address->fill([
            "table_name" => $auth_user ? "users" : "guest",
            "table_id" => $auth_user ? $auth_user->id : null,
            "address_type" => "shipping",
            "first_name" => $request->full_name ?? '',
            "last_name" => $request->last_name ?? '',
            "mobile_number" => $request->mobile_number ?? '',
            "email" => $request->email ?? '',
            "address" => $request->address ?? '',
            "city" => $request->city ?? '',
            "state" => $request->state ?? '',
            "zip_code" => $request->zip_code ?? '',
            "zone" => $request->zone ?? '',
            "country" => $request->country ?? '',
            "comment" => $request->comment ?? '',
        ])->save();

        return $address;
    }

    public function make_message($message_products, $sub_total_cost, $shipping_cost, $coupon_discount, $total_cost, $name, $mobile_number, $address, $invoice_id)
    {
        $now = Carbon::now()->format("d M, Y h:i a");
        $invoice_url = url('/invoice') . "/" . $invoice_id;
        $this->message .= "আসসালামু আলাইকুম ওয়ারহমাতুল্লাহ। \n";
        $this->message .= "নতুন অর্ডার এসেছে \n";
        $this->message .= "অর্ডার এর সময়:  $now \n";
        $this->message .= "অর্ডার এর বিবরণ \n";

        $this->message .= "------------------- \n";
        $this->message .= $message_products;

        $this->message .= "------------------- \n";
        $this->message .= enToBn("সাবটোটাল - ৳ $sub_total_cost \n");
        $this->message .= enToBn("ডেলিভারি চার্জ - ৳ $shipping_cost \n");
        if ($coupon_discount) {
            $this->message .= enToBn("কুপন ছাড় - ৳ -$coupon_discount \n");
        }
        $this->message .= enToBn("সর্বমোট মূল্য - ৳ $total_cost \n");

        $this->message .= "------------------- \n";
        $this->message .= "অর্ডারকারীর বিবরণ \n";
        $this->message .= "নাম : $name \n";
        $this->message .= "মোবাইল নাম্বার : $mobile_number \n";
        $this->message .= "ঠিকানা : $address \n";
        $this->message .= "------------------- \n";
        $this->message .= "বিস্তারিত : $invoice_url";
        $this->send_telegram($this->message);
    }

    public function validate_coupon($coupon_code, $total_cost)
    {
        $order_coupon_id = 0;
        $coupon_discount = 0;
        $coupon = OrderCoupon::where('coupon_code', $coupon_code)->whereDate('expire_date', '>', Carbon::now()->toDateTimeString())->first();
        if ($coupon) {
            $order_coupon_id = $coupon->id;
            if ($coupon->flat_amount) {
                $coupon_discount = $coupon->flat_amount;
            }
            if ($coupon->discount_amount) {
                $coupon_discount = round($total_cost * $coupon->discount_amount / 100);
            }
        }
        return [
            "order_coupon_id" => $order_coupon_id,
            "coupon_discount" => $coupon_discount,
        ];
    }

    public function apply_coupon()
    {
        $validator = Validator::make(request()->all(), [
            "coupon" => ["required", "exists:order_coupons,coupon_code"],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'data' => $validator->errors(),
            ], 422);
        }

        $coupon = OrderCoupon::where('coupon_code', request()->coupon)->whereDate('expire_date', '>', Carbon::now()->toDateTimeString())->first();

        if (!$coupon) {
            return response()->json([
                'err_message' => 'Coupon expired',
                'data' => ["coupon" => ['this coupon has been expired.']],
            ], 422);
        }

        return [
            "title" => $coupon->title,
            "discount_amount" => $coupon->discount_amount,
            "flat_amount" => $coupon->flat_amount,
        ];
    }

    public function send_telegram($message)
    {
        $bot_token = env('BOT_TOKEN');
        $method = "sendMessage";

        $parameters = [
            'chat_id' => env('BOT_ID'),
            'text' => $message,
        ];

        $url = "https://api.telegram.org/bot$bot_token/$method";

        $response = Http::get($url . '?chat_id=' . $parameters['chat_id'] . '&text=' . $parameters['text']);
        return $response->json();
    }

    public function pay_due()
    {
        $validator = Validator::make(request()->all(), [
            "payment_method" => ["required", 'exists:app_setting_values,title'],
            "number" => ["required", 'exists:app_setting_values,setting_value'],
            "invoice_id" => ["required", 'exists:orders,invoice_id'],
            "trx_id" => ["required"],
            "amount" => ["required"],
        ], [
            "number.required" => ["No payment number is selected."],
            "trx_id.required" => ["No transaction ID is given."],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'data' => $validator->errors(),
            ], 422);
        }

        $order = Order::where('invoice_id', request()->invoice_id)->first();
        $order_payment = OrderPayment::create([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'payment_method' => request()->payment_method,
            'number' => request()->number,
            // 'account_no' => request()->account_no,
            'trx_id' => request()->trx_id,
            'amount' => request()->amount,
            'date' => Carbon::now()->toDateString(),
        ]);
        // if (request()->payment_method == 'bank_account') {
        //     $order_payment->account_no = request()->number;
        //     $order_payment->save();
        // }
        $order->total_paid = $order->order_payments()->sum('amount');
        $order->save();
        return $order->total_paid;
    }
}
