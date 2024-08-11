<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Product\ProductStockLog;
use App\Models\Production\SupplierPaper;
use App\Models\Production\SupplierPrint;
use App\Models\Account\AccountSupplierLog;
use App\Models\Order\OrderDetails;
use App\Models\Payment\UserPayment;
use App\Models\Production\SupplierBinding;
use App\Models\Production\SupplierPaperStock;
use App\Models\User;

class PrintController extends Controller
{
    public function daily_sales_statement(){
        return view('publication.daily_sales_statement');
    }

    public function daily_sales_statement_printout(){
        $date = request()->all()['date'];
        $data = Order::whereDate('invoice_date',$date)->with('user')->get();

        return view('publication.daily_sales_statement',compact('data'));
    }

    public function supplier_summary(){
        return view('publication.supplier_summary');
    }
    public function supplier_summary_printout(){
        $date = request()->all()['date'];

        $binding_type_supplier_data = [];
        $paper_type_supplier_data = [];
        $print_type_supplier_data = [];
        $total_opening = 0;
        $total_bill = 0;
        $total_payment = 0;
        $total_balance = 0;

        $sup_binding = SupplierBinding::all();
        foreach($sup_binding as $single){
            $is_binding_supplier_id = AccountSupplierLog::where('supplier_type', 'binding')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->exists();
            if($is_binding_supplier_id){
                $supplier_binding_opening = AccountSupplierLog::where('supplier_type', 'binding')
                                                    ->where('supplier_id', $single->id)
                                                    ->whereDate('date',$date)
                                                    ->where('payment_type','opening')->get();
                $supplier_binding_bill = AccountSupplierLog::where('supplier_type', 'binding')
                                                    ->where('supplier_id', $single->id)
                                                    ->whereDate('date',$date)
                                                    ->where('payment_type','bill')->get();
                $supplier_binding_payment =AccountSupplierLog::where('supplier_type', 'binding')
                                                    ->where('supplier_id', $single->id)
                                                    ->whereDate('date',$date)
                                                    ->where('payment_type','payment')->get();
                $supplier_binding_balance = ( $supplier_binding_opening->sum('amount') + $supplier_binding_bill->sum('amount') )
                                            - $supplier_binding_payment->sum('amount');
                $supplier_binding_remarks = $supplier_binding_balance > 0 ? 'Due' : 'Advance';

                $obj = new \stdClass();
                $obj->company_name = $single->company_name;
                $obj->opening = $supplier_binding_opening->sum('amount');
                $obj->bill = $supplier_binding_bill->sum('amount');
                $obj->payment = $supplier_binding_payment->sum('amount');
                $obj->balance = abs($supplier_binding_balance);
                $obj->remarks = $supplier_binding_remarks;

                $binding_type_supplier_data[] = $obj;

                $total_opening += $supplier_binding_opening->sum('amount');
                $total_bill += $supplier_binding_bill->sum('amount');
                $total_payment += $supplier_binding_payment->sum('amount');
                $total_balance += $supplier_binding_balance;
            }
        }

        $sup_paper = SupplierPaper::all();
        foreach($sup_paper as $single){
            $is_paper_supplier_id = AccountSupplierLog::where('supplier_type', 'paper')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->exists();

            if($is_paper_supplier_id){
                $supplier_paper_opening = AccountSupplierLog::where('supplier_type', 'paper')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->where('payment_type','opening')->get();
                $supplier_paper_bill = AccountSupplierLog::where('supplier_type', 'paper')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->where('payment_type','bill')->get();
                $supplier_paper_payment =AccountSupplierLog::where('supplier_type', 'paper')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->where('payment_type','payment')->get();
                $supplier_paper_balance = ( $supplier_paper_opening->sum('amount') + $supplier_paper_bill->sum('amount') )
                                            - $supplier_paper_payment->sum('amount');
                $supplier_paper_remarks = $supplier_paper_balance > 0 ? 'Due' : 'Advance';

                $obj = new \stdClass();
                $obj->company_name = $single->supplier_name;
                $obj->opening = $supplier_paper_opening->sum('amount');
                $obj->bill = $supplier_paper_bill->sum('amount');
                $obj->payment = $supplier_paper_payment->sum('amount');
                $obj->balance = abs($supplier_paper_balance);
                $obj->remarks = $supplier_paper_remarks;

                $paper_type_supplier_data[] = $obj;

                $total_opening += $supplier_paper_opening->sum('amount');
                $total_bill += $supplier_paper_bill->sum('amount');
                $total_payment += $supplier_paper_payment->sum('amount');
                $total_balance += $supplier_paper_balance;

            }
        }

        $sup_print = SupplierPrint::all();
        foreach($sup_print as $single){
            $is_print_supplier_id = AccountSupplierLog::where('supplier_type', 'print')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->exists();
            if($is_print_supplier_id){
                $supplier_print_opening = AccountSupplierLog::where('supplier_type', 'print')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->where('payment_type','opening')->get();
                $supplier_print_bill = AccountSupplierLog::where('supplier_type', 'print')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->where('payment_type','bill')->get();
                $supplier_print_payment =AccountSupplierLog::where('supplier_type', 'print')
                                        ->where('supplier_id', $single->id)
                                        ->whereDate('date',$date)
                                        ->where('payment_type','payment')->get();
                $supplier_print_balance = ( $supplier_print_opening->sum('amount') + $supplier_print_bill->sum('amount') )
                                            - $supplier_print_payment->sum('amount');
                $supplier_print_remarks = $supplier_print_balance > 0 ? 'Due' : 'Advance';

                $obj = new \stdClass();
                $obj->company_name = $single->company_name;
                $obj->opening = $supplier_print_opening->sum('amount');
                $obj->bill = $supplier_print_bill->sum('amount');
                $obj->payment = $supplier_print_payment->sum('amount');
                $obj->balance = abs($supplier_print_balance);
                $obj->remarks = $supplier_print_remarks;

                $print_type_supplier_data[] = $obj;

                $total_opening += $supplier_print_opening->sum('amount');
                $total_bill += $supplier_print_bill->sum('amount');
                $total_payment += $supplier_print_payment->sum('amount');
                $total_balance += $supplier_print_balance;

            }
        }

        return view('publication.supplier_summary')
                        ->with('binding_type_supplier_data', $binding_type_supplier_data)
                        ->with('paper_type_supplier_data', $paper_type_supplier_data)
                        ->with('print_type_supplier_data', $print_type_supplier_data)
                        ->with('total_opening', $total_opening)
                        ->with('total_bill', $total_bill)
                        ->with('total_payment', $total_payment)
                        ->with('date', $date)
                        ->with('total_balance', $total_balance);
    }


    public function datewise_add_stock(){
        return view('publication.datewise_add_stock');
    }

    public function datewise_add_stock_printout(){
        $date = request()->all()['date'];
        // dd($date);
        $carbon_date = Carbon::parse($date);
        $month = $carbon_date->month;
        $year = $carbon_date->year;
        // dd($date,$carbon_date,$month ,$year);
        $products = Product::all();
        $product_stock_data = [];
        $total_stock = 0;
        $total_sales = 0;
        $total_new_stock = 0;

        foreach($products as $product){
            $isProduct =  ProductStockLog::where('product_id',$product->id)->whereMonth('created_at',$month)->whereYear('created_at',$year)->exists();
            if($isProduct){
                $product_stock = ProductStockLog::where('product_id',$product->id)
                                            ->whereIn('type', ['initial', 'production'])
                                            ->whereYear('created_at', $year)
                                            ->whereMonth('created_at', '<=', $month)
                                            ->orderBy('updated_at', 'desc')
                                            ->get();
                $product_stock_this_month = ProductStockLog::where('product_id',$product->id)
                                            ->whereIn('type', ['initial', 'production'])
                                            ->whereMonth('created_at',$month)->whereYear('created_at',$year)
                                            ->orderBy('updated_at', 'desc')
                                            ->get();
                $product_sales = ProductStockLog::where('product_id',$product->id)
                                            ->where('type', 'sales')
                                            ->whereYear('created_at', $year)
                                            ->whereMonth('created_at', '<=', $month)
                                            ->orderBy('updated_at', 'desc')
                                            ->get();
                $product_sales_this_month = ProductStockLog::where('product_id',$product->id)
                                            ->where('type', 'sales')
                                            ->whereMonth('created_at',$month)->whereYear('created_at',$year)
                                            ->orderBy('updated_at', 'desc')
                                            ->get();

                $product_stock_sum = $product_stock->sum('qty');
                $product_sales_sum = $product_sales->sum('qty');
                $new_stock = $product_stock_sum - $product_sales_sum;

                $previous_product_stock_sum = ($product_stock ? $product_stock->sum('qty') : 0) - ($product_stock_this_month ? $product_stock_this_month->sum('qty') : 0);
                $previous_product_sales_sum = ($product_sales ? $product_sales->sum('qty') : 0) - ($product_sales_this_month ? $product_sales_this_month->sum('qty') : 0);
                $previous_new_stock = $previous_product_stock_sum - $previous_product_sales_sum;

                $obj = new \stdClass();
                $obj->product_name = $product->product_name_english;

                $obj->product_stock = $product_stock_sum;
                $obj->product_sales = $product_sales_sum;
                $obj->new_stock =  $new_stock;

                $obj->previous_product_stock = $previous_product_stock_sum;
                $obj->previous_product_sales = $previous_product_sales_sum;
                $obj->previous_new_stock = $previous_new_stock;

                $obj->product_stock_this_month = $product_stock_this_month;
                $obj->product_sales_this_month = $product_sales_this_month;
                $product_stock_data[] = $obj;

                $total_stock += $product_stock_sum;
                $total_sales += $product_sales_sum;
                $total_new_stock += $new_stock;
            }

        }
        // dd($product_stock_data,$product_stock_data[0]?->product_stock_this_month);
        return view('publication.datewise_add_stock')
                            ->with('date',$date)
                            ->with('product_stock_data',$product_stock_data)
                            ->with('total_stock',$total_stock)
                            ->with('total_sales',$total_sales)
                            ->with('total_new_stock',$total_new_stock);
    }

    public function client_dues(){
        $user_due_info = [];

        $users = User::all();
        foreach($users as $user){
            $isUserPayment = UserPayment::where('user_id',$user->id)->exists();
            if($isUserPayment){
                $user_credit = UserPayment::where('user_id',$user->id)
                                            ->where('type', 'credit')
                                            ->get();
                $user_total_credit = $user_credit->sum('amount');

                $order_details = OrderDetails::where('user_id',$user->id)
                                                ->get();
                $order_details_total = 0;
                foreach ($order_details as $single_order) {
                    $single_order_total = $single_order->qty * $single_order->sales_price;
                    // dd($single_order_total,$user->id);
                    $order_details_total += $single_order_total;
                }
                if($user_total_credit < $order_details_total){
                    $user_due_info[] = (object) [
                        'client_id' => $user->user_name,
                        'client_name' => $user->first_name,
                        'due' => $user_total_credit - $order_details_total,
                    ];
                }
            }
        }

        return view('publication.client_dues')->with('user_due_info',$user_due_info);
    }

    public function due_collection($id){
        $today = Carbon::today();
        $user = User::find($id);
            $isUserPayment = UserPayment::where('user_id',$user->id)->exists();
            if($isUserPayment){
                $user_credit = UserPayment::where('user_id',$user->id)
                                            ->where('type', 'credit')
                                            ->get();
                $user_total_credit = $user_credit->sum('amount');

                $order_details = OrderDetails::where('user_id',$user->id)
                                                ->get();
                $order_details_total = 0;
                foreach ($order_details as $single_order) {
                    $single_order_total = $single_order->qty * $single_order->sales_price;
                    // dd($single_order_total,$user->id);
                    $order_details_total += $single_order_total;
                }

                // $due = 0;
                if($user_total_credit < $order_details_total){
                    $due = $user_total_credit - $order_details_total;
                }else{
                    $due = 0 ;
                }

                $user_debit = UserPayment::where('user_id',$user->id)
                                            ->where('type', 'debit')
                                            ->latest()
                                            ->first()
                                            ->amount;
                // dd($due,$user_debit);

            }

        return view('publication.due_collection')
                                                ->with('user', $user)
                                                ->with('date' , $today)
                                                ->with('due' , $due)
                                                ->with('user_debit' , $user_debit);
    }
    // public function due_collection_printout(){
    //     $user_id = request()->all()['user_id'];
    //     // dd($user_id);
    //     $user_selected = User::find($user_id);
    //     // dd($user,$user_id);
    //     $users = User::all();
    //     return view('publication.due_collection')
    //                                 ->with('users',$users)
    //                                 ->with('user_selected',$user_selected);
    // }

    public function daily_add_product(){
        return view('publication.daily_add_product');
    }
    public function daily_add_product_printout(){
        return view('publication.daily_add_product');
    }
}
