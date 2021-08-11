<section class="ml-5 mr-5 mt-3">
        <div class="row">
        <div class="col-lg-9">
            <img src="{{ asset('landb/img/Logo.png') }}" />
        </div>
        <div class="col-lg-3">
            <p>
           <b> ORDER # {{ $order->id }} </b>
            </p>
            <p>
                <b>ORDER DATE </b> {{ date('d/m/Y, h:i A', strtotime($order->created_at)) }}
            </p>
            <p>
                <b>PAYMENT</b> {{ ucfirst(str_replace(['-', '_'], ' ', @$order->payment->payment_channel)) }}
            </p>
            <p>
                <b>STATUS</b> {{ ucfirst($order->status) }}
            </p>

        </div>

        </div>
        <div class="row mt-5">
            <div class="col-lg-4">
                <div style="background: #eaeaea;" class="p-3">
                <h3>
                    <b>STORE</b>
                     </h3>
                     <hr style="border: 2px solid;">
                <p>
                    12801 N, Stemmons Fwt, Suite 710 Farmers Branch, Texas 78865 United States
                </p>
                <p>
                    97251235552
                </p>
                <p>
                    customerservice@landapparel.com
                </p>
                <p>
                    https://landapparel.com/
                </p>
            </div>
            </div>
            @if($order->billingAddress)
            <div class="col-lg-4">
                <div class="p-3">
                <h3>
                    <b>BILL TO</b>
                     </h3>
                     <hr style="border: 2px solid #DDD;">
                <p>
                    {{ $order->billingAddress->name.', '.$order->billingAddress->city.' ,'.$order->billingAddress->state.' ,'.$order->billingAddress->country. ' ,'. $order->billingAddress->zip_code }}
                </p>
                <p>
                    {{ $order->billingAddress->phone }}
                </p>
                <p>
                    {{ $order->billingAddress->email }}
                </p>
                <p>
                    https://landapparel.com/
                </p>
            </div>
            </div>
            @endif
            @if($order->shippingAddress)
            <div class="col-lg-4">
                <div class="p-3">
                <h3>
                    <b>SHIP TO</b>
                     </h3>
                     <hr style="border: 2px solid #DDD;">
                <p>
                    {{ $order->shippingAddress->name.', '.$order->shippingAddress->city.' ,'.$order->shippingAddress->state.' ,'.$order->shippingAddress->country. ' ,'. $order->shippingAddress->zip_code }}
                </p>
                <p>
                    {{ $order->shippingAddress->phone }}
                </p>
                <p>
                    {{ $order->shippingAddress->email }}
                </p>
                <p>
                    https://landapparel.com/
                </p>
            </div>
            </div>
            @endif
           {{-- <div class="col-lg-4">
                <div class="p-3">
                <h3>
                    <b>SHIP TO</b>
                     </h3>
                     <hr style="border: 2px solid #DDD;">
                <p>
                    12801 N, Stemmons Fwt, Suite 710 Farmers Branch, Texas 78865 United States
                </p>
                <p>
                    97251235552
                </p>
                <p>
                    customerservice@landapparel.com
                </p>
                <p>
                    https://landapparel.com/
                </p>
            </div>
            </div>  --}}
            </div>
    </section>
    <section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-2 mbtb-pr-2">
    <hr style="border: 2px solid;">

        <div class="row d-mbtb-none">
            <div class="col-lg-7 mt-2 mb-3 text-center">
                <p class="cart-head">Item Description</p>
            </div>
            <div class="col-lg-1 mt-2 mb-3">
                <p class="cart-head">Quantity</p>

            </div>
            <div class="col-lg-1 mt-2 mb-3 text-center">
                <p class="cart-head">Price</p>

            </div>

            <div class="col-lg-1 mt-2 mb-3 text-center">
                <p class="cart-head"> Discount</p>

            </div>
            <div class="col-lg-1 mt-2 mb-3 text-center">
                <p class="cart-head">Tax</p>

            </div>
            <div class="col-lg-1 mt-2 mb-3">
                <p class="cart-head">Item Total</p>

            </div>
        </div>
        @foreach($order->products as $order_product)
            <div class="row mb-4 mt-4">
            <div class="col-lg-7 mt-2">
                <div class="d-flex">
                    {!! image_html_generator(@$order_product->product->images[0], @$order_product->product->name, '95', '75' ) !!}
                    <div class="ml-3">
                        <p class="cart-product-name mt-2 mb-2">{{ $order_product->product->name }}</p>
                        <p class="cart-product-code mb-2">CODE: {{ $order_product->product->sku }}</p>
                        @php $sizes = get_category_sizes_by_id($order_product->product->category_id); @endphp
                        @php
                            $variation = \Botble\Ecommerce\Models\ProductVariation::where('product_id', $order_product->product_id)->join('ec_product_variation_items as epvi', 'epvi.variation_id', 'ec_product_variations.id')->join('ec_product_attributes as epa', 'epa.id', 'epvi.attribute_id')->where('epa.attribute_set_id', 2)->select('epa.*')->first();

                        @endphp
                        @if($variation)
                            <p class="cart-product-size">SIZE: {{ $order_product->product->sizes }}</p>
                            <p class="cart-product-size">Pieces Per Pack: {{ $order_product->product->prod_pieces }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-1 mt-2 text-center">
                <p class="mt-2">{{ $order_product->qty }}</p>
            </div>
            <div class="col-lg-1 mt-2">
                <p class="mt-2">$ {{ $order_product->price }}</p>
            </div>
            <div class="col-lg-1 mt-2 mb-txt-center text-center">
                <p class="mt-2">-</p>
            </div>
            <div class="col-lg-1 mt-2 text-center">
                <p class="mt-2">-</p>
            </div>
            <div class="col-lg-1 mt-2">
                <p class="mt-2">$ {{ $order_product->qty*$order_product->price  }}</p>
            </div>
        </div>
        @endforeach
        <hr>

        <hr style="border: 2px solid;">
        {{--<div class="row">
            <div class="col-lg-12">
                <p class="cart-product-name mt-2 mb-2">Gift Certificate</p>
            </div>
        </div>--}}
        <div class="row">
            <div class="col-lg-6 col-6">
                <p class="mt-2">Subtotal</p>
            </div>
            <div class="col-lg-6 col-6 text-right">
                <p class="mt-2">$ {{ $order->amount }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-6">
                <p class="mt-2">Shipping</p>
            </div>
            <div class="col-lg-6 col-6 text-right">
                <p class="mt-2">$ 00.00</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-6 col-6">
                <h3>
                    <b>TOTAL</b>
                     </h3>
            </div>
            <div class="col-lg-6 col-6 text-right">
                <h3>
                    <b>$ {{ $order->amount }}</b>
                     </h3>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-12 col-12">
                <p class="mt-2"> For return policy please visit landapparel.com/faq.html
                </p>
            </div>
        </div>
    </section>
