<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
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
                        <b>{{@$order->shippingAddress->name}}</b>
                    </p>
                    <p style="" class="m-0">
                        {{@$order->shippingAddress->zip_code}}, {{@$order->shippingAddress->city}},<br/> 
                        {{@$order->shippingAddress->address}}, {{@$order->shippingAddress->state}}<br/> 
                        {{@$order->shippingAddress->zip_code}} {{@$order->shippingAddress->country}}
                    </p>
                    <p style="" class="m-0">
                        {{@$order->shippingAddress->phone}}
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

<!---New Email Div Start Here--->

<div style="margin:0;padding:0;width:100%!important" >
   <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="border-collapse:collapse;background-color:#ffffff">
      <tbody>
         <tr>
            <td style="border-collapse:collapse;padding:40px 10px 40px 10px">
               <table cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border:1px solid #ffffff;background-color:#fff">
                  <tbody>
                     <tr>
                        <td style="border-collapse:collapse;padding:10px 30px 20px 30px;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px">
                           <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                              <tbody>
                                 <tr>
                                    <td style="border-collapse:collapse;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px"><a href="https://landbapparel.com/" style="outline:none;color:#0a0a0a" target="_blank"><img src="https://revamp.landbw.co/landb/img/Logo.png" alt="L&amp;B" style="outline:none;text-decoration:none;border:none" data-image-whitelisted="" class="CToWUd"></a></td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                     <tr style="background-color:#0a0a0a">
                        <td style="border-collapse:collapse;padding:20px 30px">
                           <h1 style="color:#fff;font-size:20px;font-weight:400;text-transform:uppercase"> ORDER #{{$order->id}} HAS BEEN PROCESSED</h1>
                        </td>
                     </tr>
                     <tr>
                        <td style="border-collapse:collapse;padding:30px;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px">
                           <b>Dear {{$order->user->name}} ,</b>
                           <br/> The status of your order has been changed to {{$order->status}}.
                           <br>
                           <br> 
                           <table width="600" style="border-collapse:separate;font-family:Helvetica,Arial,sans-serif" rel="min-width: 800px; font-family: Helvetica, Arial, sans-serif; border-collapse: separate;" cellspacing="0" cellpadding="0" border="0">
                              <tbody>
                                 <tr style="vertical-align:top">
                                    <td style="border-collapse:collapse;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:5px">
                                       <table width="100%;" cellspacing="0" border="0" style="border-collapse:separate;font-family:Helvetica,Arial,sans-serif">
                                          <tbody>
                                             <tr>
                                                <td width="50%" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-bottom:10px;padding-right:40px;vertical-align:top">
                                                   <h2 style="color:#444444;margin:0px;font-size:22px;font-family:Helvetica,Arial,sans-serif;text-transform:uppercase;margin-bottom:15px;line-height:1.5em">Ship to</h2>
                                                   <p style="margin:0px;font-size:14px;font-family:Helvetica,Arial,sans-serif;padding-bottom:5px">
                                                      <b>{{@$order->shippingAddress->name}}</b>
                                                   </p>
                                                   <p style="margin:0px;font-size:14px;font-family:Helvetica,Arial,sans-serif;padding-bottom:5px">
                                                      <b>{{@$order->shippingAddress->name}}</b>
                                                   </p>
                                                   <p style="margin:0px;font-size:14px;font-family:Helvetica,Arial,sans-serif;padding-bottom:5px">
                                                      {{@$order->shippingAddress->zip_code}}, {{@$order->shippingAddress->city}},<br/> 
                                                      {{@$order->shippingAddress->address}}, {{@$order->shippingAddress->state}}<br/> 
                                                      {{@$order->shippingAddress->zip_code}} {{@$order->shippingAddress->country}}
                                                   </p>
                                                   <p style="margin:0px;font-size:14px;font-family:Helvetica,Arial,sans-serif;padding-bottom:5px">
                                                      {{@$order->shippingAddress->phone}}
                                                   </p>
                                                </td>
                                                <td width="50%" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-bottom:10px;padding-left:40px;vertical-align:top">
                                                   <p style="margin:0px;color:#787878;font-size:14px;font-family:Helvetica,Arial,sans-serif;padding-bottom:5px">
                                                      <span style="color:#444444;font-weight:600;font-family:Helvetica,Arial,sans-serif;text-transform:uppercase">Order date</span>  {{$order->created_at}}
                                                   </p>
                                                   <p style="margin:0px;color:#787878;font-size:14px;font-family:Helvetica,Arial,sans-serif;padding-bottom:5px">
                                                      <span style="color:#444444;font-weight:600;font-family:Helvetica,Arial,sans-serif;text-transform:uppercase">Payment</span> {{$order->payment->payment_channel->label()}}
                                                   </p>
                                                   <p style="margin:0px;color:#787878;font-size:14px;font-family:Helvetica,Arial,sans-serif;padding-bottom:5px">
                                                      <span style="color:#444444;font-weight:600;font-family:Helvetica,Arial,sans-serif;text-transform:uppercase">Shipping</span> 	{{$order->shipping_method_name}}
                                                   </p>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="border-collapse:collapse;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px">
                                       <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:separate;border-top:3px solid #444;color:#444;font-family:Helvetica,Arial,sans-serif">
                                          <tbody>
                                             <tr style="font-size:12px;font-family:Helvetica,Arial,sans-serif;font-weight:600;text-transform:uppercase;text-align:center">
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px">Item description</td>
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px">Qty</td>
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px">Price</td>
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px">Item total</td>
                                             </tr>
                                             <tr style="font-size:14px;font-family:Helvetica,Arial,sans-serif;font-weight:400;text-transform:uppercase;text-align:center">
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px 0px;border-bottom:1px solid #ebebeb">
                                                   <table style="border-collapse:collapse">
                                                      <tbody>
                                                         <tr>
                                                            <td rowspan="2" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px;padding-right:20px"><img style="max-width: 80px;" class="split-img" src="{{ RvMedia::getImageUrl($product->original_product->image, null, false, RvMedia::getDefaultImage()) }}">
                                                            </td>
                                                            <td style="border-collapse:collapse;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:5px;vertical-align:middle;text-align:left"><span style="font-family:Helvetica,Arial,sans-serif"><strong style="font-weight:600">Mid Wash Extreme Flare Jeans</strong></span>
                                                            </td>
                                                         </tr>
                                                         <tr>
                                                            <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px;vertical-align:top;text-align:left"><span style="font-size:11px;font-weight:400;font-family:Helvetica,Arial,sans-serif;color:#a8a8a8">L18074-MW<br>                                     
                                                               <strong>Options:</strong>
                                                               SIZE:&nbsp;1(4), 1(6), 1(8), 1(10), 1(12), 1(14)
                                                               </span>
                                                            </td>
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                </td>
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px 0px;border-bottom:1px solid #ebebeb">
                                                   <p style="margin:1em 0;text-align:center;font-family:Helvetica,Arial,sans-serif"><strong style="font-weight:600">6</strong></p>
                                                </td>
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px 0px;border-bottom:1px solid #ebebeb">
                                                   <p style="margin:1em 0;text-align:center;font-family:Helvetica,Arial,sans-serif"><strong style="font-weight:600">$19.50</strong></p>
                                                </td>
                                                <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:10px 0px;border-bottom:1px solid #ebebeb">
                                                   <p style="margin:1em 0;text-align:center;font-family:Helvetica,Arial,sans-serif"><strong style="font-weight:600">$117.00</strong></p>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:0px;border-top:2px solid #f5f5f5;padding-top:10px">
                                       <table width="100%" style="border-collapse:separate;font-family:Helvetica,Arial,sans-serif">
                                          <tbody>
                                             <tr>
                                                <td width="66%" style="border-collapse:collapse;color:#444444;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px;line-height:21px;padding-right:30px;vertical-align:top">
                                                </td>
                                                <td width="34%" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px;vertical-align:top">
                                                   <table width="100%;" style="border-collapse:collapse;font-size:14px;font-family:Helvetica,Arial,sans-serif;color:#444">
                                                      <tbody>
                                                         <tr style="vertical-align:top;font-family:Helvetica,Arial,sans-serif">
                                                            <td align="left" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px;padding-bottom:20px">Subtotal
                                                            </td>
                                                            <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">$117.00
                                                            </td>
                                                         </tr>
                                                         <tr style="vertical-align:top">
                                                            <td align="left" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px;padding-bottom:20px;text-transform:uppercase">
                                                            </td>
                                                            <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">
                                                            </td>
                                                         </tr>
                                                         <tr style="vertical-align:top">
                                                            <td align="left" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px;padding-bottom:20px">Shipping
                                                            </td>
                                                            <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">$0.00
                                                            </td>
                                                         </tr>
                                                         <tr style="vertical-align:top;font-family:Helvetica,Arial,sans-serif">
                                                            <td align="left" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">						</td>
                                                            <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">						</td>
                                                         </tr>
                                                         <tr style="vertical-align:top;font-family:Helvetica,Arial,sans-serif">
                                                            <td align="left" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">						</td>
                                                            <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">						</td>
                                                         </tr>
                                                         <tr style="vertical-align:top;font-family:Helvetica,Arial,sans-serif">
                                                            <td align="left" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">						</td>
                                                            <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;padding:5px">						</td>
                                                         </tr>
                                                         <tr style="vertical-align:top;font-size:22px;font-weight:600;font-family:Helvetica,Arial,sans-serif">
                                                            <td align="left" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:22px;padding:5px;padding-top:20px;border-top:1px solid #e8e8e8">Total
                                                            </td>
                                                            <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:22px;padding:5px;padding-top:20px;border-top:1px solid #e8e8e8">$117.00
                                                            </td>
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                     <tr style="border-top:1px solid #ffffff">
                        <td style="border-collapse:collapse;background-color:#757f83;padding:20px 30px;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px">
                           <table width="250" align="left" style="border-collapse:collapse">
                              <tbody>
                                 <tr>
                                    <th style="border-collapse:collapse;color:#fff!important;font-size:16px!important;font-weight:600;margin:0px;text-transform:uppercase;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;border-bottom:1px solid #ffffff;text-align:left;border:none">
                                       Contact information
                                    </th>
                                 </tr>
                                 <tr>
                                    <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:5px">
                                       <address style="margin:0px">12801 N. Stemmons Fwy, Suite 710, Farmers Branch </address>
                                    </td>
                                    <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:5px">
                                       <p style="margin:1em 0">For return policy please visit <a href="https://landbapparel.com/refund-policy.html" style="outline:none;color:#0a0a0a" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://landbapparel.com/refund-policy.html&amp;source=gmail&amp;ust=1633810942000000&amp;usg=AFQjCNFVy-9x6HpOIt0VkBZz-MbhdvUXiA">Refund Policy</a></p>
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
                                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px"><a href="https://twitter.com/landb_official/" style="outline:none;color:#0a0a0a" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://twitter.com/landb_official/&amp;source=gmail&amp;ust=1633810942000000&amp;usg=AFQjCNH1BF3TLPvzJNGUbVUdmq_TB1OuOw"><img width="30" height="30" src="https://mail.google.com/mail/u/0?ui=2&amp;ik=45b73ee270&amp;attid=0.0.3&amp;permmsgid=msg-f:1712997982406929462&amp;th=17c5ca96d2536c36&amp;view=fimg&amp;sz=s0-l75-ft&amp;attbid=ANGjdJ9ito-NkjbMTC5pMXLO3aK6sUJj76gwjKafdyco3dVEbvN-aBfUiBeBH1fHQhkZwogB3BkUgAjjmwl9cDwgIC06MtRoS-pihfUBXkVtrXQCWZdPn8bE3TLWCmY&amp;disp=emb&amp;realattid=17c5ca89a5be72198fa3" style="outline:none;text-decoration:none;border:none" data-image-whitelisted="" class="CToWUd"></a></td>
                                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px"><a href="https://www.facebook.com/LANDBOFFICIAL/" style="outline:none;color:#0a0a0a" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.facebook.com/LANDBOFFICIAL/&amp;source=gmail&amp;ust=1633810942000000&amp;usg=AFQjCNFY7VVwjLmC8TAyKljVKWEhZc8Wjw"><img width="30" height="30" src="https://mail.google.com/mail/u/0?ui=2&amp;ik=45b73ee270&amp;attid=0.0.4&amp;permmsgid=msg-f:1712997982406929462&amp;th=17c5ca96d2536c36&amp;view=fimg&amp;sz=s0-l75-ft&amp;attbid=ANGjdJ_hJSqzGHFQFImM0bWO29oiInLJkclqKvZt0XNA4_QuD9N2w9vDqb142GyDtRJjUjtYTimu4kPf4Pij6T_hX5kFhQv82eBrKHtbVQeVPzSkgHIuBj6k3bDXdr8&amp;disp=emb&amp;realattid=17c5ca89a5be72fb07b4" style="outline:none;text-decoration:none;border:none" data-image-whitelisted="" class="CToWUd"></a></td>
                                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px"><a href="https://www.instagram.com/landb_official/" style="outline:none;color:#0a0a0a" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.instagram.com/landb_official/&amp;source=gmail&amp;ust=1633810942000000&amp;usg=AFQjCNGUKUV51NUU0DODG3OFk6o64lVObw"><img width="30" height="30" src="https://mail.google.com/mail/u/0?ui=2&amp;ik=45b73ee270&amp;attid=0.0.5&amp;permmsgid=msg-f:1712997982406929462&amp;th=17c5ca96d2536c36&amp;view=fimg&amp;sz=s0-l75-ft&amp;attbid=ANGjdJ-EI6kfZKxMdz4xp_p3fLCRJNZdEfzXGpP6nwdQTOrrjOEUBLrTT_s9gZEwdUFAg-1thLDEVxv96_EsxultMfJIW7hr_XYbR-R_9SElD_1b78Xu21p5cb-H5h4&amp;disp=emb&amp;realattid=17c5ca89a5be73dc7fc5" style="outline:none;text-decoration:none;border:none" data-image-whitelisted="" class="CToWUd"></a></td>
                                                <td style="border-collapse:collapse;color:#fff;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:0px;padding-right:10px"><a href="https://www.pinterest.com/landb_official/" style="outline:none;color:#0a0a0a" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.pinterest.com/landb_official/&amp;source=gmail&amp;ust=1633810942000000&amp;usg=AFQjCNFQt72RM_-wUZ9FlHSwR3xl3b_Yrw"><img width="30" height="30" src="https://mail.google.com/mail/u/0?ui=2&amp;ik=45b73ee270&amp;attid=0.0.6&amp;permmsgid=msg-f:1712997982406929462&amp;th=17c5ca96d2536c36&amp;view=fimg&amp;sz=s0-l75-ft&amp;attbid=ANGjdJ_fwRG1TQrAGrA7t4Lls1awM86ZI0x5zVRrN76gUEBwYapWqlbRDfaCX3TpvqqfnziGHU3p7C4gzUD0ljYA3pdaDqbwYHK8Lyj-r-auK_e06wRSXIwFm8_qedQ&amp;disp=emb&amp;realattid=17c5ca89a5be74bdf7d6" style="outline:none;text-decoration:none;border:none" data-image-whitelisted="" class="CToWUd"></a></td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td style="border-collapse:collapse;padding:0px 30px 10px;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px">
                           <table width="100%" style="border-collapse:collapse">
                              <tbody>
                                 <tr>
                                    <td style="border-collapse:collapse;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:10px 0 0 0;padding-bottom:0!important">
                                       ©&nbsp;L&amp;B
                                    </td>
                                    <td align="right" style="border-collapse:collapse;color:#0a0a0a;font-family:Roboto,sans-serif,Helvetica,Arial,sans-serif;font-size:14px;padding:10px 0 0 0;padding-bottom:0!important">
                                       Thank you for using our shopping cart.
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <div class="yj6qo"></div>
   <div class="adL">
   </div>
</div>

</body>

</html>
