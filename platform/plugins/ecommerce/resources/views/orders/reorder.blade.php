@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200" id="main-order">
        <create-order
                :products="{{ json_encode($products->toArray()) }}"
                :product_ids="{{ json_encode($productIds) }}"
                @if ($customer)
                    :customer="{{ $customer }}"
                @endif
                :order_id="{{ $order->id }}"
                :customer_id="{{ $order->user_id }}"
                :order_types="{{ json_encode(\Botble\Ecommerce\Models\Order::$ORDER_TYPES) }}"
                :payment_methods="{{ json_encode(get_payment_methods()) }}"
                :order_type="'{{ $order->order_type }}'"
                :payment_method="'{{$order->payment ? $order->payment->payment_channel : 'cod'}}'"
                :customer_addresses="{{ json_encode($customerAddresses) }}"
                :customer_address="{{ $customerAddress }}"
                :sub_amount="{{ $order->amount }}"
                :total_amount="{{ $order->payment->amount ?? $order->amount }}"
                :discount_amount="{{ $order->discount_amount }}"
                @if ($order->coupon_code) :discount_coupon_code="'{{ $order->coupon_code }}'" @endif
                @if ($order->discount_description) :discount_description="'{{ $order->discount_description }}'" @endif
                :shipping_amount="{{ $order->shipping_amount }}"
                @if ($order->shipping_method != \Botble\Ecommerce\Enums\ShippingMethodEnum::DEFAULT)
                    :shipping_method="'{{ $order->shipping_method }}'"
                @endif
                @if ($order->shipping_option) :shipping_option="'{{ $order->shipping_option }}'" @endif
                @if ($order->shipping_method != \Botble\Ecommerce\Enums\ShippingMethodEnum::DEFAULT && false)
                    :shipping_method_name="'{{ OrderHelper::getShippingMethod($order->shipping_method, $order->shipping_option) }}'"
                @endif
                :is_selected_shipping="true"
                :customer_order_numbers="{{ $customerOrderNumbers }}"
                :currency="'{{ get_application_currency()->symbol }}'"
                :zip_code_enabled="{{ (int)EcommerceHelper::isZipCodeEnabled() }}">
        </create-order>
    </div>
@stop

@push('header')
    <script>
        "use strict";

        window.trans = {
            "Order": "{{ trans('plugins/ecommerce::order.order') }}",
            "Order information": "{{ trans('plugins/ecommerce::order.order_information') }}",
            "Create a new product": "{{ trans('plugins/ecommerce::order.create_new_product')  }}",
            "Out of stock": "{{ trans('plugins/ecommerce::order.out_of_stock') }}",
            "product(s) available": "{{ trans('plugins/ecommerce::order.products_available') }}",
            "No products found!": "{{ trans('plugins/ecommerce::order.no_products_found') }}",
        };

    </script>
@endpush

@push('echo-server')
    <script>
        window.Echo.channel('order-edit-{{$order->id}}').listen('.orderEdit', (data) => {
            if (data.user_id != "{{auth()->user()->id}}") {
                var reply = confirm(data.user_name + " is trying to access this order edit! \n You want to give him access ? \n Press Ok to grant Or Cancel to Ignore request.");
                if (reply) {
                    data.access = 1;
                } else {
                    data.access = 0;
                }
                window.Echo.private('order-edit-access-'+data.user_id).whisper('.orderEditAccess', data);
                // window.Echo.private('order-edit-access-'+data.user_id).whisper('.orderEditAccess', data);
            }
        });
    </script>
@endpush
