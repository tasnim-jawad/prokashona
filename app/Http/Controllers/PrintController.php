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
use App\Models\Production\SupplierBinding;
use App\Models\Production\SupplierPaperStock;

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
        // $date = request()->all()['date'];
        $products = Product::all();
        $product_stock_data = [];
        $total_stock = 0;
        $total_seles = 0;
        $total_new_stock = 0;

        foreach($products as $product){
            $isProduct =  ProductStockLog::where('product_id',$product->id)->exists();
            if($isProduct){
                $product_stock = ProductStockLog::where('product_id',$product->id)
                                            ->whereIn('type', ['initial', 'production'])
                                            ->orderBy('updated_at', 'desc')
                                            ->get();
                $product_sales = ProductStockLog::where('product_id',$product->id)
                                            ->where('type', 'sales')
                                            ->orderBy('updated_at', 'desc')
                                            ->get();

                $product_stock_sum = $product_stock->sum('qty');
                $product_sales_sum = $product_sales->sum('qty');

                $new_stock = $product_stock_sum - $product_sales_sum;
                $stock_update_date = $product_stock->isNotEmpty() ? $product_stock->first()->updated_at : null;;

                $obj = new \stdClass();
                $obj->product_name = $product->product_name_english;
                $obj->product_stock = $product_stock_sum;
                $obj->product_seles = $product_sales_sum;
                $obj->new_stock =  $new_stock;
                $obj->stock_update_date = Carbon::parse($stock_update_date)->format('d-M-Y');
                $product_stock_data[] = $obj;

                $total_stock += $product_stock_sum;
                $total_seles += $product_sales_sum;
                $total_new_stock += $new_stock;
            }

        }
        // dd($product_stock_data);
        return view('publication.datewise_add_stock')
                            ->with('product_stock_data',$product_stock_data)
                            ->with('total_stock',$total_stock)
                            ->with('total_seles',$total_seles)
                            ->with('total_new_stock',$total_new_stock);
    }
}
