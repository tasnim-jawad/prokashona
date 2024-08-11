<?php

use App\Models\Account\AccountLog;
use App\Models\Order\Order;
use App\Models\Order\OrderDetails;
use App\Models\Order\OrderPayment;
use App\Models\Payment\UserPayment;
use App\Models\Payment\UserPaymentRemain;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Route;


Route::get('/home', function () {
    return "home";
});

Route::group(['prefix' => '', 'namespace' => "Livewire"], function () {
    Route::get('/', "Frontend\Home");
    Route::get('category/{category}/{name}', "Frontend\CategoryProduct")->name('category_product');
    Route::get('product/{product}/{name}', "Frontend\ProductDetails")->name('product_details');
    Route::get('cart', "Frontend\Cart")->name('cart');
    Route::get('checkout', "Frontend\Checkout")->name('checkout');
    Route::get('invoice/{invoice}', "Frontend\Invoice")->name('invoice');
    Route::get('/profile', "Frontend\CustomerProfile");

    Route::get('/login', "Login")->name('login');
    Route::get('/register', "Register")->name('register');

    // Route::get('pos', "Frontend\Pos")->name('pos');
    Route::get('contact', "Frontend\Contact")->name('contact');
    Route::get('/frequently-asked-questions', "Frontend\Faq");
    Route::get('/how-to-buy', "Frontend\Howtobuy");
    Route::get('/terms', "Frontend\Terms");
    Route::get('/refund-policy', "Frontend\RefundPolicy");
    Route::get('/privacy-policy', "Frontend\PrivacyPolicy");

    Route::get('/about-us', "Frontend\About");
});

Route::group(['prefix' => '', 'namespace' => "Controllers"], function () {
    Route::post('/checkout', 'CheckoutController@checkout');
    Route::post('/checkout/pay-due', 'CheckoutController@pay_due');
    Route::post('/apply-coupon', 'CheckoutController@apply_coupon');
    Route::get('/pos', 'WebsiteController@pos');

    Route::group(['prefix' => 'json'], function () {
        Route::get('/products', 'Json\ProductJsonController@products');
    });

    Route::post('/profile/address-update', 'Common\AddressController@update_from_frontend');
    Route::post('/profile/update', 'Auth\ApiLoginController@update_profile');
    Route::post('/profile/update-profile-pic', 'Auth\ApiLoginController@update_profile_pic');

    Route::post('/profile/update-profile-pic', 'Auth\ApiLoginController@update_profile_pic');
    Route::post('/contact-submit', 'WebsiteController@contact_submit');

    Route::get('/invoice-printout/{order}', 'Admin\Order\OrderPrintoutController@sales_invoice');

    Route::post('/register', 'WebsiteController@website_register');

    Route::get('/old-categories', 'OldDataImportController@old_categories');
    Route::get('/old-products', 'OldDataImportController@old_products');

    Route::get('/payment/{invoice}', 'Payment\BkashController@payment')->name('payment');
    Route::get('/payment/{invoice}/status', 'Payment\BkashController@status')->name('payment_status');
    Route::get('/payment/{invoice}/success', 'Payment\BkashController@success')->name('payment_success');
    Route::get('/payment/{invoice}/failed', 'Payment\BkashController@failed')->name('payment_failed');

    // ----------------------
    // -------Tasnimul Hasan----------
    // ----------------------



    // ----------------------
    // -------Tasnimul Hasan----------
    // ----------------------

    // Route::get('/json-to-db', function () {
    //     $file = file_get_contents(public_path('jsons/products.json'));
    //     $data = collect(json_decode($file));
    //     $file2 = file_get_contents(public_path('jsons/products_with_name.json'));
    //     $data2 = json_decode($file2);
    //     return view('product_upload.json_to_db', compact('data', 'data2'));
    // });

    // Route::get('/customer-json-to-db', 'OldDataImportController@users');

    Route::get('/reset', "WebsiteController@reset");
});

Route::get('/dashboard', function () {
    return view('backend.dashboard');
})->name('dashboard');

Route::get('/old-users', function () {
    $conn = new mysqli("localhost", "root", "", "alzmariz_prokasona");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM products WHERE `add_to_front` = 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            try {
                // dd($row);
                // $product = Product::where('product_name', $row->product_title)->first();
                // if ($product) {
                //     $product->is_top_product = 1;
                //     $product->save();
                // }
            } catch (\Throwable $th) {
                //throw $th;
                dump($row);
            }
        }
    } else {
        echo "0 results";
    }
    $conn->close();
});

Route::get('/set_prices', function () {
    $file = file_get_contents(public_path('jsons/product_json_1_jun.json'));
    $data = collect(json_decode($file));

    $not_found = [];
    foreach ($data as $item) {
        $product  = Product::where('product_name', $item->product_name)->first();
        if ($product) {
            // $product->cost = $item->producttion_cost;
            // $product->sales_price = $item->sales_price;
            // $product->stock_alert_qty = $item->stock_alert_qty;
            // $product->total_stock = 0;
            // $product->total_sales = 0;
            // $product->save();

            // if($item->sales_price < $item->producttion_cost){
            //     $parcent_discount = 100 - ( 100 * $item->sales_price / $item->producttion_cost);
            //     $flat_discount = $item->producttion_cost - $item->sales_price;
            //     $product->discount_parcent = $parcent_discount;
            //     $product->discount_flat = $flat_discount;
            //     $product->save();
            //     // dd($product, $parcent_discount);
            // }

            if ($item->total_stock) {
                $product->total_stock = $product->stocks()->sum('qty');
                $product->save();
                // ProductStockLog::create([
                //     "product_id" => $product->id,
                //     "qty" => $item->total_stock,
                //     "type" => "production",
                // ]);
            }
        } else {
            $not_found[] = $item;
        }
    }
    dd($item);
});


Route::get('/clean-db', function () {
    Order::where('user_id', 5)->delete();
    OrderDetails::where('user_id', 5)->delete();
    OrderPayment::where('user_id', 5)->delete();
    UserPayment::where('user_id', 5)->delete();
    UserPaymentRemain::where('user_id', 5)->delete();
    AccountLog::where('customer_id', 5)->delete();

    // AccountLog::truncate();
    // AccountSupplierLog::truncate();

    // OrderCoupon::truncate();
    // OrderDeliveryInfo::truncate();
    // OrderDetails::truncate();
    // OrderPaymentAttachment::truncate();
    // OrderPayment::truncate();
    // OrderVariant::truncate();
    // Order::truncate();

    // ProductStockLog::truncate();
    // Product::where('id', '>', 0)->update(['total_stock' => 0, 'total_sales' => 0]);

    // ProductionCost::truncate();
    // ProductionStatus::truncate();
    // ProductionUsedPaper::truncate();
    // Production::truncate();

    // return redirect()->back();
});


// Route::get('/data-reload', function () {
//     \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true]);
//     \Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations', '--force' => true]);
//     \Illuminate\Support\Facades\Artisan::call('passport:install');
//     return redirect()->back();
// });


Route::group(['prefix' => 'print', 'namespace' => "Controllers"], function () {
    Route::get('/daily-sales-statement', 'PrintController@daily_sales_statement');
    Route::post('/daily-sales-statement-printout', 'PrintController@daily_sales_statement_printout')->name('daily_sales_statement_printout');

    Route::get('/supplier-summary', 'PrintController@supplier_summary');
    Route::post('/supplier-summary-printout', 'PrintController@supplier_summary_printout')->name('supplier_summary_printout');

    Route::get('/datewise-add-stock', 'PrintController@datewise_add_stock');
    Route::post('/datewise-add-stock-printout', 'PrintController@datewise_add_stock_printout')->name('datewise_add_stock_printout');

    Route::get('/client-dues', 'PrintController@client_dues');
    // Route::post('/client-dues-printout', 'PrintController@client_dues_printout')->name('client_dues_printout');

    Route::get('/due-collection/{id}', 'PrintController@due_collection');
    // Route::post('/due-collection-printout', 'PrintController@due_collection_printout')->name('due_collection_printout');

    Route::get('/daily-add-product', 'PrintController@daily_add_product');
    Route::post('/daily-add-product-printout', 'PrintController@daily_add_product_printout')->name('daily_add_product_printout');

    // Route::get('/due_collection', function(){
    //     return view('publication.due_collection');
    // });

    Route::get('/daily_add_product', function(){
        return view('publication.daily_add_product');
    });
});
