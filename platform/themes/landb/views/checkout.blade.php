<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>Edit Profile</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
    <div class="col-lg-7">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <ul class="nav nav-tabs tabs-product">
                <li class="mt-4"><a class="active" data-toggle="tab" href="#home">Information&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li class="mt-4"><a data-toggle="tab" href="#menu1">Shipping Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li class="mt-4"><a data-toggle="tab" href="#menu2">Billing Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li class="mt-4"><a data-toggle="tab" href="#menu3">Payment</a></li>
            </ul>

            <div class="tab-content product-tab-content">
                <div id="home" class="tab-pane fade in active show">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="profile-light-txt mt-5">Contact information</h4>

                            <div class="row">
                                <div class="col-lg-6">
                                    <p class="textbox-label">First Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->first_name }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Last Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->last_name }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Business Phone</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->business_phone }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Mobile</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->phone }}" />
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Company</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->company }}" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Store’s Facebook</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->store_facebook }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Store’s Instagram</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->store_instagram }}"/>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Store’s Brick & Mortar address</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->mortar_address }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Sales Tax ID</p>
                                    <input class="input-textbox" readonly type="text" value="{{ $user_info->details->sales_tax_id }}"/>
                                </div>
                                <div class="col-lg-6">
                                </div>
                                <div class="col-lg-3 mt-3">
                                    <a href="#" class=" btn cart-btn w-100">Continue</a>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-lg-2"></div>
                        <div class="col-lg-4">
                            <div style="margin-top: 40px;" class="refer-area">
                                <p class="cout-note">Note</p>
                                <p class="cout-note-para mt-3">International shipping doesn’t qualify for free shipping. Free shipping can only be shipped to the 48 contigious states. We regret it cannot be free shipped to APO/FPO, Hawaii, Alaska, or Puerto Rico.</p>
                                <p class="cout-note  mt-5">Your Order</p>
                                <hr>
                                <p class="note-product mt-3">Product</p>
                                @php $grand_total = 0; @endphp
                                @foreach($cart->products as $cartItem)
                                    <div class="row">
                                        <div class="col-lg-8 col-6">
                                            <p class="note-product-name mt-3">{{ $cartItem->product->name }}</p>

                                        </div>
                                        <div class="col-lg-4 col-6">
                                            <p class="note-product-price mt-3">{{ $cartItem->qty }} * $ {{ $cartItem->price }}</p>

                                        </div>
                                    </div>
                                    <p class="note-product-size mt-1">SIZE: 2(S), 2(M), 2(L)</p>
                                    @php $total = $cartItem->qty * $cartItem->price; $grand_total = $grand_total + $total; @endphp
                                @endforeach
                                <div class="row  mt-5">
                                    <div class="col-lg-8 col-6">
                                        <p class="cout-note">Subtotal:</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <p class="note-product-price">$ {{ $grand_total }}</p>

                                    </div>
                                </div>
                                <div class="row  mt-2">
                                    <div class="col-lg-8 col-6">
                                        <p class="note-product-price mt-3">Shipping:</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <p class="note-product-price mt-3">Add Later</p>

                                    </div>
                                </div>
                                <p class="cout-note  mt-3">Coupon Discount</p>
                                <div class="row mt-2">
                                    <div class="col-lg-8 col-8 pr-0">
                                        <input type="text" placeholder="Gift certificate or coupon code" class="cart-coupon-input" />
                                    </div>
                                    <div class="col-lg-4 col-4 pl-0">
                                        <a href="#" class=" btn cart-btn w-100">Apply</a>
                                    </div>
                                    <div></div>

                                </div>
                                <div class="row  mt-4">
                                    <div class="col-lg-8 col-6">
                                        <p class="cout-note">Total:</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <p class="note-product-price">$ {{ $grand_total }}</p>

                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg-4 mt-5">
                            <a href="#" class=" btn border-btn w-100">Add a New Address</a>
                        </div>
                        <div class="col-lg-8"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="profile-light-txt mt-5">Shipping information</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <p class="textbox-label">First Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->first_name }}" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Last Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->last_name }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Company Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->company }}" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Mobile</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->phone }}"/>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Address</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->address }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">City</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->city }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Country</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->country }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">State/Province/Region</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->state }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Zip/Postal Code</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->shippingAddress[0]->zip_code }}"/>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Customer Type</p>
                                    <form action="">
                                        <input class="ml-2" type="checkbox" name="Western" value="Western">
                                        <label class="ml-2 label-setdefault" for="vehicle1"> Set as Default</label>
                                    </form>
                                </div>
                                <div class="col-lg-12">
                                    <div class="shipping-checkbox-area mt-2 d-flex">
                                        <p>Billing and shipping addresses are the same</p>
                                        <div class="ml-3">
                                            <input class="ml-2" type="radio" name="billing" value="Western">
                                            <label class="mr-2 mb-0" for="vehicle1"> Yes</label>
                                            <input class="ml-2" type="radio" name="billing" value="Western">
                                            <label class="mr-2 mb-0" for="vehicle1"> No</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-3 mt-5">
                                    <a href="#" class=" btn cart-btn w-100">Continue</a>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg-4 mt-5">
                            <a href="#" class=" btn border-btn w-100">Add a New Address</a>
                        </div>
                        <div class="col-lg-8"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="profile-light-txt mt-5">Shipping information</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <p class="textbox-label">First Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->first_name }}" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Last Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->last_name }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Company Name</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->company }}" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Mobile</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->phone }}"/>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Address</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->address }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">City</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->city }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Country</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->country }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">State/Province/Region</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->state }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Zip/Postal Code</p>
                                    <input class="input-textbox" readonly type="text" value="{{ @$user_info->billingAddress[0]->zip_code }}"/>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Customer Type</p>
                                    <form action="">
                                        <input class="ml-2" type="checkbox" name="Western" value="Western">
                                        <label class="ml-2 label-setdefault" for="vehicle1"> Set as Default</label>
                                    </form>
                                </div>
                                <div class="col-lg-12">
                                    <div class="shipping-checkbox-area mt-2 d-flex">
                                        <p>Billing and shipping addresses are the same</p>
                                        <div class="ml-3">
                                            <input class="ml-2" type="radio" name="billing" value="Western">
                                            <label class="mr-2 mb-0" for="vehicle1"> Yes</label>
                                            <input class="ml-2" type="radio" name="billing" value="Western">
                                            <label class="mr-2 mb-0" for="vehicle1"> No</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-3 mt-5">
                                    <a href="#" class=" btn cart-btn w-100">Continue</a>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <div class="row mt-5">
                        <div class="col-lg-12">
                            <form method="POST" id="checkout-main-form" action="{{ route('public.cart.order_checkout') }}">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $grand_total }}">
                                <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
                                <input type="hidden" name="currency_id" value="{{ get_application_currency_id() }}">
                                <input type="hidden" name="callback_url" value="{{ route('public.paypal_status') }}">
                                <input type="hidden" name="return_url" value="{{ route('public.checkout_success', $token) }}">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <input class="" type="radio" name="payment_method" value="1">
                                        <label class="mr-2 mb-0" for=""> Credit Card (Secure)</label>
                                        <input class="ml-2" type="radio" name="payment_method" id="payment_paypal"
                                               @if (setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::PAYPAL) checked @endif
                                               value="paypal">
                                        <label class="mr-2 mb-0" for=""> {{ setting('payment_paypal_name', trans('plugins/payment::payment.payment_via_paypal')) }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <h4 class="profile-light-txt mt-4 mb-4">Payment information</h4>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Card Number</p>
                                    <input class="input-textbox" readonly type="text" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Valid Thru</p>
                                    <input style="width:46.5%;" class="input-textbox" readonly type="text" /> /
                                    <input style="width:46.5%;" class="input-textbox" readonly type="text" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">CVV/CVC</p>
                                    <input class="input-textbox" readonly type="text" />
                                </div>
                                <div class="col-lg-12">
                                <p class="textbox-label">Cardholder Name</p>
                                    <input class="input-textbox" readonly type="text" />
                                </div>                                 

                                <div class="col-lg-3 mt-5">
                                    <input type="submit" form="checkout-main-form" class=" btn cart-btn w-100" value="Finish">
                                </div>
                            </div>
                        </form>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-4">
                            <div style="margin-top: 40px;" class="refer-area">
                                <p class="cout-note">Note</p>
                                <p class="cout-note-para mt-3">International shipping doesn’t qualify for free shipping. Free shipping can only be shipped to the 48 contigious states. We regret it cannot be free shipped to APO/FPO, Hawaii, Alaska, or Puerto Rico.</p>
                                <p class="cout-note  mt-5">Your Order</p>
                                <hr>
                                <p class="note-product mt-3">Product</p>
                                @php $grand_total = 0; @endphp
                                @foreach($cart->products as $cartItem)
                                    <div class="row">
                                        <div class="col-lg-8 col-6">
                                            <p class="note-product-name mt-3">{{ $cartItem->product->name }}</p>

                                        </div>
                                        <div class="col-lg-4 col-6">
                                            <p class="note-product-price mt-3">{{ $cartItem->qty }} * $ {{ $cartItem->price }}</p>

                                        </div>
                                    </div>
                                    <p class="note-product-size mt-1">SIZE: 2(S), 2(M), 2(L)</p>
                                    @php $total = $cartItem->qty * $cartItem->price; $grand_total = $grand_total + $total; @endphp
                                @endforeach
                                <div class="row  mt-5">
                                    <div class="col-lg-8 col-6">
                                        <p class="cout-note">Subtotal:</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <p class="note-product-price">$ {{ $grand_total }}</p>

                                    </div>
                                </div>
                                <div class="row  mt-2">
                                    <div class="col-lg-8 col-6">
                                        <p class="note-product-price mt-3">Shipping:</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <p class="note-product-price mt-3">Add Later</p>

                                    </div>
                                </div>
                                <p class="cout-note  mt-3">Coupon Discount</p>
                                <div class="row mt-2">
                                    <div class="col-lg-8 col-8 pr-0">
                                        <input type="text" placeholder="Gift certificate or coupon code" class="cart-coupon-input" />
                                    </div>
                                    <div class="col-lg-4 col-4 pl-0">
                                        <a href="#" class=" btn cart-btn w-100">Apply</a>
                                    </div>
                                    <div></div>

                                </div>
                                <div class="row  mt-4">
                                    <div class="col-lg-8 col-6">
                                        <p class="cout-note">Total:</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <p class="note-product-price">$ {{ $grand_total }}</p>

                                    </div>
                                </div>
                            </div>
                        </div>
    </div>
    
</section>