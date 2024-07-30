<div>

    <link rel="stylesheet" href="/css/select2.css">
    <script src="/js/select2.js"></script>
    
    <div class="breadcrumbs-area mb-70">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="breadcrumbs-menu">
						<ul>
							<li><a href="#">Home</a></li>
							<li><a href="#" class="active">checkout</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="entry-header-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="entry-header-title">
						<h2 class="hind-siliguri">ক্রয় চূড়ান্ত করুন</h2>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="checkout-area mb-70">
        <div class="pt-2 section-space shop-checkout-area">
            <div class="container">
                <form class="checkout-content" id="checkout-form" onsubmit="checkout_submit(event)" method="post">
                    <div class="row">
                        <div class="col-md-4 col-sm-12 mb-3">
                            <div class="card checkout-section checkout-box h-100">
                                <div class="section-head">
                                    <h2><span>1</span>Customer Information</h2>
                                </div>
                                <div class="address">
                                    <div class="multiple-form-group">
                                        <div class="form-group">
                                            <label class="control-label" for="input-firstname">First Name</label>
                                            <input value="{{$address->first_name}}" class="form-control" name="first_name" type="text" id="input-firstname" placeholder="First Name*" />
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="input-lastname">Last Name</label>
                                            <input value="{{$address->last_name}}" type="text" id="input-lastname" name="last_name" class="form-control" placeholder="Last Name*" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="input-address">Address</label>
                                        <input value="{{$address->address}}" type="text" id="input-address" name="address" class="form-control" placeholder="Address*" />
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="input-telephone">Mobile</label>
                                        <input value="{{$address->mobile_number}}" type="tel" id="input-telephone" name="mobile_number" class="form-control" placeholder="Telephone*" />
                                    </div>
                                    <div class="form-group" for="input-email">
                                        <label class="control-label">Email</label>
                                        <input value="{{$address->email}}" type="email" id="input-email" name="email" class="form-control" placeholder="E-Mail*" />
                                    </div>
                                    <div class="multiple-form-group">
                                        <div class="form-group" for="input-city">
                                            <label class="control-label">City</label>
                                            <input value="{{$address->city}}" type="text" id="input-city" name="city" class="form-control" placeholder="City*" />
                                        </div>
                                        <div class="form-group" for="input-zone">
                                            <label class="control-label">Zone</label>
                                            <select id="input-zone" name="zone" class="form-control">
                                                <option value="Dhaka City" selected=""> Dhaka City</option>
                                                <option value="Khulna City"> Khulna City</option>
                                                <option value="Rangpur City"> Rangpur City</option>
                                                <option value="Chittagong City">Chittagong City</option>
                                                <option value="Gazipur City">Gazipur City</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Comment</label>
                                        <textarea class="form-control" name="comment" placeholder="Comment" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 col-sm-12">
                            <div class="row row-payment-delivery-order">
                                <div class="col-md-12 col-lg-6 col-sm-12 payment-methods">
                                    <div class="card checkout-section checkout-box">
                                        <div class="section-head">
                                            <h2><span>2</span>Payment Method</h2>
                                        </div>
                                        <div class="">
                                            <p>Select a payment method</p>
                                            <label class="radio-inline">
                                                <input type="radio" onchange="checkout.set_payment_method('cashon')" id="cod_btn" name="payment_method" value="cod" checked />
                                                Cash on Delivery
                                            </label>
                                            <br />
                                            <label class="radio-inline">
                                                <input type="radio" onchange="checkout.set_payment_method('bkash')" id="bkash_btn" name="payment_method"
                                                    value="bkash" />
                                                Bkash
                                            </label>
                                            {{-- <br />
                                            <label class="radio-inline">
                                                <input type="radio" onchange="checkout.set_payment_method('bank')" id="bank_transfer_btn" name="payment_method"
                                                    value="bank" />
                                                Bank Transfer
                                            </label> --}}
                                            <br />
                                            <div id="bkash_section"
                                                class="border border-1 rounded-1 my-2 p-2 d-none">
                                                <p class="mb-3">
                                                    অনুগ্রহ করে আপনার বিকাশ ‘পেমেন্ট অপশন’ থেকে আপনার পেমেন্ট
                                                    কমপ্লিট করুন। তারপর নিচের ফর্মটি ফিলাপ করুন। আমাদের বিকাশ
                                                    একাউন্টে টাকা পাঠানোর নিয়মঃ
                                                </p>
                                                <ul class="mb-3">
                                                    <li class="d-flex gap-2">
                                                        <span>১।</span>
                                                        <span>*247# ডায়াল করে বিকাশ মোবাইল মেন্যুতে যান</span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>২।</span>
                                                        <span>"Payment" অপশন সিলেক্ট করুন।</span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>৩।</span>
                                                        <span>
                                                            Enter Merchant Bkash account এ : বিকাশ নাম্বারটি লিখুন
                                                        </span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>৪।</span>
                                                        <span>
                                                            Amount এ আপনার বিল এমাউন্টটি লিখুন।
                                                        </span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>৫।</span>
                                                        <span>Enter Reference এ আপনার নামের প্রথম শব্দ লিখুন।</span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>৬।</span>
                                                        <span>Enter counter number এ 1 লিখুন।</span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span> ৭।</span>
                                                        <span>
                                                            আপনার বিকাশ মোবাইল মেন্যু পিনটি দিয়ে লেনদেনটি সম্পন্ন করুন।
                                                        </span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <b>bKash Agent No : </b>
                                                        <ul>
                                                            @php
                                                                $bkash_numbers = \App\Models\Account\Account::where('name','bkash')->first()->numbers()->get();
                                                            @endphp
                                                            @foreach ($bkash_numbers as $item)
                                                                <li style="cursor: pointer;">
                                                                    <b onclick="bkash_number.value=`{{$item->value}}`">{{$item->value}}</b>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                    <div class="form-group">
                                                        <label class="control-label" for="input-firstname">
                                                            <b>BKash Number: </b>
                                                        </label>
                                                        <input class="form-control" name="bkash_number" type="text" id="bkash_number" placeholder="013******" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label" for="input-firstname">
                                                            <b>BKash transaction ID: </b>
                                                        </label>
                                                        <input class="form-control" name="bkash_trx_id" type="text" id="bkash_trx" placeholder="TRX-4548" />
                                                    </div>
                                                </ul>
                                            </div>

                                            {{-- <div id="bank_section"
                                                class="border border-1 rounded-1 my-2 p-2 d-none">
                                                <p class="mb-3">
                                                    Please go to your personal bank or log into your online banking
                                                    portal, and follow the guideline:
                                                </p>
                                                <ul class="mb-3">
                                                    <li class="d-flex gap-2">
                                                        <span>1।</span>
                                                        <span>
                                                            specify that you
                                                            would like to send your funds to another bank account,
                                                            and provides the bank account details obtained when they
                                                            submitted the order.
                                                        </span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>2।</span>
                                                        <span>
                                                            Send the funds
                                                            and the bank transfer has been initiated.
                                                        </span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>3।</span>
                                                        <span>
                                                            The payment is
                                                            complete when we payment reaches the receiving
                                                            account.
                                                        </span>
                                                    </li>
                                                    <li class="d-flex gap-2">
                                                        <span>4।</span>
                                                        <span>
                                                            Provide us the
                                                            transaction ID
                                                        </span>
                                                    </li>
                                                    <div class="form-group">
                                                        <label class="control-label" for="input-firstname">
                                                            <b>Bank account no: </b>
                                                        </label>
                                                        <input class="form-control" name="bank_account_no" type="text" id="bank_ac_no" placeholder="013******" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label" for="input-firstname"><b>Transaction ID: </b></label>
                                                        <input class="form-control" name="bank_transaction_id" type="text" id="bank_trx_no" placeholder="TRX-4548" />
                                                    </div>
                                                </ul>
                                            </div> --}}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 col-sm-12 delivery-methods">
                                    <div class="card checkout-section">
                                        <div class="section-head">
                                            <h2><span>3</span>Delivery Method</h2>
                                        </div>
                                        <div class="">
                                            <p>Select a delivery method</p>
                                            <label class="radio-inline">
                                                @php
                                                    $setting = \App\Models\Settings\AppSettingTitle::class;
                                                    $home_delivery = $setting::getValue('home_delivery_cost');
                                                    $out_dhaka_home_delivery_cost = $setting::getValue('out_dhaka_home_delivery_cost');
                                                @endphp
                                                <input type="radio" onchange="delivery_method.set(`home_delivery`)"
                                                    data-charge="{{$home_delivery}}"
                                                    name="shipping_method" value="home_delivery" />
                                                Home Delivery - {{$home_delivery}}৳
                                            </label>
                                            <br />
                                            <label class="radio-inline">
                                                <input type="radio" onchange="delivery_method.set(`pickup`)" data-charge="0" name="shipping_method" checked value="pickup" />
                                                Store Pickup - 0৳
                                            </label>
                                            <br />
                                            <label class="radio-inline">
                                                <input type="radio" onchange="delivery_method.set(`outside_dhaka`)" data-charge="{{$out_dhaka_home_delivery_cost}}" name="shipping_method" value="outside_dhaka" />
                                                Home Delivery outside Dhaka - {{$out_dhaka_home_delivery_cost}}৳
                                            </label>
                                            <br />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="checkout-section card checkout-box voucher-coupon p-1">
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- <div class="col-md-6 col-sm-12" id="gift-voucher">
                                                    <div class="input-group">
                                                        <input type="text" name="voucher" placeholder="Gift Voucher"
                                                            id="input-voucher" class="form-control" />
                                                        <span class="input-group-btn"><button type="button"
                                                                id="button-voucher" data-loading-text="Loading..."
                                                                class="btn st-outline">Apply Voucher</button></span>
                                                    </div>
                                                </div> --}}
                                                <div class="col-md-12" id="discount-coupon">
                                                    <div class="input-group gap-2">
                                                        <div style="flex:1;">
                                                            <input type="text" name="coupon" onchange="apply_coupon()" placeholder="Promo / Coupon Code" id="input_coupon" class="form-control" />
                                                        </div>
                                                        <span class="input-group-btn">
                                                            <button type="button" onclick="apply_coupon()" id="button-coupon" data-loading-text="Loading..." class="btn btn-outline-info">
                                                                Apply Coupon
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 details-section-wrap">
                                    <div class="card checkout-section checkout-box">
                                        <div class="section-head">
                                            <h2><span>4</span>Order Overview</h2>
                                        </div>
                                        <div>
                                            <div id="carts"></div>
                                        </div>
                                        <div class="card-body">
                                            <table class="table check_out_cart_list table-bordered bg-white checkout-data">
                                                <thead>
                                                    <tr>
                                                        <td>Product Name</td>
                                                        <td>Price</td>
                                                        <td class="text-right">Total</td>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr class="total">
                                                        <td colspan="2" class="text-right">
                                                            <strong>Sub-Total:</strong>
                                                        </td>
                                                        <input type="hidden" name="cart_total" value="" id="cart_total">
                                                        <td class="text-right">
                                                            <span class="amount">
                                                                ৳
                                                                <span class="check_out_cart_list_subtotal solaiman">0</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr class="total delivery_cost_row">
                                                        <td colspan="2" class="text-right">
                                                            <strong>Delivery Cost:</strong>
                                                        </td>
                                                        <td class="text-right">
                                                            <span class="amount solaiman">
                                                                ৳ <span data-cost="0" id="delivery_cost">0</span>
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    <tr class="total">
                                                        <td colspan="2" class="text-right">
                                                            <strong>Total:</strong>
                                                        </td>
                                                        <input type="hidden" name="order_total" value="" id="order_total">
                                                        <td class="text-right">
                                                            <span class="amount">
                                                                ৳
                                                                <span class="check_out_cart_list_total solaiman">0</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="checkout-final-action mt-3">
                        <div class="agree-text" style="margin-bottom: 10px;">
                            <input type="checkbox" name="agree" value="1" checked="checked" />
                            I have read and agree to the
                            <a href="#">
                                <b>Terms and Conditions</b>
                            </a>,
                            <a href="#">
                                <b>Privacy Policy</b>
                            </a> and
                            <a href="#">
                                <b>Refund and Return Policy</b>
                            </a>
                        </div>
                        <button id="button-confirm" class="btn submit-btn pull-right order_btn" type="submit">
                            Confirm Order
                        </button>
                    </div>
                </form>
                <script>
                    cart.render_check_out_cart_list();
                </script>
            </div>
        </div>
	</div>
</div>
