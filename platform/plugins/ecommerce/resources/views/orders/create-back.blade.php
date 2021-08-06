@extends('core/base::layouts.master')

@section('content')

    <div class="max-width-1200" id="main-order">
        <create-order
                :currency="'{{ get_application_currency()->symbol }}'"
                :zip_code_enabled="{{ (int)EcommerceHelper::isZipCodeEnabled() }}"
                :order_types="{{ json_encode(\Botble\Ecommerce\Models\Order::$ORDER_TYPES) }}"
                :payment_methods="{{ json_encode(get_payment_methods()) }}">
        </create-order>
    </div>

    <div id="main-order" class="card-area max-width-1200" style="display: none">
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
                                    {!!Form::select('order_card', ['No Credit Card Found'], null, ['class' => 'form-control card_list','id'=> 'card_id',])!!}
                                </label>
                            </div>
                            <div class="row group m-0 pt-4 ">
                                <label class="col-lg-12 ">
                                    <span class="mb-2">Billing Address</span>
                                    {!! Form::select('billing_address',[],null ,['class' => 'form-control','id'   => 'billing_address','data-live-search'=>'true', 'placeholder'=>'Select Address', ]) !!}
                                </label>
                            </div>
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
                        <span class="card_customer_select">Please Select Customer First</span>
                    </div>
                    <!--<div class="pd-all-20 bg-white">
                        <form action="#" method="POST">
                            <input type="hidden" value="" name="order_id" class="order_id">
                            <input type="hidden" value="" name="amount">
                            <button type="submit" class="btn btn-info">Create Payment</button>
                        </form>
                    </div>-->
                </div>
            </div>
        </div>
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
        }
    </script>
@endpush
