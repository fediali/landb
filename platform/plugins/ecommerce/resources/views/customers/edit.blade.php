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

                        <div class="form-group">
                            <input class="hrv-checkbox" id="is_private" name="is_private" type="checkbox"
                                   value="{{$customer->is_private}}" {{$customer->is_private ? 'checked' : ''}}>
                            <label for="is_private" class="control-label">Is Private?</label>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                            <select class="input-textbox form-control" name="salesperson_id">
                                <option value="" selected disabled>Select Salesperson</option>
                                @foreach(get_salesperson() as $id => $name)
                                    <option value="{{ $id }}"
                                            @if(@$customer->salesperson_id == $id || old('hear_us') == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
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
                <div class="p-2 bg-white">
                    @php
                        $options = [
                                    ['value' => 'verified', 'text' => 'Verified'],
                                    ['value' => 'pending', 'text' => 'Pending'],
                                    ['value' => 'draft', 'text' => 'Draft']
                                   ];
                    @endphp
                    Status:
                    <select name="status">
                        @foreach($options as $key => $option)
                            <option
                                value="{{ $option['value'] }}" {!! ($customer->status == $option['value']) ? 'selected' : '' !!}>{{ $option['text'] }}</option>
                        @endforeach
                    </select>

                    <a class="btn btn-lg btn-primary" href="{{route('orders.index', ['user_id' => $customer->id])}}">View
                        All Orders</a>
                </div>

            </div>
        </div>
        <div class="p-3 bg-white mt-3">
            <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5>Customer account information</h5>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="row">
                        <div class="col-lg-6">
                            <p class="textbox-label">First Name</p>
                            <input class="input-textbox form-control @error('first_name') is-invalid @enderror"
                                   type="text" name="first_name"
                                   value="{{ old('first_name',@$customer->details->first_name) }}"/>
                            @error('first_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Last Name</p>
                            <input class="input-textbox form-control @error('last_name') is-invalid @enderror"
                                   type="text" name="last_name"
                                   value="{{ old('last_name',@$customer->details->last_name) }}"/>
                            @error('last_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Business Phone</p>
                            <input class="input-textbox form-control @error('business_phone') is-invalid @enderror"
                                   type="text" name="business_phone"
                                   value="{{ old('business_phone', @$customer->details->business_phone )  }}"/>
                            @error('business_phone')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-12">
                            <p class="textbox-label">Company</p>
                            <input class="input-textbox form-control @error('company') is-invalid @enderror" type="text"
                                   name="company" value="{{ old('company', @$customer->details->company) }}"/>
                            @error('company')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-12">
                            <p class="textbox-label">Customer Type</p>
                                @foreach(\Botble\Ecommerce\Models\Customer::$customerType as $type)
                                    <input class="ml-2" type="checkbox" name="customer_type[]" value="{{ $type }}"
                                          @if(isset($customer->details)) @if(in_array($type, json_decode(isset($customer->details) ? $customer->details->customer_type : '[]')) || old('customer_type') == $type) checked @endif @endif>
                                    <label class="mr-2" for="vehicle1"> {{ $type }}</label>
                                @endforeach
                            @error('customer_type')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Store’s Facebook</p>
                            <input class="input-textbox form-control" type="text" name="store_facebook"
                                   value="{{ old('store_facebook', @$customer->details->store_facebook) }}"/>
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Store’s Instagram</p>
                            <input class="input-textbox form-control" type="text" name="store_instagram"
                                   value="{{ old('store_instagram', @$customer->details->store_instagram) }}"/>
                        </div>
                        <div class="col-lg-12">
                            <p class="textbox-label">Store’s Brick & Mortar address</p>
                            <input class="input-textbox form-control" type="text" name="mortar_address"
                                   value="{{ old('mortar_address', @$customer->details->mortar_address) }}"/>
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Where did they find us from?</p>
                            <select class="input-textbox form-control" name="hear_us">
                                <option @if(is_null(@$customer->details->hear_us)) selected @endif disabled hidden>
                                    Select an Option
                                </option>
                                @foreach(\Botble\Ecommerce\Models\Customer::$hearUs as $key => $hearUs)
                                    <option value="{{ $key }}"
                                            @if(@$customer->details->hear_us == $key || old('hear_us') == $key) selected @endif>{{ $hearUs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Preffered Way of Communication</p>
                            <select class="input-textbox form-control" name="preferred_communication">
                                <option @if(is_null(@$customer->details->preferred_communication)) selected
                                        @endif disabled hidden>Select an Option
                                </option>
                                @foreach(\Botble\Ecommerce\Models\Customer::$preferredCommunication as $key => $preferred)
                                    <option value="{{ $key }}"
                                            @if(@$customer->details->preferred_communication == $key || old('preferred_communication') == $key) selected @endif>{{ $preferred }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Sales Tax ID</p>
                            <input class="input-textbox form-control @error('sales_tax_id') is-invalid @enderror"
                                   type="text" name="sales_tax_id"
                                   value="{{ old('sales_tax_id', @$customer->details->sales_tax_id) }}"/>
                            @error('sales_tax_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <p class="textbox-label">Which shows/events do you attend?</p>
                            <input class="input-textbox form-control" type="text" name="events_attended"
                                   value="{{ old('events_attended' , @$customer->details->events_attended )}}"/>
                        </div>
                        <div class="col-lg-12">
                            <p class="textbox-label">Comments</p>
                            <textarea rows="4" name="comments"
                                      class="input-textbox form-control">{{ old('comments',@$customer->details->comments) }}</textarea>
                        </div>

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
                        Form::select('billing_address', $customer->billingAddress->pluck('address', 'id'),null ,['class' => 'form-control selectpicker','id'   => 'billing_address','data-live-search'=>'true', 'placeholder'=>'Select Address',
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
                {{--                <form method="POST" action="{{route('thread.testingPayment')}}">--}}
                {{--                    @csrf--}}
                {{--                    <button class="btn btn-info mt-3 ">Pay $1</button>--}}
                {{--                </form>--}}
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
                                <td>
                                    <a data-row="{{ $row }}" class="toggle-edit-address"><i class="fa fa-edit"></i></a>
                                    &nbsp;
                                    <a class="delete_address" data-id="{{ $row->id }}"><i class="fa fa-trash"></i></a>
                                </td>
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
                            <th>Name on Card</th>
                            <th>Expires</th>
                            <th>Last 4 Digit</th>
                        </tr>
                        </thead>
                        <tbody>

                        @if(isset($card))
                            @foreach($card as $cards)
                                <tr>
                                    <td>{{$cards->person_name}}</td>
                                    <td>{{$cards->card_exp}}</td>
                                    <td>{{$cards->card_last_four}}</td>
                                    {{--                                <td><a data-toggle="modal" data-target="#edit_address"><i class="fa fa-edit"></i></a>--}}

                                    {{--                                    &nbsp;<a><i class="fa fa-trash"></i></a></td>--}}
                                </tr>
                            @endforeach
                        @endif
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
                <form id="address_form">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="address_id" value="">
                            <div class="col-lg-6">
                                <p class="textbox-label">First Name</p>
                                <input
                                    class="input-textbox form-control @error('address_first_name') is-invalid @enderror"
                                    type="text" name="address_first_name" value=""/>

                            </div>
                            <div class="col-lg-6">
                                <p class="textbox-label">Last Name</p>
                                <input
                                    class="input-textbox form-control @error('address_last_name') is-invalid @enderror"
                                    type="text" name="address_last_name" value=""/>
                                @error('address_last_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <p class="textbox-label">Company Name</p>
                                <input class="input-textbox form-control @error('address_company') is-invalid @enderror"
                                       type="text" name="address_company"
                                       value="{{ old('address_company',@$customer->addresses[0]->company) }}"/>
                                @error('address_company')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <p class="textbox-label">Mobile</p>
                                <input class="input-textbox form-control @error('address_phone') is-invalid @enderror"
                                       type="text" name="address_phone"
                                       value="{{ old('address_phone',@$customer->addresses[0]->phone) }}"/>
                                @error('address_phone')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <p class="textbox-label">Address</p>
                                <input class="input-textbox form-control @error('address_address') is-invalid @enderror"
                                       type="text" name="address_address"
                                       value="{{ old('address_address',@$customer->addresses[0]->address) }}"/>
                                @error('address_address')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <p class="textbox-label">City</p>
                                <input class="input-textbox form-control @error('address_city') is-invalid @enderror"
                                       type="text" name="address_city"
                                       value="{{ old('address_city',@$customer->addresses[0]->city) }}"/>
                                @error('address_city')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <p class="textbox-label">Country</p>
                                {{--<select class="input-textbox">
                                    <option></option>
                                </select>--}}
                                {{--<input class="input-textbox form-control @error('address_country') is-invalid @enderror" type="text"  name="address_country" value="{{ old('address_country',@$customer->addresses[0]->country) }}"/>--}}
                                <select
                                    class="input-textbox address-country form-control  @error('address_country') is-invalid @enderror"
                                    name="address_country">
                                    <option selected hidden disabled>Select a Country</option>
                                </select>
                                @error('address_country')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <p class="textbox-label">State/Province/Region</p>
                                {{--<select class="input-textbox">
                                    <option></option>
                                </select>--}}
                                {{--<input class="input-textbox form-control @error('address_state') is-invalid @enderror" type="text"  name="address_state" value="{{ old('address_state',@$customer->addresses[0]->state) }}"/>--}}
                                <select class="input-textbox form-control  @error('address_state') is-invalid @enderror"
                                        name="address_state">
                                    <option selected hidden disabled>Select a State</option>
                                </select>
                                @error('address_state')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <p class="textbox-label">Zip/Postal Code</p>
                                <input
                                    class="input-textbox form-control @error('address_zip_code') is-invalid @enderror"
                                    type="text" name="address_zip_code"
                                    value="{{ old('address_zip_code',@$customer->addresses[0]->zip_code) }}"/>
                                @error('address_zip_code')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-info" type="submit" value="Update">
                    </div>
                </form>
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


