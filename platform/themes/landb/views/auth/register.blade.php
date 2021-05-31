<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>Sign Up</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
            <form id="register-form" method="post" action="{{ route('public.register.post') }}">
            @csrf
            <h2 class="mt-5 mb-4 text-center signin-head">SIGN UP</h2>

            <h4 class="profile-light-txt mt-5">User account information</h4>
            <div class="row">
                <div class="col-lg-12">
                    <p class="textbox-label">Email</p>
                    <input class="input-textbox form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" type="email" name="email" required/>
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    <p class="textbox-label">Password</p>
                    <input class="input-textbox form-control @error('password') is-invalid @enderror" type="password" name="password" required/>
                        @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    <p class="textbox-label">Confirm Password</p>
                    <input class="input-textbox form-control" type="password" name="password_confirmation" required/>
                </div>
            </div>

            <h4 class="profile-light-txt mt-4">Contact information</h4>
            <div class="row">
                <div class="col-lg-6">
                    <p class="textbox-label">First Name</p>
                    <input class="input-textbox form-control @error('first_name') is-invalid @enderror"  value="{{ old('first_name') }}" type="text" name="first_name" required/>
                    @error('first_name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Last Name</p>
                    <input class="input-textbox form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" type="text" name="last_name" required/>
                    @error('last_name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Business Phone</p>
                    <input class="input-textbox form-control @error('business_phone') is-invalid @enderror" value="{{ old('business_phone') }}" type="text" name="business_phone" required/>
                    @error('business_phone')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Mobile</p>
                    <input class="input-textbox form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" type="text" name="mobile" required/>
                    @error('mobile')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Company</p>
                    <input class="input-textbox form-control @error('company') is-invalid @enderror" value="{{ old('company') }}" type="text" name="company" required/>
                    @error('company')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Customer Type</p>
                    @foreach(\Botble\Ecommerce\Models\Customer::$customerType as $type)
                        <input class="ml-2" type="checkbox" name="customer_type[]" value="{{ $type }}">
                        <label class="mr-2" for="vehicle1"> {{ $type }}</label>
                    @endforeach
                    @error('customer_type')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Store’s Facebook</p>
                    <input class="input-textbox form-control" type="text"  value="{{ old('store_facebook') }}" name="store_facebook"  />

                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Store’s Instagram</p>
                    <input class="input-textbox form-control" type="text" value="{{ old('store_instagram') }}" name="store_instagram" />
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Store’s Brick & Mortar address</p>
                    <input class="input-textbox form-control" type="text" value="{{ old('mortar_address') }}" name="mortar_address"/>
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Newsletter</p>
                        <input class="" type="checkbox" name="newsletter" value="1">
                        <label class="mr-2" for="Subscibe"> Want to Subscibe?</label>
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Where did they find us from?</p>
                    <select class="input-textbox form-control" name="hear_us">
                        <option @if(is_null(old('hear_us'))) selected @endif disabled hidden>Select an Option</option>
                        @foreach(\Botble\Ecommerce\Models\Customer::$hearUs as $key => $hearUs)
                            <option value="{{ $key }}" @if(old('hear_us') == $key) selected @endif>{{ $hearUs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Preffered Way of Communication</p>
                    <select class="input-textbox form-control" name="preferred_communication">
                        <option @if(is_null(old('preferred_communication'))) selected @endif disabled hidden>Select an Option</option>
                        @foreach(\Botble\Ecommerce\Models\Customer::$preferredCommunication as $key => $preferred)
                            <option value="{{ $key }}" @if(old('preferred_communication') == $key) selected @endif>{{ $preferred }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Sales Tax ID</p>
                    <input class="input-textbox form-control @error('sales_tax_id') is-invalid @enderror" type="text" value="{{ old('sales_tax_id') }}" name="sales_tax_id" required/>
                    @error('sales_tax_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Which shows/events do you attend?</p>
                    <input class="input-textbox form-control" type="text"  value="{{ old('events_attended') }}" name="events_attended" />
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Comments</p>
                    <textarea rows="4" class="input-textbox form-control" name="comments">{{ old('comments') }}</textarea>
                </div>

            </div>
            <h4 class="profile-light-txt mt-5">Shipping address</h4>
            <div class="row">
                <div class="col-lg-6">
                    <p class="textbox-label">First Name</p>
                    <input class="input-textbox form-control @error('shipping_first_name') is-invalid @enderror" type="text"  value="{{ old('shipping_first_name') }}" name="shipping_first_name" required/>
                    @error('shipping_first_name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Last Name</p>
                    <input class="input-textbox form-control @error('shipping_last_name') is-invalid @enderror" value="{{ old('shipping_last_name') }}" type="text" name="shipping_last_name" required/>
                    @error('shipping_last_name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Company Name</p>
                    <input class="input-textbox form-control @error('shipping_company') is-invalid @enderror" value="{{ old('shipping_company') }}" type="text" name="shipping_company" required/>
                    @error('shipping_company')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Mobile</p>
                    <input class="input-textbox form-control @error('shipping_mobile') is-invalid @enderror" type="text" value="{{ old('shipping_mobile') }}" name="shipping_mobile" required/>
                    @error('shipping_mobile')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Address</p>
                    <input class="input-textbox form-control @error('shipping_address') is-invalid @enderror" value="{{ old('shipping_address') }}" type="text" name="shipping_address" required/>
                    @error('shipping_address')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">City</p>
                    <input class="input-textbox form-control @error('shipping_city') is-invalid @enderror" type="text"  value="{{ old('shipping_city') }}" name="shipping_city" required/>
                    @error('shipping_city')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Country</p>
                    <select class="input-textbox form-control  @error('shipping_country') is-invalid @enderror" name="shipping_country" required>
                        <option selected hidden disabled>Select a Country</option>
                        @foreach(get_countries() as $key => $country)
                            <option value="{{ $key }}">{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('shipping_country')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">State/Province/Region</p>
                    <select class="input-textbox state-select form-control @error('shipping_state') is-invalid @enderror" name="shipping_state" required>
                        <option selected hidden disabled>Select a State</option>
                    </select>@error('shipping_state')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                </div>
                <div class="col-lg-6">
                    <p class="textbox-label">Zip/Postal Code</p>
                    <input class="input-textbox form-control @error('shipping_postal_code') is-invalid @enderror" type="text"  value="{{ old('shipping_postal_code') }}" name="shipping_postal_code" required/>
                    @error('shipping_postal_code')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-12">
                    <div class="shipping-checkbox-area mt-2 d-flex">
                        <p class="text-billing">Billing and shipping addresses are the same</p>
                        <div class="ml-3">
                            <input class="ml-2" type="radio" name="billing" value="1" data-toggle="collapse" data-target=".collapseOne.show" checked>
                            <label class="mr-2 mb-0" for="vehicle1"> Yes</label>
                            <input class="ml-2" type="radio" name="billing" value="0" data-toggle="collapse" data-target=".collapseOne:not(.show)">
                            <label class="mr-2 mb-0" for="vehicle1"> No</label>
                        </div>

                    </div>
                </div>
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="collapseOne panel-collapse collapse">
                            <div class="panel-body">
                                <h4 class="profile-light-txt mt-5">Billing address</h4>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <p class="textbox-label">First Name</p>
                                        <input class="input-textbox form-control" type="text"  value="{{ old('billing_first_name') }}" name="billing_first_name" />
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="textbox-label">Last Name</p>
                                        <input class="input-textbox form-control" type="text" value="{{ old('billing_last_name') }}" name="billing_last_name" />
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="textbox-label">Company Name</p>
                                        <input class="input-textbox form-control" type="text" value="{{ old('billing_company') }}" name="billing_company"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="textbox-label">Mobile</p>
                                        <input class="input-textbox form-control" type="text" value="{{ old('billing_mobile') }}" name="billing_mobile"/>
                                    </div>
                                    <div class="col-lg-12">
                                        <p class="textbox-label">Address</p>
                                        <input class="input-textbox form-control" type="text" value="{{ old('billing_address') }}" name="billing_address" />
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="textbox-label">City</p>
                                        <input class="input-textbox form-control" type="text" value="{{ old('billing_city') }}" name="billing_city" />
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="textbox-label">Country</p>
                                        <select class="input-textbox form-control" name="billing_country">
                                            <option selected hidden disabled>Select a Country</option>
                                            @foreach(get_countries() as $key => $country)
                                                <option value="{{ $key }}">{{ $country }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="textbox-label">State/Province/Region</p>
                                        <select class="input-textbox form-control" name="billing_state">
                                            <option selected hidden disabled>Select a State</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="textbox-label">Zip/Postal Code</p>
                                        <input class="input-textbox form-control" type="text" value="{{ old('billing_postal_code') }}" name="billing_postal_code" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-5 mb-5">
                    <input type="submit" form="register-form" class="btn cart-btn w-100" value="Sign Up"/>
                </div>
            </div>
            </form>
        </div>
        <div class="col-lg-3"></div>
    </div>
</section>