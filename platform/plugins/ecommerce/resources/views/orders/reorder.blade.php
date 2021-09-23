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
                :paypal_email_prop="{{$order->payment ? json_encode($order->payment->paypal_email) : ''}}"
                :order_types="{{ json_encode(\Botble\Ecommerce\Models\Order::$ORDER_TYPES) }}"
                :payment_methods="{{ json_encode(get_payment_methods()) }}"
                :order_type="'{{ $order->order_type }}'"
                :payment_method="'{{$order->payment ? $order->payment->payment_channel : 'cod'}}'"
                :customer_addresses="{{ json_encode($customerAddresses) }}"
                :customer_billing_addresses="{{ !empty($customerBillingAddresses) ? json_encode($customerBillingAddresses) : [] }}"
                :customer_address="{{ $customerAddress }}"
                @if ($customerBillingAddress)
                :customer_billing_address="{{ $customerBillingAddress }}"
                @endif
                :sub_amount="{{ $order->sub_total }}"
                :total_amount="{{ /*$order->payment->amount ??*/ $order->amount }}"
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


    <div id="main-order" class="card-area max-width-1200" style="{{in_array($order->payment->payment_channel->label(), ['omni-payment', 'omni_payment']) ? 'block' : 'none'}}">
        <div class="flexbox-grid no-pd-none">
            <div class="flexbox-content"></div>
            <div class="flexbox-content flexbox-right">
                <div class="wrapper-content bg-gray-white mb20">
                    <button class="btn btn-info btn-card credit_card">
                        Credit Card
                    </button>
                    <div class="bg-white">
                        <div class="card_fields">
                            <div class="row group m-0 pt-4 ">
                                <label class="col-lg-12 ">
                                    <span class="mb-2">Credit Card</span>
                                    {!!Form::select('order_card', $cards, @$order->order_card, ['class' => 'form-control card_list','id'=> 'card_id',])!!}
                                </label>
                            </div>
                            {{--<div class="row group m-0 pt-4 ">
                                <label class="col-lg-12 ">
                                    <span class="mb-2">Billing Address</span>
                                    {!! Form::select('billing_address',$order->user->billingAddress->pluck('address', 'id'), @$order->billingAddress->customer_address_id ,['class' => 'form-control','id'   => 'billing_address','data-live-search'=>'true', 'placeholder'=>'Select Address', ]) !!}
                                </label>
                            </div>--}}
                            <div class="add_card">
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
                                        <input name="month" size="3" maxlength="2" placeholder="MM" class="form-control month">
                                    </div>
                                    <p class="mt-2"> / </p>
                                    <div class="col-lg-3">
                                        <input name="year" size="5" maxlength="4" placeholder="YYYY" class="form-control year">
                                    </div>
                                </div>
                                <div class="row m-0">
                                    <div class="col-lg-6">
                                        <button class="btn btn-success mt-3" id="tokenizebutton">Add Credit Card
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
                        </div>
                        <span class="card_customer_select" style="display:none;">Please Select Customer First</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!in_array($order->payment->payment_channel->label(), ['omni-payment', 'omni_payment']))
        @if($order->payment->payment_channel->label() == 'paypal')
            <div class="wrapper-content bg-gray-white mb20">
                <div class="row m-0 pt-4 bg-white">
                    <div class="col-lg-12 ">
                        <strong class="mb-2">Paid with Paypal</strong>
                    </div>
                </div>
            </div>
        @else
            <div class="wrapper-content bg-gray-white mb20">
                <div class="row m-0 pt-2 pb-2 bg-white">
                    <div class="col-lg-12 ">
                        <strong class="mb-2">{{$order->payment ? $order->payment->payment_channel : 'Cash on delivery'}}</strong>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <script>
        setTimeout(function () {
            getCustomer();
            getbillingadress();
        }, 200);
    </script>
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
