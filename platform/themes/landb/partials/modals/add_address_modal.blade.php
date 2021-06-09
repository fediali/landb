
<div class="modal" id="add_address_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <div class="row p-3">
                    <div class="col-lg-12">
                        <form method="POST" action="{{ route('customer.edit-account-post', 'add-address') }}">
                            @csrf
                            <div class="row">
                                <div class="">
                                    <h4 class="profile-light-txt mt-2">Address information</h4>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <p class="textbox-label">Address Type</p>
                                            <input class="input-textbox form-control @error('type') is-invalid @enderror" type="radio" name="type" value="shipping" checked/> Shipping
                                            <input class="input-textbox form-control @error('type') is-invalid @enderror" type="radio" name="type" value="billing"/> Billing
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">First Name</p>
                                            <input required class="input-textbox form-control @error('first_name') is-invalid @enderror" type="text" name="first_name" value="{{ old('first_name') }}"/>
                                            @error('first_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Last Name</p>
                                            <input required class="input-textbox form-control @error('last_name') is-invalid @enderror" type="text"  name="last_name" value="{{ old('last_name') }}"/>
                                            @error('last_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Company Name</p>
                                            <input required class="input-textbox form-control @error('company') is-invalid @enderror" type="text"  name="company" value="{{ old('company') }}"/>
                                            @error('company')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Mobile</p>
                                            <input required class="input-textbox form-control @error('phone') is-invalid @enderror" type="text"  name="phone" value="{{ old('phone') }}"/>
                                            @error('phone')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-12">
                                            <p class="textbox-label">Address</p>
                                            <input required class="input-textbox form-control @error('address') is-invalid @enderror" type="text"  name="address" value="{{ old('address') }}"/>
                                            @error('address')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">City</p>
                                            <input required class="input-textbox form-control @error('city') is-invalid @enderror" type="text"  name="city" value="{{ old('city') }}"/>
                                            @error('city')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Country</p>
                                            {{--<select class="input-textbox">
                                                <option></option>
                                            </select>--}}
                                            {{--<input required class="input-textbox form-control @error('country') is-invalid @enderror" type="text"  name="country" value="{{ old('country',@$user->shippingAddress[0]->country) }}"/>--}}
                                            <select class="input-textbox form-control  @error('country') is-invalid @enderror" name="country">
                                                <option selected hidden disabled>Select a Country</option>
                                                @foreach(get_countries() as $key => $country)
                                                    <option @if(old('country') == $key) selected @endif value="{{ $key }}">{{ $country }}</option>
                                                @endforeach
                                            </select>
                                            @error('country')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">State/Province/Region</p>
                                            {{--<select class="input-textbox">
                                                <option></option>
                                            </select>--}}
                                            {{--<input required class="input-textbox form-control @error('state') is-invalid @enderror" type="text" name="state" value="{{ old('state',@$user->shippingAddress[0]->state) }}"/>--}}
                                            <select class="input-textbox form-control  @error('state') is-invalid @enderror" name="state">
                                                <option selected hidden disabled>Select a State</option>
                                                @foreach(get_states() as $key => $state)
                                                    <option @if(old('state') == $key) selected @endif value="{{ $key }}">{{ $state }}</option>
                                                @endforeach
                                            </select>
                                            @error('state')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Zip/Postal Code</p>
                                            <input required class="input-textbox form-control @error('zip_code') is-invalid @enderror" type="text"  name="zip_code" value="{{ old('zip_code') }}"/>
                                            @error('zip_code')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-12">
                                            <input class="ml-2" type="checkbox" name="set_default" value="1">
                                            <label class="ml-2 label-setdefault" for="vehicle1"> Set as Default</label>
                                        </div>
                                        {{--<div class="col-lg-12">
                                            <div class="shipping-checkbox-area mt-2 d-flex">
                                                <p>Billing and shipping addresses are the same</p>
                                                <div class="ml-3">
                                                    <input required class="ml-2" type="radio" name="billing" value="1" data-toggle="collapse" data-target=".collapseOne.show">
                                                    <label class="mr-2 mb-0" for="vehicle1"> Yes</label>
                                                    <input required class="ml-2" type="radio" name="billing" value="0" data-toggle="collapse" data-target=".collapseOne:not(.show)" checked>
                                                    <label class="mr-2 mb-0" for="vehicle1"> No</label>
                                                </div>

                                            </div>
                                        </div>--}}
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-5">
                                    <input required type="submit" class="btn cart-btn w-100" value="Save">
                                    {{--<a href="#" class=" btn cart-btn w-100">Register</a>--}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>