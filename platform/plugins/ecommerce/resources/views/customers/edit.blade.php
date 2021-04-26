@extends('core/base::layouts.master')
@section('content')

    <form method="POST" action="{{route('customer.edit',[$customer->id])}}" accept-charset="UTF-8"
          novalidate="novalidate" _lpchecked="1">
        @csrf
        <div class="row">
            <div class="col-md-9">
                <div class="main-form">
                    <div class="form-body">
                        <div class="form-group">

                            <label for="name" class="control-label required" aria-required="true">Name</label>
                            <input class="form-control is-valid" placeholder="Name" name="name"
                                   type="text" value="{{$customer->name}}" id="name">
                            {!! Form::error('name', $errors) !!}

                        </div>


                        <div class="form-group">
                            <label for="email" class="control-label required" aria-required="true">Email</label>
                            <input class="form-control" placeholder="Ex: example@gmail.com" data-counter="60"
                                   name="email" type="text" value="{{$customer->email}}" id="email">
                            {!! Form::error('email', $errors) !!}
                        </div>

                        <div class="form-group">
                            <input class="hrv-checkbox" id="is_change_password" name="is_change_password"
                                   type="checkbox" value="1">
                            <label for="is_change_password" class="control-label">Change password?</label>

                        </div>

                        <div class="form-group hidden">

                            <label for="password" class="control-label required" aria-required="true">Password</label>
                            <input class="form-control" data-counter="60" name="password" type="password" id="password">


                        </div>

                        <div class="form-group hidden">
                            <label for="password_confirmation" class="control-label required" aria-required="true">Password
                                confirmation</label>
                            <input class="form-control" data-counter="60" name="password_confirmation" type="password"
                                   id="password_confirmation">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>


            </div>
            <div class="col-md-3 right-sidebar">
                <div class="widget meta-boxes form-actions form-actions-default action-horizontal">
                    <div class="widget-title">
                        <h4>
                            <span>Publish</span>
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="btn-set">
                            <button type="submit" name="submit" value="save" class="btn btn-info">
                                <i class="fa fa-save"></i> Save
                            </button>
                            &nbsp;
                            <button type="submit" name="submit" value="apply" class="btn btn-success">
                                <i class="fa fa-check-circle"></i> Save &amp; Edit
                            </button>
                        </div>
                    </div>
                </div>
                <div id="waypoint"></div>
                <div class="form-actions form-actions-fixed-top hidden">
                    <ol class="breadcrumb">

                        <li class="breadcrumb-item"><a href="http://landb.co/admin">Dashboard</a></li>


                        <li class="breadcrumb-item"><a href="http://landb.co/admin/ecommerce/products">Ecommerce</a>
                        </li>


                        <li class="breadcrumb-item"><a href="http://landb.co/admin/customers">Customers</a></li>


                        <li class="breadcrumb-item active">Edit customer "Raspberry Lemon Boutique"</li>

                    </ol>


                    <div class="btn-set">
                        <button type="submit" name="submit" value="save" class="btn btn-info">
                            <i class="fa fa-save"></i> Save
                        </button>
                        &nbsp;
                        <button type="submit" name="submit" value="apply" class="btn btn-success">
                            <i class="fa fa-check-circle"></i> Save &amp; Edit
                        </button>
                    </div>
                </div>


            </div>
        </div>

    </form>

    <div class="row">
        <div class="col-lg-6">
            <div class="p-3 bg-white">
                {!! Form::open(['route' => 'customers.create-customer-address', 'class' => 'ps-form--account-setting', 'method' => 'POST']) !!}

                <div class="row">
                    <div class="col-lg-6 mt-2">
                        <label for="name">{{ __('Full Name') }}:</label>
                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                        <input type="hidden" class="form-control" name="customer_id" value="{{$customer->id}}">
                    </div>
                    {!! Form::error('name', $errors) !!}

                    <div class="col-lg-6 mt-2">
                        <label for="email">{{ __('Email') }}:</label>
                        <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                    {!! Form::error('email', $errors) !!}

                    <div class="col-lg-6 mt-2">
                        <label for="phone">{{ __('Phone') }}:</label>
                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}">

                    </div>
                    {!! Form::error('phone', $errors) !!}

                    <div class="col-lg-6 mt-2 @if ($errors->has('country')) has-error @endif">
                        <label for="country">{{ __('Country') }}:</label>
                        <select name="country" class="form-control selectpicker select-country" data-live-search="true"
                                id="country">
                            @foreach(['' => __('Select country...')] + \Botble\Base\Supports\Helper::countries() as $countryCode => $countryName)
                                <option value="{{ $countryCode }}"
                                        @if (old('country') == $countryCode) selected @endif>{{ $countryName }}</option>
                            @endforeach
                        </select>
                    </div>
                    {!! Form::error('country', $errors) !!}

                    <div class="col-lg-6 mt-2 @if ($errors->has('state')) has-error @endif">
                        <label for="state">{{ __('State') }}:</label>
                        <input id="state" type="text" class="form-control" name="state" value="{{ old('state') }}">

                    </div>
                    {!! Form::error('state', $errors) !!}

                    <div class="col-lg-6 mt-2 @if ($errors->has('city')) has-error @endif">
                        <label for="city">{{ __('City') }}:</label>
                        <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}">

                    </div>
                    {!! Form::error('city', $errors) !!}

                    <div class="col-lg-12 mt-2">
                        <label for="address">{{ __('Address') }}:</label>
                        <input id="address" type="text" class="form-control" name="address"
                               value="{{ old('address') }}">
                    </div>
                    {!! Form::error('address', $errors) !!}

                    @if (EcommerceHelper::isZipCodeEnabled())
                        <div class="form-group">
                            <label>{{ __('Zip code') }}:</label>
                            <input id="zip_code" type="text" class="form-control" name="zip_code"
                                   value="{{ old('zip_code') }}">
                            {!! Form::error('zip_code', $errors) !!}
                        </div>
                    @endif

                    <div class="form-group col-lg-12">
                        <div class="ps-checkbox mt-3">
                            <input class="ml-2" type="checkbox" value="1" name="is_default" id="is-default">
                            <label class="mr-2" for="is-default"> {{ __('Use this address as default') }}</label>
                        <!-- <input class="form-control" type="checkbox" value="1" name="is_default" id="is-default">
                <label for="is-default">{{ __('Use this address as default') }}</label> -->
                        </div>
                        {!! Form::error('is_default', $errors) !!}
                    </div>

                    <div class="form-group col-lg-6">
                        <button class="btn btn-primary btn-lg" type="submit">{{ __('Add a new address') }}</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-lg-6">
            <div class="p-3 bg-white">


                <form onsubmit="return false;">
                    <!--      Make your own form or copy this one -->
                    <div class="row group">
                        @isset($customer->billingAddress)
                            <label class="col-lg-12">
                                <span>Billing Address</span>
                                {!!
                        Form::select('billing_address', $customer->billingAddress->pluck('address'),null ,['class' => 'form-control selectpicker','id'   => 'billing_address','data-live-search'=>'true', 'placeholder'=>'Select Address',
                        ])
                    !!}
                            </label>
                        @endisset
                    </div>
                    <div class="group row">
                        <label class="col-lg-12">

                            <div id="card-element" class="field">
                                <span>Card</span>
                                <div id="fattjs-number" style="height: 35px"></div>
                                <span class="mt-2">CVV</span>
                                <div id="fattjs-cvv" style="height: 35px"></div>
                            </div>
                        </label>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <input name="month" size="3" maxlength="2" placeholder="MM" class="form-control month">
                        </div>
                        <p class="mt-2"> / </p>
                        <div class="col-lg-3">
                            <input name="year" size="5" maxlength="4" placeholder="YYYY" class="form-control year">
                        </div>
                    </div>
                    {{--                    <button class="btn btn-info mt-3" id="paybutton">Pay $1</button>--}}
                    <button class="btn btn-success mt-3" id="tokenizebutton">Add Credit Card</button>
                    <div class="outcome">
                        <div class="error"></div>
                        <div class="success">
                            Successful! The ID is
                            <span class="token"></span>
                        </div>
                        <div class="loader" style="margin: auto">
                        </div>
                    </div>
                </form>
                <button class="btn btn-info mt-3 paynow">Pay $1</button>
                <form method="POST" action="{{route('thread.testingPayment')}}">
                    @csrf
                    <button class="btn btn-info mt-3 ">Pay $1</button>
                </form>
            </div>
        </div>
    </div>


    <div class="p-3 bg-white mt-3">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <h5>Address Details</h5>
            </div>
            <div class="col-lg-12 mb-3">
                <div class="table-responsive table-height">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zip Code</th>
                            <th>Country</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customer->addresses as $row)
                            <tr>
                                <td>{{$row->name}}</td>
                                <td>{{$row->email}}</td>
                                <td>{{$row->phone}}</td>
                                <td>{{$row->address}}</td>
                                <td>{{$row->city}}</td>
                                <td>{{$row->state}}</td>
                                <td>{{$row->zip_code}}</td>
                                <td>{{$row->country}}</td>
                                <td>{{($row->type == 'shipping') ? 'Shipping':'Billing'}}</td>
                                {{--                                <td><a data-toggle="modal" data-target="#edit_address"><i class="fa fa-edit"></i></a>--}}

                                {{--                                    &nbsp;<a><i class="fa fa-trash"></i></a></td>--}}
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <div class="p-3 bg-white mt-3">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <h5>Card Details</h5>
            </div>
            <div class="col-lg-12 mb-3">
                <div class="table-responsive table-height">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Card Number</th>
                            <th>Expires</th>
                            <th>CVV</th>
                            <th>Name on Card</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customer->addresses as $row)
                            <tr>
                                <td>{{$row->name}}</td>
                                <td>{{$row->email}}</td>
                                <td>{{$row->phone}}</td>
                                <td>{{$row->address}}</td>
                                <td>{{($row->type == 'shipping') ? 'Shipping':'Billing'}}</td>
                                {{--                                <td><a data-toggle="modal" data-target="#edit_address"><i class="fa fa-edit"></i></a>--}}

                                {{--                                    &nbsp;<a><i class="fa fa-trash"></i></a></td>--}}
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>



    <!-- Modal -->
    <div class="modal fade" id="edit_address" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex w-100">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                        <h4 class="modal-title text-center w-100 thread-pop-head">Edit Address <span
                                class="variation-name"></span></h4>
                        <div></div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                              class="control-label"
                                                                                                              aria-required="true">Full
                                        Name</label>
                                </label>
                                <input class="form-control is-valid" placeholder="Full Name" data-counter="120"
                                       name="name" type="text" value="asdas" id="name" aria-invalid="false"
                                       aria-describedby="name-error">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                              class="control-label"
                                                                                                              aria-required="true">Email</label>
                                </label>
                                <input class="form-control is-valid" placeholder="Email" data-counter="120" name="name"
                                       type="text" value="asdas" id="name" aria-invalid="false"
                                       aria-describedby="name-error">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                              class="control-label"
                                                                                                              aria-required="true">Phone</label>
                                </label>
                                <input class="form-control is-valid" placeholder="Phone" data-counter="120" name="name"
                                       type="text" value="asdas" id="name" aria-invalid="false"
                                       aria-describedby="name-error">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                              class="control-label"
                                                                                                              aria-required="true">Country</label>
                                </label>
                                <input class="form-control is-valid" placeholder="Country" data-counter="120"
                                       name="name" type="text" value="asdas" id="name" aria-invalid="false"
                                       aria-describedby="name-error">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                              class="control-label"
                                                                                                              aria-required="true">State</label>
                                </label>
                                <input class="form-control is-valid" placeholder="State" data-counter="120" name="name"
                                       type="text" value="asdas" id="name" aria-invalid="false"
                                       aria-describedby="name-error">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                              class="control-label"
                                                                                                              aria-required="true">City</label>
                                </label>
                                <input class="form-control is-valid" placeholder="City" data-counter="120" name="name"
                                       type="text" value="asdas" id="name" aria-invalid="false"
                                       aria-describedby="name-error">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                              class="control-label"
                                                                                                              aria-required="true">Address</label>
                                </label>
                                <input class="form-control is-valid" placeholder="Address" data-counter="120"
                                       name="name" type="text" value="asdas" id="name" aria-invalid="false"
                                       aria-describedby="name-error">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" name="submit" value="save" class="btn btn-info w-100 mb-4">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>


                    </div>
                </div>
            </div>


        </div>
    </div>

@stop

<style>
    .heading {
        color: #d64635;
        font-weight: 600;
    }

    .order-detail {
        font-size: 20px !important;
    }

    .img-circle {
        border-radius: 10px;
        width: 100%;
    }

    .table td {
        padding: 10px 5px !important;
        font-size: 14px;
    }
</style>

