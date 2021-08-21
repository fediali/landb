@extends('core/base::layouts.master')
@section('content')

    <form method="POST" action="{{route('customer.create')}}" accept-charset="UTF-8"
          novalidate="novalidate" _lpchecked="1">
        @csrf
        <div class="row">
            <div class="col-md-9">
                <div class="main-form">
                    <div class="form-body">
                        <div class="form-group">

                            <label for="name" class="control-label required" aria-required="true">Name</label>
                            <input class="form-control is-valid" placeholder="Name" name="name"
                                   type="text" value="{{@$customer->name}}" id="name">
                            {!! Form::error('name', $errors) !!}

                        </div>


                        <div class="form-group">
                            <label for="email" class="control-label required" aria-required="true">Email</label>
                            <input class="form-control" placeholder="Ex: example@gmail.com" data-counter="60"
                                   name="email" type="text" value="{{@$customer->email}}" id="email">
                            {!! Form::error('email', $errors) !!}
                        </div>



                        <div class="form-group ">

                            <label for="password" class="control-label required" aria-required="true">Password</label>
                            <input class="form-control" data-counter="60" name="password" type="password" id="password">


                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="control-label required" aria-required="true">Password
                                confirmation</label>
                            <input class="form-control" data-counter="60" name="password_confirmation" type="password"
                                   id="password_confirmation">
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                            <input class="hrv-checkbox" id="is_private" name="is_private" type="checkbox"
                                   value="1" {{@$customer->is_private ? 'checked' : ''}}>
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


                <div class="p-2 bg-white">
                    Status:
                    {!! Form::select('status', \Botble\Base\Enums\BaseStatusEnum::$CUSTOMERS, null , ['class' => 'w-100','placeholder'=>'Select Status']) !!}
                    <hr>
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
                        <div class="col-lg-6">
                            <p class="textbox-label">Company</p>
                            <input class="input-textbox form-control @error('company') is-invalid @enderror" type="text"
                                   name="company" value="{{ old('company', @$customer->details->company) }}"/>
                            @error('company')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-12">
                            <p class="textbox-label">Customer Type </p>
                            @foreach(\Botble\Ecommerce\Models\Customer::$customerType as $type)
                                <input class="ml-2" type="checkbox" name="customer_type[]" value="{{ $type }}"
                                       @if(isset($customer->details) && !empty($customer->details->customer_type)) @if(in_array($type, json_decode(isset($customer->details) ? $customer->details->customer_type : '[]')) || old('customer_type') == $type) checked @endif @endif>
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
@stop



