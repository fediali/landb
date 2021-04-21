<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Product</li>
                <li class="breadcrumb-item active" aria-current="page">Product Detail</li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-2 mbtb-pr-2">
    <div class="row d-mbtb-none">
        <div class="col-lg-6 mt-2 mb-3">
            <p class="cart-head">Product</p>
        </div>
        <div class="col-lg-2 mt-2 mb-3 text-center">
            <p class="cart-head">Price</p>

        </div>
        <div class="col-lg-2 mt-2 mb-3">
            <p class="cart-head">Quantity</p>

        </div>
        <div class="col-lg-2 mt-2 mb-3">
            <p class="cart-head">Total</p>

        </div>
    </div>
    @php $grand_total = 0; @endphp
    @foreach($cart->cartItems as $cartItem)
        <div class="row mb-4 mt-4 cartitem-{{ $cartItem->id }}">
            <div class="col-lg-6 mt-2">
                <div class="d-flex">
                    {!! image_html_generator(@$cartItem->product->images[0], @$cartItem->product->name, '95px', '75px' ) !!}
                    <div class="ml-3">
                        <p class="cart-product-name mt-2 mb-2">{{ $cartItem->product->name }}</p>
                        <p class="cart-product-code mb-2">CODE: {{ $cartItem->product->sku }}</p>
                        @php $sizes = get_category_sizes_by_id($cartItem->product->category_id); @endphp
                        @if(!empty($sizes->category_sizes))
                            <p class="cart-product-size">SIZE: @foreach($sizes->category_sizes as $size) {{ @$size->name }} {!! ($loop->last) ? '':',' !!} @endforeach</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-2 mt-2 text-center">
                <p class="mt-2">$ {{ $cartItem->product->price }}</p>
            </div>
            <div class="col-lg-2 mt-2">
                <form id='myform' method='POST' action='#'>
                    <input style="height: 35px;" type='button' data-update="1" data-id="{{ $cartItem->id }}" value='-' class='qtyminus' field='quantity' />
                    <input style="height: 35px;" type='text' name='quantity' value='{{ $cartItem->quantity }}' class='qty' />
                    <input style="height: 35px;" type='button' data-update="1" data-id="{{ $cartItem->id }}" value='+' class='qtyplus' field='quantity' />
                </form>
            </div>
            <div class="col-lg-2 mt-2">
                @php $total = $cartItem->quantity * $cartItem->price; $grand_total = $grand_total + $total; @endphp
                <p class="mt-2">$ {{ $total }}</p>
            </div>
        </div>
        <hr>
    @endforeach
    {{--<div class="row mb-4 mt-4">
        <div class="col-lg-6 mt-2">
            <div class="d-flex">
                <img style="height: 95px; width: 75px;" src="./img/product/Front.png" />
                <div class="ml-3">
                    <p class="cart-product-name mt-2 mb-2">Coral Cut Out V-neck Basic Tee Plus Size</p>
                    <p class="cart-product-code mb-2">CODE: MH0315-RSER</p>
                    <p class="cart-product-size">SIZE: 2(XL), 2(2XL), 2(3XL) </p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 mt-2 text-center">
            <p class="mt-2">$ 25.00</p>
        </div>
        <div class="col-lg-2 mt-2">
            <form id='myform' method='POST' action='#'>
                <input style="height: 35px;" type='button' value='-' class='qtyminus' field='quantity' />
                <input style="height: 35px;" type='text' name='quantity' value='0' class='qty' />
                <input style="height: 35px;" type='button' value='+' class='qtyplus' field='quantity' />
            </form>
        </div>
        <div class="col-lg-2 mt-2">
            <p class="mt-2">$ 150.00</p>
        </div>
    </div>
    <hr>--}}
    <div class="row mt-5">
        <div class="col-lg-12 mb-3">
            <h3 class="coupon-txt">Coupon Discount</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-9 col-9 pr-0">
                    <input type="text" placeholder="Gift certificate or coupon code" class="cart-coupon-input" />
                </div>
                <div class="col-lg-3 col-3 pl-0">
                    <a href="./cart.html" class=" btn cart-btn w-100">Apply</a>
                </div>
                <div></div>

            </div>
        </div>
        <div class="col-lg-3"></div>
        <div class="col-lg-5">
            <div class="total-area mt-2">
                <div class="row mt-2">
                    <div class="col-lg-8 col-7">
                        <p class="total-head">Subtotal</p>
                    </div>
                    <div class="col-lg-4 col-5">
                        <p class="total-para">$ <span class="total-cart-price">{{ $grand_total }}</span></p>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-8 col-7">
                        <p class="total-head">Shipping Cost</p>
                    </div>
                    <div class="col-lg-4 col-5">
                        <p class="total-para">+ Calculate</p>
                    </div>
                </div>
                <hr>
                <div class="row mt-4">
                    <div class="col-lg-8 col-7">
                        <p class="final-total-head">Total</p>
                    </div>
                    <div class="col-lg-4 col-5">
                        <p class="final-total-para">$0.00</p>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-lg-6 mt-3">
                    <a href="#" class=" btn cart-btn w-100">Continue Shopping</a>

                </div>
                <div class="col-lg-6 mt-3">
                    <a href="./checkout.html" class=" btn cart-btn w-100">Proceed to Checkout</a>

                </div>
            </div>
        </div>
    </div>


</section>