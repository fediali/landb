@extends('core/base::layouts.master')
@section('content')
<div class="p-3 bg-white" >
    {!! Form::open(['route' => 'customers.create-customer-address', 'class' => 'ps-form--account-setting', 'method' => 'POST']) !!}
    <div class="ps-form__header">
        <h3>{{ SeoHelper::getTitle() }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-6 mt-2">
            <label for="name">{{ __('Full Name') }}:</label>
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
            <input type="hidden" class="form-control" name="customer_id" value="{{$user}}">
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
            <select name="country" class="form-control" id="country">
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
            <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}">
        </div>
        {!! Form::error('address', $errors) !!}

        @if (EcommerceHelper::isZipCodeEnabled())
            <div class="form-group">
                <label>{{ __('Zip code') }}:</label>
                <input id="zip_code" type="text" class="form-control" name="zip_code" value="{{ old('zip_code') }}">
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

        <div class="form-group col-lg-3">
            <button class="btn btn-primary btn-lg" type="submit">{{ __('Add a new address') }}</button>
        </div>
    </div>
    {!! Form::close() !!}
    </div>

    <div class="p-3 bg-white mt-3" >
    <div class="row">
                <div class="col-lg-12 mb-3">
                <div class="table-responsive">
                <table class="table table-striped">
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Country</th>
        <th>State</th>
        <th>City</th>
        <th>Address</th>
        <th>Action</th>

      </tr>
    </thead>
    <tbody>
      <tr>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td>+1647881255</td>
        <td>Canada</td>
        <td>Ontario</td>
        <td>Toronto</td>
        <td>Abcd Street</td>
        <td><a><i class="fa fa-edit"></i></a> &nbsp; &nbsp;<a><i class="fa fa-trash"></i></a></td> 
      </tr>
      <tr>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td>+1647881255</td>
        <td>Canada</td>
        <td>Ontario</td>
        <td>Toronto</td>
        <td>Abcd Street</td>
        <td><a><i class="fa fa-edit"></i></a> &nbsp; &nbsp;<a><i class="fa fa-trash"></i></a></td> 
      </tr>
      <tr>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td>+1647881255</td>
        <td>Canada</td>
        <td>Ontario</td>
        <td>Toronto</td>
        <td>Abcd Street</td>
        <td><a><i class="fa fa-edit"></i></a> &nbsp; &nbsp;<a><i class="fa fa-trash"></i></a></td> 
      </tr>
    </tbody>
  </table></div>
                
                </div>
            </div> 
           
    </div>

    <div class="p-3 bg-white mt-3" >
    <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5 class="order-detail">PROFILE DETAIL </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">
                <img class="img-circle" src="http://laravel.landbw.co/api/resize/users/user.png?w=100&amp;h=100" alt="User Avatar">
                </div>
                <div class="col-lg-10">
                <div class="row">
                <div class="col-lg-3">
                    <p class="m-0 heading">Full Name</p>
                    <p>Vendor</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading">Email</p>
                    <p>Order No.</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading"> Phone</p>
                    <p>Regular Pack Category</p>
                </div>
                    <div class="col-lg-3">
                        <p class="m-0 heading"> Country</p>
                        <p>Plus Pack Category</p>
                    </div> 
                <div class="col-lg-3">
                    <p class="m-0 heading">State</p>
                    <p>Description</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading"> City</p>
                    <p>PP Sample</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading">Address</p> 
                        <p>PP Sample Date</p>  
                </div>    
            </div></div>
            </div>
           
    </div>

@stop

<style>
    .heading{
        color: #d64635;
        font-weight: 600;
    } 
    .order-detail {
        font-size:20px !important;
    }
    .img-circle {
    border-radius: 10px;
    width: 100%;
}
.table td {
    padding: 10px 5px!important;
    font-size: 14px;
}
</style>
