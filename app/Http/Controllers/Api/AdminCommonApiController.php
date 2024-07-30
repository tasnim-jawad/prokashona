<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Models\Order\OrderDetails;
use App\Models\Product\Product;
use App\Models\Production\Production;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminCommonApiController extends Controller
{
    public function all_stock_out_products()
    {
        $products = Product::select(Product::$common_selected_fields)
            ->whereRaw('total_stock <= stock_alert_qty')
            ->where('is_in_stock', 0)
            ->orderBy('total_stock', 'ASC')
            ->get();
        return response()->json($products);
    }

    public function all_stock_in_products()
    {
        $products = Product::select(Product::$common_selected_fields)
            ->whereRaw('total_stock > stock_alert_qty')
            ->orderBy('total_stock', 'ASC')
            ->where('is_in_stock', 1)->get();
        return response()->json($products);
    }

    public function product_report()
    {
        $from = Carbon::parse(request()->from);
        $to = Carbon::parse(request()->to);

        $formated_from = $from->copy()->toDateString();
        $formated_to = $to->copy()->toDateString();

        $diff = $from->diffInMonths($to);
        $report = [];
        $products = Product::select(Product::$common_selected_fields)->get();
        foreach ($products as $product) {
            $temp = [];
            $temp["product"] = $product->product_name;

            $temp["total"]["production"] = Production::where('product_id', $product->id)
                ->whereBetween('created_at', [$formated_from, $formated_to])
                ->where('is_complete', 1)
                ->sum('final_print_qty');;

            $temp["total"]["sales"] = OrderDetails::query()
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$formated_from, $formated_to])
                ->sum('qty');;

            for ($month_no = 0; $month_no <= $diff; $month_no++) {
                $get_month = $to->copy()->subMonthNoOverflow($month_no)->format('m');
                $get_year = $to->copy()->subMonthNoOverflow($month_no)->format('Y');
                $index = $to->copy()->subMonthNoOverflow($month_no)->format('M y');

                $temp[$index]["sales"] = OrderDetails::query()
                    ->where('product_id', $product->id)
                    ->whereMonth('created_at', $get_month)
                    ->whereYear('created_at', $get_year)
                    ->sum('qty');

                $temp[$index]["production"] = Production::where('product_id', $product->id)
                    ->whereYear('created_at', $get_year)
                    ->whereMonth('created_at', $get_month)
                    ->where('is_complete', 1)
                    ->sum('final_print_qty');
            }
            $report[] = $temp;
        }

        return $report;
    }

    function branch_product_report()
    {
        $user_id = request()->user_id;
        $from = request()->from;
        $to = request()->to;

        $report = [];

        $query = OrderDetails::select(['product_id'])
            ->unique('product_id');

        if ($user_id) {
            $query->where('user_id', $user_id);
            $query->addSelect('user_id');
        }
        $products = $query->get()->unique('product_id');

        foreach ($products as $product) {
            $product = Product::where('id', $product->product_id)->first();
            if ($product) {
                $query = OrderDetails::where('product_id', $product->id);
                if ($user_id) {
                    $query->where('user_id', $user_id);
                }
                $orders = $query->get();
                $temp = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'qty' => OrderDetails::where('product_id', $product->id)->sum('qty'),
                    'price' => 0,
                    'current_price' => $product->discount_info->discount_price,
                ];
                foreach ($orders as $order) {
                    $temp['price'] += $order->qty * $order->discount_price;
                }
                $report[] = $temp;
            }
        }

        return $report;
    }

    function branch_product_report_details()
    {
        $user_id = request()->user_id;
        $from = request()->from;
        $to = request()->to;

        $report = [];

        $query = OrderDetails::select(['product_id'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if ($user_id) {
            $query->where('user_id', $user_id);
            $query->addSelect('user_id');
        }

        $products = $query->get()->unique('product_id');

        $si = 1;
        $total_qty = 0;
        $total_price = 0;
        foreach ($products as $product) {
            $product = Product::where('id', $product->product_id)->first();
            if ($product) {
                $query = OrderDetails::where('product_id', $product->id)
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);

                if ($user_id) {
                    $query->where('user_id', $user_id);
                }

                $orders = $query->get();

                $order_qty = 0;
                $order_price = 0;
                foreach ($orders as $order) {
                    $temp = [
                        'si' => $si++,
                        'product_id' => $product->id,
                        'product_name' => $product->product_name,
                        'qty' => $order->qty,
                        'price' => $order->discount_price,
                        'current_price' => $order->sales_price,
                        'date' => $order->created_at->format('d-M-y'),
                    ];
                    $order_qty += $order->qty;
                    $order_price += $order->discount_price;
                    $report[] = $temp;

                    $total_qty += $order->qty;
                    $total_price += $order->discount_price;
                }

                $report[] = [
                    'si' => '',
                    'product_id' => $product->id,
                    'product_name' => "total",
                    'qty' => $order_qty,
                    'price' => $order_price,
                    'current_price' => '',
                    'date' => '',

                ];
            }
        }

        return [
            "report" => $report,
            "total_qty" => $total_qty,
            "total_price" => $total_price,
        ];
    }

    function sales_statements()
    {
        $from = request()->from;
        $to = request()->to;

        $orders = Order::whereDate('invoice_date', '>=', $from)->whereDate('invoice_date', '<=', $to)
            ->where('sales_id', '>', 0)
            ->with('user')
            ->withCount('products')
            ->get();

        $total_price = Order::whereDate('invoice_date', '>=', $from)->whereDate('invoice_date', '<=', $to)
            ->where('sales_id', '>', 0)
            ->sum('total_price');

        $total_qty = 0;
        foreach ($orders as $i) {
            $total_qty += $i->products_count;
            $i->branch_name = $i->user->first_name;
        }

        return [
            "orders" => $orders,
            "total_price" => $total_price,
            "total_qty" => $total_qty,
        ];
    }
}
