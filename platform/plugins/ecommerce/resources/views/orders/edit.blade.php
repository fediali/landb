@extends('core/base::layouts.master')

@section('content')
    <link media="all" type="text/css" rel="stylesheet"
          href="{{asset('vendor/core/core/base/libraries/bootstrap3-editable/css/bootstrap-editable.css')}}">
    <script
        src="{{asset('vendor/core/core/base/libraries/bootstrap3-editable/js/bootstrap-editable.min.js')}}"></script>
    <script>
        setTimeout(() => {
            $(".editable").editable();
        }, 200);
    </script>

    <div class="max-width-1200">
        <div class="ui-layout">
            <div class="flexbox-layout-sections" id="main-order-content">
                @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
                    <div class="ui-layout__section">
                        <div class="ui-layout__item">
                            <div class="ui-banner ui-banner--status-warning">
                                <div class="ui-banner__ribbon">
                                    <svg class="svg-next-icon svg-next-icon-size-20">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#alert-circle"></use>
                                    </svg>
                                </div>
                                <div class="ui-banner__content">
                                    <h2 class="ui-banner__title">{{ trans('plugins/ecommerce::order.cancel_order') }}</h2>
                                    <div class="ws-nm">
                                        {{ trans('plugins/ecommerce::order.order_was_canceled_at') }}
                                        <strong>{{ BaseHelper::formatDate($order->updated_at, 'H:i d/m/Y') }}</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="flexbox-layout-section-primary mt20">
                    <div class="ui-layout__item">
                        <div class="wrapper-content">
                            <div class="pd-all-20">
                                <div class="flexbox-grid-default">
                                    <div class="flexbox-auto-right mr5">
                                        <label class="title-product-main text-no-bold">
                                            {{ trans('plugins/ecommerce::order.order_information') }}
                                            {{ get_order_code($order->id) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="mt20">
                                    @if ($order->shipment->id)
                                        <svg
                                            class="svg-next-icon svg-next-icon-size-16 next-icon--right-spacing-quartered">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 xlink:href="#next-orders"></use>
                                        </svg>
                                        <strong class="ml5">{{ trans('plugins/ecommerce::order.completed') }}</strong>
                                    @else
                                        <svg class="svg-next-icon svg-next-icon-size-16 svg-next-icon-gray">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 xlink:href="#next-order-unfulfilled-16"></use>
                                        </svg>
                                        <strong class="ml5">{{ trans('plugins/ecommerce::order.completed') }}</strong>
                                    @endif
                                    <strong
                                        class="ml5">{{ $order->order_completion_date ? date('d M, Y', strtotime($order->order_completion_date)) : 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="pd-all-20 p-none-t border-top-title-main">
                                <div class="table-wrap">
                                    <table class="table-order table-divided">
                                        <tbody>
                                        {{--'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::ACTIVE,--}}
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
                                            if($product->is_variation){
                                             $pro = \Botble\Ecommerce\Models\ProductVariation::where('product_id',$product->id)->first();
                                             $url = $pro->configurable_product_id;
                                            }
                                            else{
                                                $url = $product->id;
                                            }
                                            @endphp

                                            <tr>
                                                @if ($product)

                                                    <td class="width-60-px min-width-60-px vertical-align-t">
                                                        <div class="wrap-img">
                                                            <a target="_blank"
                                                               href="{{route('products.edit',[$url])}}">
                                                                <img
                                                                    class="thumb-image thumb-image-cartorderlist"
                                                                    src="{{ RvMedia::getImageUrl($product->original_product->image, null, false, RvMedia::getDefaultImage()) }}"
                                                                ">
                                                            </a>
                                                        </div>
                                                    </td>


                                                @endif
                                                <td class="pl5 p-r5 min-width-200-px">
                                                    <a target="_blank" href="{{route('products.edit',[$url])}}">
                                                        {{ $orderProduct->product_name }}
                                                    </a>
                                                    {{--<a class="text-underline hover-underline pre-line" target="_blank" href="{{ route('products.edit', $orderProduct->product_id) }}" title="{{ $orderProduct->product_name }}">
                                                        {{ $orderProduct->product_name }}
                                                    </a>--}}
                                                    @if ($product)
                                                        &nbsp;
                                                        @if ($product->sku)
                                                            ({{ trans('plugins/ecommerce::order.sku') }} :
                                                            <strong>{{ $product->sku }}</strong>)
                                                        @endif
                                                        @if ($product->is_variation)
                                                            <p class="mb-0">
                                                                <small>
                                                                    @php $attributes = get_product_attributes($product->id) @endphp
                                                                    @if (!empty($attributes))
                                                                        @foreach ($attributes as $attribute)
                                                                            @if($attribute->attribute_set_title !== 'Size')
                                                                                {{ $attribute->attribute_set_title }}
                                                                                : {{ $attribute->title }}
                                                                                @if($attribute->title !== 'Single')
                                                                </small><small>
                                                                    Size
                                                                    : {{$orderProduct->product_sizes ? $orderProduct->product_sizes : $product->sizes}}
                                                                    @endif
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                </small>
                                                                <small>
                                                                    Per Piece:
                                                                    ${{($product->prod_pieces) ? $product->price / $product->prod_pieces :$product->price }}
                                                                </small>
                                                            </p>
                                                        @endif
                                                    @endif

                                                    @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                                                        @foreach($orderProduct->options as $option)
                                                            @if (!empty($option['key']) && !empty($option['value']))
                                                                <p class="mb-0">
                                                                    <small>{{ $option['key'] }}:
                                                                        <strong> {{ $option['value'] }}</strong>
                                                                    </small>
                                                                </p>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    {!! apply_filters(ECOMMERCE_ORDER_DETAIL_EXTRA_HTML, null) !!}
                                                    @if ($order->shipment->id)
                                                        <ul class="unstyled">
                                                            <li class="simple-note">
                                                                <a>
                                                                    <span>{{ $orderProduct->qty }}</span>
                                                                    <span
                                                                        class="text-lowercase"> {{ trans('plugins/ecommerce::order.completed') }}</span>
                                                                </a>
                                                                <ul class="dom-switch-target line-item-properties small">
                                                                    <li class="ws-nm">
                                                                        <span class="bull">↳</span>
                                                                        <span
                                                                            class="black">{{ trans('plugins/ecommerce::order.shipping') }} </span>

                                                                        <a class="text-underline bold-light"
                                                                           target="_blank"
                                                                           title="{{ $order->shipping_method_name }}"
                                                                           href="{{ route('ecommerce.shipments.edit', $order->shipment->id) }}">{{ $order->shipping_method_name }}</a>

                                                                    </li>
                                                                    <li class="ws-nm">
                                                                        <span class="bull">↳</span>
                                                                        <span
                                                                            class="black">{{ trans('plugins/ecommerce::order.warehouse') }}</span>
                                                                        <span
                                                                            class="bold-light">{{ $order->shipment->store->name ?? $defaultStore->name }}</span>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </td>
                                                <td class="pl5 p-r5 text-right">
                                                    <div class="inline_block">
                                                        <span>{{ format_price($orderProduct->price) }}</span>
                                                    </div>
                                                </td>
                                                <td class="pl5 p-r5 text-center">x</td>
                                                <td class="pl5 p-r5">
                                                    <span>{{ $orderProduct->qty }}</span>
                                                </td>
                                                <td class="pl5 text-right">{{ format_price($orderProduct->price * $orderProduct->qty) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="pd-all-20 p-none-t">
                                <div class="flexbox-grid-default block-rps-768">
                                    <div class="flexbox-auto-right p-r5"></div>
                                    <div class="flexbox-auto-right pl5">
                                        <div class="table-wrap">
                                            <table class="table-normal table-none-border table-color-gray-text">
                                                <tbody>
                                                <tr>
                                                    <td class="text-right color-subtext">{{ trans('plugins/ecommerce::order.sub_amount') }}</td>
                                                    <td class="text-right pl10">
                                                        <span>{{ format_price($order->sub_total) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right color-subtext mt10">
                                                        @if ($order->coupon_code)
                                                            <p class="mb0">
                                                                <i class="fa fa-trash"
                                                                   style="cursor: pointer; color: red"
                                                                   onclick="event.preventDefault(); document.getElementById('remove-discount').submit();"></i>
                                                                {!! trans('plugins/ecommerce::order.coupon_code', ['code' => Html::tag('strong', $order->coupon_code)->toHtml()])  !!}
                                                            </p>
                                                        @elseif ($order->discount_description)
                                                            <p class="mb0">
                                                                <i class="fa fa-trash"
                                                                   style="cursor: pointer; color: red"
                                                                   onclick="event.preventDefault(); document.getElementById('remove-discount').submit();"></i>
                                                                {{ $order->discount_description }}
                                                            </p>
                                                        @endif
                                                        @if ($order->promotion_applied && $order->promotion_amount)
                                                            <p class="mb0">
                                                                <i class="fa fa-trash"
                                                                   style="cursor: pointer; color: red"
                                                                   onclick="event.preventDefault(); document.getElementById('discount-type-ip').value = 'promotion'; document.getElementById('remove-discount').submit();"></i>
                                                                Promotion Discount
                                                            </p>
                                                        @endif
                                                        <p class="mb0">{{ trans('plugins/ecommerce::order.discount') }}</p>
                                                    </td>
                                                    <td class="text-right p-none-b pl10">
                                                        @if ($order->coupon_code)
                                                            <p class="mb0">{{ format_price($order->discount_amount - $order->promotion_amount) }}</p>
                                                        @endif
                                                        @if ($order->promotion_applied && $order->promotion_amount)
                                                            <p class="text-right p-none-b pl10 mb0">{{ format_price($order->promotion_amount) }}</p>
                                                        @endif
                                                        <p class="mb0">{{ format_price($order->discount_amount) }}</p>

                                                        <form id="remove-discount"
                                                              action="{{ route('orders.remove-discount', $order->id) }}"
                                                              style="display:none;">
                                                            <input type="hidden" name="discount_type" value="coupon"
                                                                   id="discount-type-ip">
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right color-subtext mt10">
                                                        <p class="mb0">{{ trans('plugins/ecommerce::order.shipping_fee') }}</p>
                                                        <p class="mb0 font-size-12px">{{ $order->shipping_method_name }}</p>
                                                        <p class="mb0 font-size-12px">{{ ecommerce_convert_weight($weight) }} {{ ecommerce_weight_unit(true) }}</p>
                                                    </td>
                                                    <td class="text-right p-none-t pl10">
                                                        <p class="mb0">{{ format_price($order->shipping_amount) }}</p>
                                                    </td>
                                                </tr>
                                                @if (EcommerceHelper::isTaxEnabled())
                                                    <tr>
                                                        <td class="text-right color-subtext mt10">
                                                            <p class="mb0">{{ trans('plugins/ecommerce::order.tax') }}</p>
                                                        </td>
                                                        <td class="text-right p-none-t pl10">
                                                            <p class="mb0">{{ format_price($order->tax_amount, $order->currency_id) }}</p>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="text-right mt10">
                                                        <p class="mb0 color-subtext">{{ trans('plugins/ecommerce::order.total_amount') }}</p>
                                                        @if ($order->payment->id)
                                                            <p class="mb0  font-size-12px"><a
                                                                    href="{{ route('payment.show', $order->payment->id) }}"
                                                                    target="_blank">{{ $order->payment->payment_channel->label() }}</a>
                                                            </p>
                                                        @endif
                                                    </td>
                                                    <td class="text-right text-no-bold p-none-t pl10">
                                                        @if ($order->payment->id)
                                                            <a href="{{ route('payment.show', $order->payment->id) }}"
                                                               target="_blank">
                                                                <span>{{ format_price($order->amount) }}</span>
                                                            </a>
                                                        @else
                                                            <span>{{ format_price($order->amount) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="border-bottom"></td>
                                                    <td class="border-bottom"></td>
                                                </tr>
                                                {{--                                                <tr>--}}
                                                {{--                                                    <td class="text-right color-subtext">{{ trans('plugins/ecommerce::order.paid_amount') }}</td>--}}
                                                {{--                                                    <td class="text-right color-subtext pl10">--}}
                                                {{--                                                        @if ($order->payment->id)--}}
                                                {{--                                                            <a href="{{ route('payment.show', $order->payment->id) }}"--}}
                                                {{--                                                               target="_blank">--}}
                                                {{--                                                                <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->payment->amount : 0) }}</span>--}}
                                                {{--                                                            </a>--}}
                                                {{--                                                        @else--}}
                                                {{--                                                            <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->payment->amount : 0) }}</span>--}}
                                                {{--                                                        @endif--}}
                                                {{--                                                    </td>--}}
                                                {{--                                                </tr>--}}
                                                @if ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::REFUNDED)
                                                    <tr class="hidden">
                                                        <td class="text-right color-subtext">{{ trans('plugins/ecommerce::order.refunded_amount') }}</td>
                                                        <td class="text-right pl10">
                                                            <span>{{ format_price($order->payment->amount) }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr class="hidden">
                                                    <td class="text-right color-subtext">{{ trans('plugins/ecommerce::order.amount_received') }}</td>
                                                    <td class="text-right pl10">
                                                        <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->amount : 0) }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        {{--                                        <div class="text-right">--}}
                                        {{--                                            <a href="{{ route('orders.generate-invoice', $order->id) }}"--}}
                                        {{--                                               class="btn btn-info">--}}
                                        {{--                                                <i class="fa fa-download"></i> {{ trans('plugins/ecommerce::order.download_invoice') }}--}}
                                        {{--                                            </a>--}}
                                        {{--                                        </div>--}}
                                        <div class="pd-all-20">
                                            <form action="{{ route('orders.edit', $order->id) }}">
                                                <label
                                                    class="text-title-field">{{ trans('plugins/ecommerce::order.note') }}</label>
                                                <textarea class="ui-text-area textarea-auto-height" name="description"
                                                          rows="3"
                                                          placeholder="{{ trans('plugins/ecommerce::order.add_note') }}">{{ $order->description }}</textarea>
                                                <label
                                                    class="text-title-field mt-2">Customer Notes</label>
                                                <textarea class="ui-text-area textarea-auto-height" name="notes"
                                                          rows="3"
                                                          placeholder="Customer Notes">{{ $order->notes }}</textarea>
                                                <div class="mt10">
                                                    <button type="button"
                                                            class="btn btn-primary btn-update-order">{{ trans('plugins/ecommerce::order.save') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            @if(count($order->refund_products) && $order->order_type == \Botble\Ecommerce\Models\Order::NORMAL)
                                <div class="pd-all-20 p-none-t border-top-title-main">
                                    <div class="flexbox-auto-right ml15 mr15 text-upper">
                                        <span>Refunded Products</span>
                                    </div>
                                    <div class="table-wrap">
                                        <table class="table-order table-divided">
                                            <tbody>
                                            @php $refund_total = 0; @endphp
                                            @foreach ($order->refund_products as $orderProduct)
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
                                                    @if ($product)
                                                        <td class="width-60-px min-width-60-px vertical-align-t">
                                                            <div class="wrap-img">
                                                                <img class="thumb-image thumb-image-cartorderlist"
                                                                     src="{{ RvMedia::getImageUrl($product->original_product->image, null, false, RvMedia::getDefaultImage()) }}">
                                                            </div>
                                                        </td>
                                                    @endif
                                                    <td class="pl5 p-r5 min-width-200-px">
                                                        {{ $orderProduct->product_name }}
                                                        @if ($product)
                                                            @if ($product->sku)
                                                                ({{ trans('plugins/ecommerce::order.sku') }} :
                                                                <strong>{{ $product->sku }}</strong>)
                                                            @endif
                                                            @if ($product->is_variation)
                                                                <p class="mb-0">
                                                                    <small>
                                                                        @php $attributes = get_product_attributes($product->id) @endphp
                                                                        @if (!empty($attributes))
                                                                            @foreach ($attributes as $attribute)
                                                                                @if($attribute->attribute_set_title !== 'Size')
                                                                                    {{ $attribute->attribute_set_title }}
                                                                                    : {{ $attribute->title }}
                                                                                    @if($attribute->title !== 'Single')
                                                                    </small><small>
                                                                        Size
                                                                        : {{$orderProduct->product_sizes ? $orderProduct->product_sizes : $product->sizes}}
                                                                        @endif
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                    </small>
                                                                    <small>
                                                                        Per Piece:
                                                                        ${{($product->prod_pieces) ? $product->price / $product->prod_pieces :$product->price }}
                                                                    </small>
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="pl5 p-r5 text-right">
                                                        <div class="inline_block">
                                                            <span>{{ format_price($orderProduct->price) }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="pl5 p-r5 text-center">x</td>
                                                    <td class="pl5 p-r5">
                                                        <span>{{ $orderProduct->qty }}</span>
                                                    </td>
                                                    <td class="pl5 text-right">{{ format_price($orderProduct->price * $orderProduct->qty) }}</td>
                                                    <td class="pl5 p-r5 text-center">
                                                        <a class="btn btn-primary"
                                                           href="{{route('orders.order.revert.refund.product', ['order_id' => $order->id, 'prod_id' => $orderProduct->product_id])}}">Revert</a>
                                                    </td>
                                                </tr>
                                                @php $refund_total += ($orderProduct->price * $orderProduct->qty); @endphp
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="pd-all-20 p-none-t">
                                    <div class="flexbox-grid-default block-rps-768">
                                        <div class="flexbox-auto-right p-r5"></div>
                                        <div class="flexbox-auto-right pl5">
                                            <div class="table-wrap">
                                                <table class="table-normal table-none-border table-color-gray-text">
                                                    <tbody>
                                                    <tr>
                                                        <td class="text-right color-subtext">{{ trans('plugins/ecommerce::order.total_amount') }}</td>
                                                        <td class="text-right pl10">
                                                            <span>{{ format_price($refund_total) }}</span>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif


                            <div class="pd-all-20 border-top-title-main">
                                <div class="flexbox-grid-default flexbox-align-items-center">
                                    <div class="flexbox-auto-left">
                                        <svg
                                            class="svg-next-icon svg-next-icon-size-20 @if ($order->is_confirmed) svg-next-icon-green @else svg-next-icon-gray @endif">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 xlink:href="#next-checkmark"></use>
                                        </svg>
                                    </div>
                                    <div class="flexbox-auto-right ml15 mr15 text-upper">
                                        @if ($order->is_confirmed)
                                            <span>{{ trans('plugins/ecommerce::order.order_was_confirmed') }}</span>
                                        @else
                                            <span>{{ trans('plugins/ecommerce::order.confirm_order') }}</span>
                                        @endif
                                    </div>
                                    @if (!$order->is_confirmed)
                                        <div class="flexbox-auto-left">
                                            <form action="{{ route('orders.confirm') }}">
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <button
                                                    class="btn btn-primary btn-confirm-order">{{ trans('plugins/ecommerce::order.confirm') }}</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="pd-all-20 border-top-title-main">
                                <div class="flexbox-grid-default flexbox-flex-wrap flexbox-align-items-center">
                                    @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
                                        <div class="flexbox-auto-left">
                                            <svg class="svg-next-icon svg-next-icon-size-24 svg-next-icon-gray">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     xlink:href="#next-error"></use>
                                            </svg>
                                        </div>
                                        <div class="flexbox-auto-content ml15 mr15 text-upper">
                                            <span>{{ trans('plugins/ecommerce::order.order_was_canceled') }}</span>
                                        </div>
                                    @elseif ($order->payment->id)
                                        <div class="flexbox-auto-left">
                                            @if (!$order->payment->status || $order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                                                <svg class="svg-next-icon svg-next-icon-size-24 svg-next-icon-gray">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#next-credit-card"></use>
                                                </svg>
                                            @elseif ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED || $order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                                                <svg class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#next-checkmark"></use>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flexbox-auto-content ml15 mr15 text-upper">
                                            @if (!$order->payment->status || $order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                                                <span>{{ trans('plugins/ecommerce::order.pending_payment') }}</span>
                                            @elseif ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED)
                                                <span>{{ trans('plugins/ecommerce::order.payment_was_accepted', ['money' => format_price($order->payment->amount - $order->payment->refunded_amount)]) }}</span>
                                            @elseif ($order->payment->amount - $order->payment->refunded_amount == 0)
                                                <span>{{ trans('plugins/ecommerce::order.payment_was_refunded') }}</span>
                                            @endif
                                        </div>
                                        @if (!$order->payment->status || in_array($order->payment->status, [\Botble\Payment\Enums\PaymentStatusEnum::PENDING]))
                                            <div class="flexbox-auto-left">
                                                <button class="btn btn-primary btn-trigger-confirm-payment"
                                                        data-target="{{ route('orders.confirm-payment', $order->id) }}">{{ trans('plugins/ecommerce::order.confirm_payment') }}</button>
                                            </div>
                                        @endif
                                        @if ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED && (($order->payment->amount - $order->payment->refunded_amount > 0) || ($order->products->sum('qty') - $order->products->sum('restock_quantity') > 0)))
                                            <div class="flexbox-auto-left">
                                                <button
                                                    class="btn btn-secondary ml10 btn-trigger-refund">{{ trans('plugins/ecommerce::order.refund') }}</button>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if($order->payment->status != \Botble\Payment\Enums\PaymentStatusEnum::REFUNDED)
                                <div class="pd-all-20 border-top-title-main">
                                    <div class="flexbox-grid-default flexbox-flex-wrap flexbox-align-items-center">
                                        @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED && !$order->shipment->id)
                                            <div class="flexbox-auto-left">
                                                <svg class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#next-checkmark"></use>
                                                </svg>
                                            </div>
                                            <div class="flexbox-auto-content ml15 mr15 text-upper">
                                                <span>{{ trans('plugins/ecommerce::order.all_products_are_not_delivered') }}</span>
                                            </div>
                                        @else
                                            @if ($order->shipment->id)
                                                <div class="flexbox-auto-left">
                                                    <svg
                                                        class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                             xlink:href="#next-checkmark"></use>
                                                    </svg>
                                                </div>
                                                <div class="flexbox-auto-content ml15 mr15 text-upper">
                                                    {{--<span>{{ trans('plugins/ecommerce::order.delivery') }}</span>--}}
                                                    <span>Shipping</span>
                                                </div>
                                            @else
                                                <div class="flexbox-auto-left">
                                                    <svg class="svg-next-icon svg-next-icon-size-24 svg-next-icon-gray">
                                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                             xlink:href="#next-shipping"></use>
                                                    </svg>
                                                </div>
                                                <div class="flexbox-auto-content ml15 mr15 text-upper">
                                                    {{--<span>{{ trans('plugins/ecommerce::order.delivery') }}</span>--}}
                                                    <span>Shipping</span>
                                                </div>
                                                <div class="flexbox-auto-left">
                                                    <div class="item">
                                                        <button class="btn btn-primary btn-trigger-shipment"
                                                                data-target="{{ route('orders.get-shipment-form', $order->id) }}">
                                                            {{--{{ trans('plugins/ecommerce::order.delivery') }}--}}
                                                            Shipping
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @if (!$order->shipment->id)
                                    <div class="shipment-create-wrap hidden"></div>
                                @else
                                    @if($order->products->count() > 0)
                                        @include('plugins/ecommerce::orders.shipment-detail', ['shipment' => $order->shipment])
                                    @endif
                                @endif
                            @endif

                        </div>
                        <div class="mt20 mb20">
                            <div>
                                <div class="comment-log ws-nm">
                                    <div class="comment-log-title">
                                        <label
                                            class="bold-light m-xs-b hide-print">{{ trans('plugins/ecommerce::order.history') }}</label>
                                    </div>
                                    <div class="comment-log-timeline">
                                        <div class="column-left-history ps-relative" id="order-history-wrapper">
                                            @foreach ($order->histories()->orderBy('id', 'DESC')->get() as $history)
                                                <div class="item-card">
                                                    <div class="item-card-body clearfix">
                                                        <div
                                                            class="item comment-log-item comment-log-item-date ui-feed__timeline">
                                                            <div class="ui-feed__item ui-feed__item--message">
                                                                <span
                                                                    class="ui-feed__marker @if ($history->user_id) ui-feed__marker--user-action @endif"></span>
                                                                <div class="ui-feed__message">
                                                                    <div class="timeline__message-container">
                                                                        <div class="timeline__inner-message">
                                                                            @if (in_array($history->action, ['confirm_payment', 'refund']))
                                                                                <a href="#"
                                                                                   class="text-no-bold show-timeline-dropdown hover-underline"
                                                                                   data-target="#history-line-{{ $history->id }}">
                                                                                    <span>{{ OrderHelper::processHistoryVariables($history) }}</span>
                                                                                </a>
                                                                            @else
                                                                                <span>{{ OrderHelper::processHistoryVariables($history) }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <time class="timeline__time">
                                                                            <span>{{ date('d M, Y', strtotime($history->created_at)) }}</span>
                                                                        </time>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($history->action == 'refund' && Arr::get($history->extras, 'amount', 0) > 0)
                                                                <div class="timeline-dropdown"
                                                                     id="history-line-{{ $history->id }}">
                                                                    <table>
                                                                        <tbody>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.order_number') }}</th>
                                                                            <td>
                                                                                <a href="{{ route('orders.edit', $order->id) }}"
                                                                                   title="{{ get_order_code($order->id) }}">{{ get_order_code($order->id) }}</a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.description') }}</th>
                                                                            <td>{{ $history->description . ' ' . trans('plugins/ecommerce::order.from') . ' ' . $order->payment->payment_channel->label() }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.amount') }}</th>
                                                                            <td>{{ format_price(Arr::get($history->extras, 'amount', 0)) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.status') }}</th>
                                                                            <td>{{ trans('plugins/ecommerce::order.successfully') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.transaction_type') }}</th>
                                                                            <td>{{ trans('plugins/ecommerce::order.refund') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.staff') }}</th>
                                                                            <td>{{ $order->payment->user->getFullName() ? $order->payment->user->getFullName() : trans('plugins/ecommerce::order.n_a') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.refund_date') }}</th>
                                                                            <td>{{ date('d M, Y', strtotime($history->created_at)) }}</td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                            @if ($history->action == 'confirm_payment' && $order->payment)
                                                                <div class="timeline-dropdown"
                                                                     id="history-line-{{ $history->id }}">
                                                                    <table>
                                                                        <tbody>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.order_number') }}</th>
                                                                            <td>
                                                                                <a href="{{ route('orders.edit', $order->id) }}"
                                                                                   title="{{ get_order_code($order->id) }}">{{ get_order_code($order->id) }}</a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.description') }}</th>
                                                                            <td>{!! trans('plugins/ecommerce::order.mark_payment_as_confirmed', ['method' => $order->payment->payment_channel->label()]) !!}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.transaction_amount') }}</th>
                                                                            <td>{{ format_price($order->payment->amount) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.payment_gateway') }}</th>
                                                                            <td>{{ $order->payment->payment_channel->label() }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.status') }}</th>
                                                                            <td>{{ trans('plugins/ecommerce::order.successfully') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.transaction_type') }}</th>
                                                                            <td>{{ trans('plugins/ecommerce::order.confirm') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.staff') }}</th>
                                                                            <td>{{ $order->payment->user->getFullName() ? $order->payment->user->getFullName() : trans('plugins/ecommerce::order.n_a') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ trans('plugins/ecommerce::order.payment_date') }}</th>
                                                                            <td>{{ date('d M, Y', strtotime($history->created_at)) }}</td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                            @if ($history->action == 'send_order_confirmation_email')
                                                                <div class="ui-feed__item ui-feed__item--action">
                                                                    <span class="ui-feed__spacer"></span>
                                                                    <div class="timeline__action-group">
                                                                        <a href="#"
                                                                           class="btn hide-print timeline__action-button hover-underline btn-trigger-resend-order-confirmation-modal"
                                                                           data-action="{{ route('orders.send-order-confirmation-email', $history->order_id) }}">{{ trans('plugins/ecommerce::order.resend') }}</a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="flexbox-layout-section-secondary mt20">
                    <div class="ui-layout__item">
                        <div class="wrapper-content bg-gray-white mb20">
                            <div class="pd-all-20">
                                <?php
                                $next = $order->nextOrder();
                                $previous = $order->previousOrder();
                                ?>
                                <a href="{{ !is_null($next) ? route('orders.edit', ['order' => $next]) : 'javascript:void(0);' }}"
                                   class="btn btn-default order-btn-pre" {{ is_null($previous) ? 'disabled' : '' }}><i
                                        class="fa fa-angle-left"></i>&nbsp;&nbsp;</a>&nbsp;
                                <a href="{{ !is_null($previous) ? route('orders.edit', ['order' => $previous]) : 'javascript:void(0);' }}"
                                   class="btn btn-default order-btn-pre" {{ is_null($next) ? 'disabled' : '' }}>&nbsp;&nbsp;<i
                                        class="fa fa-angle-right"></i></a> &nbsp;
                            </div>
                        </div>
                        <div class="wrapper-content bg-gray-white mb20">
                            <div class="pd-all-20">

                                @if($order->payment->payment_channel == 'paypal')
                                    @if($order->payment && @$order->payments()->where('type', 1)->latest('id')->first()->status != 'completed')
                                        <a href="{{ route('orders.orderInvoice', ['id' => $order->id, 'type' => 1]) }}"
                                           class="btn btn-primary mb-2">
                                            {{$order->payment && @$order->payments()->where('type', 1)->latest('id')->first()->paypal_invoice_id ? 'Order Reminder' : 'Order Invoice'}}
                                        </a>&nbsp;&nbsp;
                                    @elseif($order->payment && @$order->payments()->where('type', 1)->latest('id')->first()->status == 'completed')
                                        <button type="button" class="btn btn-outline-success mb-2">Order Invoice Paid
                                        </button>
                                    @endif

                                    @if($order->shipping_amount > 0)
                                        @if($order->payment && @$order->payments()->where('type', 0)->latest('id')->first()->status != 'completed')
                                            <a href="{{ route('orders.orderInvoice', ['id' => $order->id, 'type' => 0]) }}"
                                               class="btn btn-warning mb-2">
                                                {{$order->payment && @$order->payments()->where('type', 0)->latest('id')->first()->paypal_invoice_id ? 'Shipping Reminder' : 'Shipping Invoice'}}
                                            </a>&nbsp;&nbsp;
                                        @elseif($order->payment && @$order->payments()->where('type', 0)->latest('id')->first()->status == 'completed')
                                            <button type="button" class="btn btn-outline-success mb-2">Shipping Invoice
                                                Paid
                                            </button>
                                        @endif
                                    @endif
                                @endif

                                @if($order->order_type == \Botble\Ecommerce\Models\Order::NORMAL)
                                    <button type="button" class="btn btn-outline-primary mb-2" data-toggle="modal"
                                            data-target="#modal_order_refund_product">Refund Order
                                    </button>
                                @endif

                                <button type="button" class="btn btn-outline-warning mb-2" data-toggle="modal"
                                        data-target="#modal_split_payment">Split Payment
                                </button>
                                <button type="button" class="btn btn-outline-danger mb-2" data-toggle="modal"
                                        data-target="#modal_split_order">Split Order
                                </button>&nbsp;&nbsp;
                                <a href="{{ route('orders.printReceipt', json_encode([$order->id])) }}"
                                   class="btn btn-success mb-2">Print Order</a>&nbsp;&nbsp;
                                <a href="{{ route('orders.editOrder', ['id' => $order->id]) }}"
                                   class="btn btn-warning mb-2">Edit Order</a>&nbsp;&nbsp;
                                {{--                                <a href="{{ route('orders.reorder', ['order_id' => $order->id]) }}"--}}
                                {{--                                   class="btn btn-info mb-2">{{ trans('plugins/ecommerce::order.reorder') }}</a>&nbsp;&nbsp;--}}
                                @if (!in_array($order->status, [\Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED, \Botble\Ecommerce\Enums\OrderStatusEnum::COMPLETED]))
                                    <a href="#" class="btn  mb-2 btn-secondary btn-trigger-cancel-order"
                                       data-target="{{ route('orders.cancel', $order->id) }}">{{ trans('plugins/ecommerce::order.cancel') }}</a>
                                @endif

                                <a data-type="select"
                                   data-source="{{ json_encode(get_order_statuses()) }}"
                                   data-pk="{{ $order->id }}"
                                   data-url="{{ route('orders.changeStatus') }}"
                                   data-value="{{ strtolower($order->status) }}"
                                   data-title="Change Status"
                                   class="editable"
                                   href="#">
                                    {{ strtoupper($order->status) }}
                                </a>

                                <a href="{{ route('orders.fetchOldSystemOrder', ['id' => $order->id]) }}" class="btn btn-warning mb-2">Fetch Order</a>

                            </div>
                        </div>


                        @if($order->split_payment)
                            @foreach($order->split_payments as $split_payment)
                                @if($split_payment->amount)
                                    @if(in_array($split_payment->payment_type, ['cash_payment','cheque_payment']))
                                        <div class="wrapper-content bg-gray-white mb20">
                                            <div class="row m-0 pt-3 pb-3 bg-white">
                                                <div class="col-lg-9">
                                                    <strong
                                                        class="mb-2">{{str_replace('_payment', '', ucwords($split_payment->payment_type))}}</strong>
                                                </div>
                                                <div class="col-lg-3 text-right">
                                                    <strong class="mb-2">$ {{$split_payment->amount}}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="wrapper-content bg-gray-white mb20">
                                            <div class="row m-0 pt-3 pb-3 bg-white">
                                                <div class="col-lg-9">
                                                    <strong>{{$cards[str_replace('card_', '', $split_payment->payment_type)]??''}}</strong>
                                                </div>
                                                <div class="col-lg-3 text-right">
                                                    <strong class="mb-2">$ {{$split_payment->amount}}</strong>
                                                </div>
                                            </div>
                                            <?php $order_card_preauth = \App\Models\CardPreAuth::where(['order_id' => $order->id, 'card_id' => str_replace('card_', '', $split_payment->payment_type)])->first(); ?>
                                            @if($order_card_preauth == null)
                                                <div class="pd-all-20 bg-white">
                                                    <form action="{{route('orders.charge')}}" method="POST">
                                                        @csrf
                                                        <input type="hidden"
                                                               value="{{str_replace('card_', '', $split_payment->payment_type)}}"
                                                               name="payment_id">
                                                        <input type="hidden" value="{{$order->id}}" name="order_id">
                                                        <input type="hidden" value="{{$split_payment->amount}}"
                                                               name="sub_total">
                                                        <input type="hidden" value="{{$split_payment->amount}}"
                                                               name="amount">
                                                        <button type="submit" class="btn btn-info">Create Payment
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($order_card_preauth->status == 0 && $order_card_preauth->transaction_id)
                                                <div class="pd-all-20">
                                                    <form action="{{route('orders.capture')}}" method="POST">
                                                        @csrf
                                                        <input type="hidden"
                                                               value="{{$order_card_preauth->transaction_id}}"
                                                               name="transaction_id">
                                                        <input type="hidden" value="{{$split_payment->amount}}"
                                                               name="amount">
                                                        <label class="col-lg-12">
                                                            <strong>Transaction ID : </strong>
                                                            {{$order_card_preauth->transaction_id}}
                                                        </label>
                                                        <button type="submit" class="btn btn-info">
                                                            Capture Payment
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($order_card_preauth->status == 1)
                                                <div class="wrapper-content bg-gray-white mb20">
                                                    <div class="pd-all-20">
                                                        <div class="p-b10">
                                                            <strong>Payment Status</strong>
                                                            <ul class="p-sm-r mb-0">
                                                                <li class="ws-nm">
                                                                    <span class="ww-bw text-no-bold">
                                                                        <button class="btn btn-info">Captured</button>
                                                                    </span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(@$order_card_preauth->payment_status == 'Declined')
                                                <div class="wrapper-content bg-gray-white mb20">
                                                    <div class="pd-all-20">
                                                        <div class="p-b10">
                                                            <strong>Declined Reason</strong>
                                                            <ul class="p-sm-r mb-0">
                                                                <li class="ws-nm">
                                                                    <span class="ww-bw text-no-bold">
                                                                        {{$order_card_preauth->response}}
                                                                    </span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @else
                            @if(in_array($order->payment->payment_channel->label(), ['omni-payment', 'omni_payment']))
                                <div class="wrapper-content bg-gray-white mb20">

                                    <!-- card -->
                                    @if($order->preauth == null || !$order->order_cards_preauth()->where('transaction_id', '!=', "")->value('transaction_id'))
                                        <div class="row m-0 pt-4 bg-white">
                                            <div class="col-lg-12 ">
                                                <span class="mb-2">Card</span>
                                                {!!Form::select('card_list', $cards, @$order->order_card, ['class' => 'form-control selectpicker card_list','id'    => 'card_id',])!!}
                                            </div>
                                        </div>

                                        <div class="add_card bg-white">

                                            <div class="row group m-0 pt-4 ">
                                                @isset($order->user->billingAddress)
                                                    <label class="col-lg-12 ">
                                                        <span class="mb-2">Billing Address</span>
                                                        {!! Form::select('billing_address', $order->user->billingAddress->pluck('address', 'id'), @$order->billingAddress->customer_address_id ,['class' => 'form-control selectpicker','id'   => 'billing_address','data-live-search'=>'true', 'placeholder'=>'Select Address']) !!}
                                                    </label>
                                                @endisset
                                            </div>

                                            <div class="group row m-0">
                                                <label class="col-lg-12">
                                                    <div id="card-element" class="field">
                                                        <span>Card</span>
                                                        <div id="fattjs-number" style="height: 35px"></div>
                                                        <span class="mt-2">CVV</span>
                                                        <div id="fattjs-cvv" style="height: 35px"></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="row m-0">
                                                <div class="col-lg-3">
                                                    <input name="month" size="3" maxlength="2" placeholder="MM"
                                                           class="form-control month">
                                                </div>
                                                <p class="mt-2"> / </p>
                                                <div class="col-lg-3">
                                                    <input name="year" size="5" maxlength="4" placeholder="YYYY"
                                                           class="form-control year">
                                                </div>
                                            </div>
                                            {{--<button class="btn btn-info mt-3" id="paybutton">Pay $1</button>--}}
                                            <div class="row m-0">
                                                <div class="col-lg-6">
                                                    <button class="btn btn-success mt-3" id="tokenizebutton">Add Credit
                                                        Card
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row m-0">
                                                <div class="col-lg-12">
                                                    <div class="outcome">
                                                        <div class="error"></div>
                                                        <div class="success">
                                                            Successful! The ID is
                                                            <span class="token"></span>
                                                        </div>
                                                        <div class="loader" style="margin: auto"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--                                        @if($order->preauth != null || $order->order_cards_preauth()->where('transaction_id', '!=', "")->value('transaction_id'))--}}
                                        <div class="pd-all-20 bg-white">
                                            <form action="{{route('orders.charge')}}" method="POST">
                                                @csrf
                                                <input type="hidden" value="" name="payment_id" class="payment_id">
                                                <input type="hidden" value="{{$order->id}}" name="order_id"
                                                       class="order_id">
                                                <input type="hidden" value="{{$order->sub_total}}" name="sub_total">
                                                <input type="hidden" value="{{$order->amount}}" name="amount">
                                                <button type="submit" class="btn btn-info">Create Payment</button>
                                            </form>
                                        </div>
                                        {{--                                        @endif()--}}

                                    @elseif($order->order_cards_preauth()->where('transaction_id', '!=', "")->value('status') == 0)

                                        <div class="capture_card">
                                            <div class="row group"></div>
                                            <div class="group row"></div>
                                        </div>
                                        <div class="pd-all-20">
                                            <form action="{{route('orders.capture')}}" method="POST">
                                                @csrf
                                                <input type="hidden"
                                                       value="{{$order->order_cards_preauth()->where('transaction_id', '!=', "")->value('transaction_id')}}"
                                                       name="transaction_id">
                                                <input type="hidden" value="{{$order->amount}}" name="amount">
                                                <label class="col-lg-12"> <strong>Transaction ID
                                                        : </strong>{{$order->order_cards_preauth()->where('transaction_id', '!=', "")->value('transaction_id')}}
                                                </label>
                                                <button type="submit" class="btn btn-info">Capture Payment</button>
                                            </form>
                                        </div>
                                    @else($order->preauth->status == 1)
                                        <div class="wrapper-content bg-gray-white mb20">
                                            <div class="pd-all-20">
                                                <div class="p-b10">
                                                    <strong>Payment Status</strong>
                                                    <ul class="p-sm-r mb-0">
                                                        <li class="ws-nm">
                                                        <span class="ww-bw text-no-bold">
                                                            <button class="btn btn-info">Captured</button>
                                                        </span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($order->status == 'Declined')
                                        <div class="wrapper-content bg-gray-white mb20">
                                            <div class="pd-all-20">
                                                <div class="p-b10">
                                                    <strong>Declined Reason</strong>
                                                    <ul class="p-sm-r mb-0">
                                                        <li class="ws-nm">
                                                            <span
                                                                class="ww-bw text-no-bold">{{$order->transaction_error}}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @elseif($order->payment->payment_channel == 'paypal')
                                <div class="wrapper-content bg-gray-white mb20">
                                    <div class="row m-0 pt-4 bg-white">
                                        <div class="col-lg-12 ">
                                            <strong class="mb-2">Paid with Paypal</strong>
                                            <a href="{{'mailto:'.$order->payment->paypal_email}}">
                                                <span class="asd">{{$order->payment->paypal_email}}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @elseif($order->payment->payment_channel->label() != "")
                                <div class="wrapper-content bg-gray-white mb20">
                                    <div class="row m-0 pt-3 pb-3 bg-white">
                                        <div class="col-lg-12 ">
                                            <strong class="mb-2">{{$order->payment->payment_channel->label()}}</strong>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="wrapper-content bg-gray-white mb20">
                                    <div class="row m-0 pt-3 pb-3 bg-white">
                                        <div class="col-lg-12 ">
                                            <strong class="mb-2">Cash on Delivery</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif


                        <div class="wrapper-content mb20">
                            <div class="next-card-section p-none-b">
                                <div class="flexbox-grid-default flexbox-align-items-center">
                                    <div class="flexbox-auto-content-left">
                                        <label
                                            class="title-product-main text-no-bold">{{ trans('plugins/ecommerce::order.customer_label') }}</label>
                                    </div>
                                    <div class="flexbox-auto-left">
                                        <img class="width-30-px radius-cycle" width="40"
                                             src="{{ $order->user->id ? $order->user->avatar_url : $order->address->avatar_url }}"
                                             alt="{{ $order->address->name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="next-card-section border-none-t">
                                <div class="mb5">
                                    <a href=" {{route('customer.edit', $order->user->id)}}">
                                        <strong
                                            class="text-capitalize">{{ $order->user->name ? $order->user->name : $order->address->name }}</strong>
                                    </a>
                                </div>
                                @if ($order->user->id)
                                    <div>
                                        <i class="fas fa-inbox mr5"></i>
                                        <span>{{ $order->user->orders()->count() }}</span>
                                        {{ trans('plugins/ecommerce::order.orders') }}
                                    </div>
                                @endif
                                <ul class="ws-nm text-infor-subdued">
                                    <li class="overflow-ellipsis">
                                        <a class="hover-underline"
                                           href="mailto:{{ $order->user->email ? $order->user->email : $order->address->email }}">
                                            {{ $order->user->email ? $order->user->email : $order->address->email }}
                                        </a>
                                    </li>
                                    @if ($order->user->id)
                                        <li>
                                            <div>{{ trans('plugins/ecommerce::order.have_an_account_already') }}</div>
                                        </li>
                                    @else
                                        <li>
                                            <div>{{ trans('plugins/ecommerce::order.dont_have_an_account_yet') }}</div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="next-card-section">
                                <div class="flexbox-grid-default flexbox-align-items-center">
                                    <div class="flexbox-auto-content-left">
                                        <label
                                            class="title-text-second"><strong>{{ trans('plugins/ecommerce::order.shipping_address') }}</strong></label>
                                    </div>
                                    <div class="flexbox-auto-content-right text-right">
                                        <a href="#" class="btn-trigger-update-shipping-address">
                                        <span data-placement="top" title="" data-toggle="tooltip"
                                              data-original-title="{{ trans('plugins/ecommerce::order.update_address') }}">
                                            <svg class="svg-next-icon svg-next-icon-size-12">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     xlink:href="#next-edit"></use>
                                            </svg>
                                        </span>
                                        </a>
                                    </div>
                                </div>
                                <div>
                                    <ul class="ws-nm text-infor-subdued shipping-address-info">
                                        @include('plugins/ecommerce::orders.shipping-address.detail', ['address' => $order->address,'company'=>@$order->user->detail->company])

                                        <hr>
                                        <div class="flexbox-auto-content-left">
                                            <label
                                                class="title-text-second"><strong>Billing Address</strong></label>
                                        </div>
                                        <li>{{ @$order->user->detail->company }}</li>
                                        <li>{{ @$order->billingAddress->name }}</li>
                                        @if (@$order->billingAddress->phone)
                                            <li>
                                                <a href="tel:{{ @$order->billingAddress->phone }}">
                                                    <span><i class="fa fa-phone-square cursor-pointer mr5"></i></span>
                                                    <span>{{ @$order->billingAddress->phone }}</span>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            @if (@$order->billingAddress->address)
                                                <div>{{ $order->billingAddress->address }}</div>
                                            @endif
                                            @if (@$order->billingAddress->city)
                                                <div>{{ $order->billingAddress->city }}</div>
                                            @endif
                                            @if (@$order->billingAddress->state)
                                                <div>{{ $order->billingAddress->state }}</div>
                                            @endif
                                            @if (@$order->billingAddress->country_name)
                                                <div>{{ $order->billingAddress->country_name }}</div>
                                            @endif
                                            @if (@$order->billingAddress->zip_code)
                                                <div>{{ $order->billingAddress->zip_code }}</div>
                                            @endif

                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <div class="wrapper-content bg-gray-white mb20">
                            <div class="pd-all-20">
                                <div class="p-b10">
                                    <strong>{{ trans('plugins/ecommerce::order.warehouse') }}</strong>
                                    <ul class="p-sm-r mb-0">
                                        <li class="ws-nm">
                                            <span
                                                class="ww-bw text-no-bold">{{ $defaultStore->name ?? trans('plugins/ecommerce::order.default_store') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="wrapper-content bg-gray-white mb20">
                            <div class="pd-all-20">
                                <div class="d-flex">
                                    <input class="form-control" type="text" id="salesperson_id"
                                           value="{{@$order->salesperson->username}}"
                                           disabled>
                                    <i style="padding: 10px; background: #bbb;" class="fa fa-plus" data-toggle="modal"
                                       data-target="#salesrep"></i>
                                </div>
                                <input type="hidden" id="customer_id" value="{{$order->user_id}}">
                                <form action="{{ route('orders.edit', $order->id) }}">
                                    <label class="text-title-field mt-3">Tracking No.</label>
                                    <input class="form-control" name="tracking_no" placeholder="Tracking No."
                                           value="{{ $order->tracking_no }}"/>
                                    <label class="text-title-field mt-3">PO No.</label>
                                    <input class="form-control" name="po_number" placeholder="PO No."
                                           value="{{ $order->po_number }}"/>
                                    <div class="mt10">
                                        <button type="button"
                                                class="btn btn-primary btn-update-order">{{ trans('plugins/ecommerce::order.save') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_split_payment" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="d-flex w-100">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                        <h4 class="modal-title text-center w-100 thread-pop-head color-white">
                            Split Payment#{{$order->id}}
                            <span class="variation-name"></span>
                        </h4>
                    </div>
                </div>

                <form method="post" action="{{route('orders.split.payment', $order->id)}}">
                    @csrf
                    <div class="modal-body">
                        <div class="col-md-12 split-payment">
                            <label>Total Amount <strong>$ {{$order->amount}}</strong></label><br>
                            <label>Paid Amount <strong>$ {{$paidAmount}}</strong></label><br>
                            <label>Rem. Amount <strong>$ {{$order->amount - $paidAmount}}</strong></label><br>
                        </div>
                        <div class="col-md-12 split-payment">
                            <label>Cash $</label>
                            {!! Form::number('cash_payment', @$split_payments['cash_payment'], ['class' => 'form-control', 'placeholder'=>'Cash Payment', 'step' => 0.01, 'max' => ($order->amount - $paidAmount)]) !!}
                        </div>
                        <div class="col-md-12 split-payment">
                            <label>Cheque $</label>
                            {!! Form::number('cheque_payment', @$split_payments['cheque_payment'], ['class' => 'form-control', 'placeholder'=>'Cheque Payment', 'step' => 0.0, 'max' => ($order->amount - $paidAmount)]) !!}
                        </div>
                        @foreach($cards as $key => $value)
                            @php
                                $checkPaid = \App\Models\OrderSplitPayment::where(['order_id' => $order->id, 'payment_type' => 'card_'.$key])->value('status');
                            @endphp
                            @if($key)
                                <div class="col-md-12 split-payment">
                                    <label>{{$value}} $</label>
                                    @if($checkPaid == 'paid')
                                        {{@$split_payments['card_'.$key]}} (paid)
                                    @else
                                        {!! Form::number('card_'.$key, @$split_payments['card_'.$key], ['class' => 'form-control', 'placeholder'=>'Enter Payment', 'step' => 0.01, 'max' => ($order->amount - $paidAmount)]) !!}
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_split_order" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="d-flex w-100">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                        <h4 class="modal-title text-center w-100 thread-pop-head color-white">
                            Split Order#{{$order->id}}
                            <span class="variation-name"></span>
                        </h4>
                    </div>
                </div>

                <form method="post" action="{{route('orders.split.order', $order->id)}}">
                    @csrf
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center"> Quantity</th>
                                <th></th>
                                <th class="text-center">Quantity Moved</th>
                            </tr>
                            </thead>
                            <tbody class="">
                            {{--'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::ACTIVE,--}}
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
                                        ],
                                    ]);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <img class="split-img"
                                                 src="{{ RvMedia::getImageUrl(@$product->original_product->image, null, false, RvMedia::getDefaultImage()) }}"/>
                                            <div class="ml-3">
                                                <p class="split-head m-0">{{ $orderProduct->product_name }}</p>
                                                <p class="split-code">SKU: {{ @$product->sku }}</p>
                                                {{--<p class="split-opt m-0"><b>Options:</b></p>--}}
                                                @php $attributes = get_product_attributes($product->id) @endphp
                                                @if (!empty($attributes))
                                                    @foreach ($attributes as $attribute)
                                                        {{ $attribute->attribute_set_title }}: {{ $attribute->title }}
                                                        @if (!$loop->last), @endif
                                                    @endforeach
                                                @endif
                                                {{--<a class="split-link" href="#">landbapparel.com</a>--}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input name="order_prod[{{$product->id}}]" type="number" step="1" min="0"
                                               max="{{ $orderProduct->qty }}" value="{{ $orderProduct->qty }}"
                                               data-prod-id="{{$product->id}}" data-prod-qty="{{$orderProduct->qty}}"
                                               id="split-input-{{$product->id}}" class="split-input"/>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn-default split-btn"
                                                data-prod-id="{{$product->id}}">-->
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <input name="order_prod_move[{{$product->id}}]" type="number" step="1" min="0"
                                               max="{{ $orderProduct->qty }}" value="0" data-prod-id="{{$product->id}}"
                                               id="split-input2-{{$product->id}}" class="split-input2"/>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_order_refund_product" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="d-flex w-100">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                        <h4 class="modal-title text-center w-100 thread-pop-head color-white">
                            Order #{{$order->id}} Refund Product
                            <span class="variation-name"></span>
                        </h4>
                    </div>
                </div>

                <form method="post" action="{{route('orders.order.refund.product', $order->id)}}">
                    @csrf
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center"> Quantity</th>
                                <th></th>
                                <th class="text-center">Quantity Moved</th>
                            </tr>
                            </thead>
                            <tbody class="">
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
                                        ],
                                    ]);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <img class="split-img"
                                                 src="{{ RvMedia::getImageUrl(@$product->original_product->image, null, false, RvMedia::getDefaultImage()) }}"/>
                                            <div class="ml-3">
                                                <p class="split-head m-0">{{ $orderProduct->product_name }}</p>
                                                <p class="split-code">SKU: {{ @$product->sku }}</p>
                                                @php $attributes = get_product_attributes($product->id) @endphp
                                                @if (!empty($attributes))
                                                    @foreach ($attributes as $attribute)
                                                        {{ $attribute->attribute_set_title }}: {{ $attribute->title }}
                                                        @if (!$loop->last), @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input name="order_refund_prod[{{$product->id}}]" type="number" step="1" min="0"
                                               max="{{ $orderProduct->qty }}" value="{{ $orderProduct->qty }}"
                                               data-refund-prod-id="{{$product->id}}"
                                               data-refund-prod-qty="{{$orderProduct->qty}}"
                                               id="split-refund-input-{{$product->id}}" class="split-refund-input"/>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn-default split-refund-btn"
                                                data-refund-prod-id="{{$product->id}}">-->
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <input name="order_refund_prod_move[{{$product->id}}]" type="number" step="1"
                                               min="0"
                                               max="{{ $orderProduct->qty }}" value="0"
                                               data-refund-prod-id="{{$product->id}}"
                                               id="split-refund-input2-{{$product->id}}" class="split-refund-input2"/>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="salesrep" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="d-flex w-100">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                        <h4 class="modal-title text-center w-100 thread-pop-head color-white">
                            Sales Rep {{@$order->salesperson->username}}
                        </h4>
                    </div>
                </div>

                <form method="post" action="{{route('orders.sales-rep', $order->id)}}">
                    @csrf
                    <div class="modal-body">
                        {!! Form::select('salesperson_id', $salesRep, $order->salesperson_id, ['class' => 'form-control','placeholder'=>'Select Sales Rep']) !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function () {
            getCustomer();
            getbillingadress();
        }, 200);

        $(document).ready(function () {
            $('body').on('click', 'button.split-btn', function () {
                let prodId = $(this).data('prod-id');
                let input = 'input#split-input-' + prodId;
                let maxQty = $(input).data('prod-qty');
                let qty = $(input).val();
                $('input#split-input2-' + prodId).val(qty);
                $(input).val(maxQty - qty);
            });
            $('body').on('change', 'input.split-input', function () {
                let prodId = $(this).data('prod-id');
                let maxQty = $(this).data('prod-qty');
                let curVal = $(this).val();
                let final = maxQty - curVal;
                let input = 'input#split-input2-' + prodId;
                $(input).val(final);
            });
            $('body').on('change', 'input.split-input2', function () {
                let prodId = $(this).data('prod-id');
                let input = 'input#split-input-' + prodId;
                let maxQty = $(input).data('prod-qty');
                let curVal = $(this).val();
                let final = maxQty - curVal;
                $(input).val(final);
            });
            $('#modal_split_order').on('hidden.bs.modal', function () {
                $(this).find('form').trigger('reset');
            });


            $('body').on('click', 'button.split-refund-btn', function () {
                let prodId = $(this).data('refund-prod-id');
                let input = 'input#split-refund-input-' + prodId;
                let maxQty = $(input).data('refund-prod-qty');
                let qty = $(input).val();
                $('input#split-refund-input2-' + prodId).val(qty);
                $(input).val(maxQty - qty);
            });
            $('body').on('change', 'input.split-refund-input', function () {
                let prodId = $(this).data('refund-prod-id');
                let maxQty = $(this).data('refund-prod-qty');
                let curVal = $(this).val();
                let final = maxQty - curVal;
                let input = 'input#split-refund-input2-' + prodId;
                $(input).val(final);
            });
            $('body').on('change', 'input.split-refund-input2', function () {
                let prodId = $(this).data('refund-prod-id');
                let input = 'input#split-refund-input-' + prodId;
                let maxQty = $(input).data('refund-prod-qty');
                let curVal = $(this).val();
                let final = maxQty - curVal;
                $(input).val(final);
            });
            $('#modal_order_refund_product').on('hidden.bs.modal', function () {
                $(this).find('form').trigger('reset');
            });
        });
    </script>

    {!! Form::modalAction('resend-order-confirmation-email-modal', trans('plugins/ecommerce::order.resend_order_confirmation'), 'info', trans('plugins/ecommerce::order.resend_order_confirmation_description', ['email' => $order->user->id ? $order->user->email : $order->address->email]), 'confirm-resend-confirmation-email-button', trans('plugins/ecommerce::order.send')) !!}
    {!! Form::modalAction('cancel-shipment-modal', trans('plugins/ecommerce::order.cancel_shipping_confirmation'), 'info', trans('plugins/ecommerce::order.cancel_shipping_confirmation_description'), 'confirm-cancel-shipment-button', trans('plugins/ecommerce::order.confirm')) !!}
    {!! Form::modalAction('update-shipping-address-modal', trans('plugins/ecommerce::order.update_address'), 'info', view('plugins/ecommerce::orders.shipping-address.form', ['address' => $order->address, 'orderId' => $order->id])->render(), 'confirm-update-shipping-address-button', trans('plugins/ecommerce::order.update'), 'modal-md') !!}
    {!! Form::modalAction('cancel-order-modal', trans('plugins/ecommerce::order.cancel_order_confirmation'), 'info', trans('plugins/ecommerce::order.cancel_order_confirmation_description'), 'confirm-cancel-order-button', trans('plugins/ecommerce::order.cancel_order')) !!}
    {!! Form::modalAction('confirm-payment-modal', trans('plugins/ecommerce::order.confirm_payment'), 'info', trans('plugins/ecommerce::order.confirm_payment_confirmation_description', ['method' => $order->payment->payment_channel->label()]), 'confirm-payment-order-button', trans('plugins/ecommerce::order.confirm_payment')) !!}
    {!! Form::modalAction('confirm-refund-modal', trans('plugins/ecommerce::order.refund'), 'info', view('plugins/ecommerce::orders.refund.modal', compact('order'))->render(), 'confirm-refund-payment-button', trans('plugins/ecommerce::order.refund') . ' <span class="refund-amount-text">' . format_price($order->payment->amount - $order->payment->refunded_amount, true) . '</span>') !!}

@stop
