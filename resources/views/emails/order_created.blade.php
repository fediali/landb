<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>

<body style="font-family:arial;">
<div style="margin-left: 20px; margin-right: 20px;" id="printReceipt">
    <section class="ml-5 mr-5 mt-3" style=" page-break-before: always;">
        <div class="row">
            <div style="">
                <img height="50" src="https://revamp.landbw.co/landb/img/Logo.png">
            </div>
            <div style="">
                <p style="font-size:16px; color:#fff; padding: 30px 30px; background-color: #0a0a0a;" class="m-0">
                    ORDER #{{$order->id}} HAS BEEN PROCESSED
                </p>
                <div style="margin-top:30px; padding: 0px 30px;">
                    <p style="font-size:16px;" class="m-0">
                        <b>Dear {{$order->user->name}} ,</b>
                        <br/> The status of your order has been changed to {{$order->status}}.
                    </p>
                </div>
            </div>
        </div>
        <div style="display:flex;padding: 0px 30px; " class="row mt-5">
            <div style="width:50%">
                <div style="" class="p-3">
                    <h6 style="color: #444444;  margin: 0px; font-size: 22px; font-family: Helvetica,Arial,sans-serif;text-transform: uppercase; margin-bottom: 15px;  line-height: 1.5em;">
                        <b>SHIP TO</b>
                    </h6>
                    <p style="" class="m-0">
                        <b>{{$order->shippingAddress->name}}</b>
                    </p>
                    <p style="" class="m-0">
                        {{$order->shippingAddress->zip_code}}, {{$order->shippingAddress->city}},<br/> {{$order->shippingAddress->address}}, {{$order->shippingAddress->state}}<br/> {{$order->shippingAddress->zip_code}} {{$order->shippingAddress->country}}
                    </p>
                    <p style="" class="m-0">
                        {{$order->shippingAddress->phone}}
                    </p>
                </div>
            </div>
            <div style="width:50%">
                <div>
                    <p style="" class="m-0">
                        <b>ORDER DATE</b>&nbsp;&nbsp;{{$order->created_at}}
                    </p>
                    <p style="" class="m-0">
                        <b>PAYMENT </b>&nbsp;&nbsp;{{$order->payment->payment_channel->label()}}
                    </p>
                    <p style="" class="m-0">
                        <b>SHIPPING </b>&nbsp;&nbsp;{{$order->shipping_method_name}}
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-2 mbtb-pr-2">
        <hr style="border: 2px solid; margin: 5px;">
        <table style="width:100%" class="table">
            <thead>
            <tr>
                <th style="font-size:12px;" scope="col"></th>
                <th style="text-align:left; text-transform:uppercase;" scope="col">Product</th>
                <th style="text-align:left; text-transform:uppercase;" scope="col">Quantity</th>
                <th style="text-align:left; text-transform:uppercase;" scope="col">Price</th>
                <th style="text-align:left; text-transform:uppercase;" scope="col">Item Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($order->products as $orderProduct)
                @php
                    $product = get_products([
                        'condition' => [
                            'ec_products.id' => $orderProduct->product_id,
                        ],
                        'take' => 1,
                        'select' => [
                            'ec_products.id',
                            'ec_products.images',
                            'ec_products.name',
                            'ec_products.price',
                            'ec_products.sale_price',
                            'ec_products.sale_type',
                            'ec_products.start_date',
                            'ec_products.end_date',
                            'ec_products.sku',
                            'ec_products.is_variation',
                            'ec_products.sizes',
                            'ec_products.prod_pieces',
                        ],
                    ]);
                @endphp
                <tr>
                    <td>
                        <img style="max-width: 80px;" class="split-img" src="{{ RvMedia::getImageUrl($product->original_product->image, null, false, RvMedia::getDefaultImage()) }}">
                    </td>
                    <td>
                        <div class="ml-3">
                            <p style="margin-top:30px;  font-weight: 600;" class="cart-product-name mt-2 mb-2">
                                {{ $orderProduct->product_name }}
                            </p>
                            <p style=" " class="cart-product-code mb-2">
                                {{ $product->sku }}
                            </p>
                            <p style="" class="cart-product-size mb-2">
                            @if ($product->is_variation)
                                <small>
                                    @php $attributes = get_product_attributes($product->id) @endphp
                                    @if (!empty($attributes))
                                        @foreach ($attributes as $attribute)
                                            @if($attribute->attribute_set_title !== 'Size')
                                                {{ $attribute->attribute_set_title }}
                                                : {{ $attribute->title }}@if (!$loop->last)
                                                @endif
                                                @if($attribute->title !== 'Single')
                                                    , Size : {{$product->sizes}}
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                    , Per Piece:
                                    ${{($product->prod_pieces) ? $product->price / $product->prod_pieces :$product->price }}
                                </small>
                            @endif
                            </p>
                        </div>
                    </td>
                    <td style="    font-weight: 600;">{{ $orderProduct->qty }}</td>
                    <td style="    font-weight: 600;">{{ format_price($orderProduct->price) }}</td>
                    <td style="    font-weight: 600;">{{ format_price($orderProduct->price * $orderProduct->qty) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr>
        <div style="display:flex;">
            <div style="width:80%"></div>
            <div style="width:20%">
                <div style="display:flex;">
                    <div style="width:50%">
                        <p style="" class="mt-2">Subtotal</p>
                    </div>
                    <div style="width:50%">
                        <p style="text-align:right" class="mt-2">{{ format_price($order->sub_total) }}</p>
                    </div>
                </div>
                <div style="display:flex;">
                    <div style="width:50%">
                        <p style="" class="mt-2">Shipping</p>
                    </div>
                    <div style="width:50%">
                        <p style="text-align:right" class="mt-2">{{ format_price($order->shipping_amount) }}</p>
                    </div>
                </div>
                <hr/>
                <div style="display:flex;">
                    <div style="width:50%">
                        <p style="font-weight: 600;" class="mt-2">Total</p>
                    </div>
                    <div style="width:50%">
                        <p style="text-align:right;font-weight: 600;" class="mt-2">{{ format_price($order->amount) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div style="     min-height: 150px;   border-collapse: collapse;
               background-color: #757f83;
               padding: 20px 30px;
               color: #fff;
               font-family: Roboto,sans-serif,Helvetica,Arial,sans-serif;
               font-size: 14px;">
            <table width="250" align="left" style="border-collapse:collapse">
                <tbody>
                <tr>
                    <th style="border-collapse:collapse;color:#fff!important;font-size:16px!important;font-weight:600;margin:0px;text-transform:uppercase;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;border-bottom:1px solid #ffffff;text-align:left;border:none">
                        Contact information
                    </th>
                </tr>
                <tr>
                    <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:5px">
                        <address style="margin:0px">12801 N. Stemmons Fwy, Suite 710, Farmers Branch</address>
                    </td>
                    <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:5px">
                        <p style="margin:1em 0">For return policy please visit <a
                                    href="https://landbapparel.com/refund-policy.html"
                                    style="outline:none;color:#0a0a0a" target="_blank"
                                    data-saferedirecturl="https://www.google.com/url?q=https://landbapparel.com/refund-policy.html&amp;source=gmail&amp;ust=1632258981830000&amp;usg=AFQjCNHsU6Qm3HjhzDzipUWrsIHFUkIyHQ">Refund
                                Policy</a></p>
                    </td>
                </tr>
                </tbody>
            </table>
            <table width="250" align="left" style="border-collapse:collapse">
                <tbody>
                <tr>
                    <th style="border-collapse:collapse;color:#fff!important;font-size:16px!important;font-weight:600;margin:0px;text-transform:uppercase;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;border-bottom:1px solid #ffffff;text-align:left;border:none">
                        Get social
                    </th>
                </tr>
                <tr>
                    <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:5px">
                        <table cellspacing="0" cellpadding="0" align="left" style="border-collapse:collapse">
                            <tbody>
                            <tr>
                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px">
                                    <a href="https://twitter.com/landb_official/" style="outline:none;color:#0a0a0a"
                                       target="_blank"><img width="30" height="30"
                                                            src="https://landbw.co/images/companies/twitter.png"
                                                            style="outline:none;text-decoration:none;border:none"></a>
                                </td>
                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px">
                                    <a href="https://www.facebook.com/LANDBOFFICIAL/" style="outline:none;color:#0a0a0a"
                                       target="_blank"><img width="30" height="30"
                                                            src="https://landbw.co/images/companies/facebook.png"
                                                            style="outline:none;text-decoration:none;border:none"></a>
                                </td>
                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px">
                                    <a href="https://www.instagram.com/landb_official/"
                                       style="outline:none;color:#0a0a0a" target="_blank"><img width="30" height="30"
                                                                                               src="https://landbw.co/images/companies/instagram.png"
                                                                                               style="outline:none;text-decoration:none;border:none"></a>
                                </td>
                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px">
                                    <a href="https://www.pinterest.com/landb_official/"
                                       style="outline:none;color:#0a0a0a" target="_blank"><img width="30" height="30"
                                                                                               src="https://landbw.co/images/companies/pinterest.png"
                                                                                               style="outline:none;text-decoration:none;border:none"></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>

</html>
