<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>Edit Profile</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <ul class="nav nav-tabs tabs-product">
                <li class="mt-4"><a class="active" data-toggle="tab" href="#home">General&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li class="mt-4"><a data-toggle="tab" href="#menu1">Shipping Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li class="mt-4"><a data-toggle="tab" href="#menu2">Tax Certificate Online&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li class="mt-4"><a data-toggle="tab" href="#menu3">Store Locator</a></li>
            </ul>

            <div class="tab-content product-tab-content">
                <div id="home" class="tab-pane fade in active show">
                    <div class="row">
                        <div class="col-lg-6">
                            <form method="POST" action="{{ route('customer.edit-account-post', 'account') }}">
                                @csrf
                            <h4 class="profile-light-txt mt-5">User account information</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="textbox-label">Email</p>
                                    <input class="input-textbox" type="email" value="{{ $user->email }}" disabled/>
                                    <p class="textbox-label">Old Password</p>
                                    <input class="input-textbox form-control @error('old_password') is-invalid @enderror" type="password" name="old_password"/>
                                        @error('old_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    <p class="textbox-label">Password</p>
                                    <input class="input-textbox form-control @error('new_password') is-invalid @enderror" type="password" name="new_password"/>
                                        @error('new_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    <p class="textbox-label">Confirm Password</p>
                                    <input class="input-textbox form-control @error('new_password') is-invalid @enderror" type="password" name="new_password_confirmation" />
                                </div>
                            </div>

                            <h4 class="profile-light-txt mt-4">Contact information</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <p class="textbox-label">First Name</p>
                                    <input class="input-textbox form-control @error('first_name') is-invalid @enderror" type="text" name="first_name" value="{{ old('first_name',$user->details->first_name) }}"/>
                                    @error('first_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Last Name</p>
                                    <input class="input-textbox form-control @error('last_name') is-invalid @enderror" type="text" name="last_name"  value="{{ old('last_name',$user->details->last_name) }}"/>
                                    @error('last_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Business Phone</p>
                                    <input class="input-textbox form-control @error('business_phone') is-invalid @enderror" type="text" name="business_phone" value="{{ old('business_phone', $user->details->business_phone )  }}"/>
                                    @error('business_phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Mobile</p>
                                    <input class="input-textbox form-control @error('phone') is-invalid @enderror" type="text" name="phone" value="{{ old('phone', $user->details->phone) }}"/>
                                    @error('phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Company</p>
                                    <input class="input-textbox form-control @error('company') is-invalid @enderror" type="text" name="company" value="{{ old('company', $user->details->company) }}"/>
                                    @error('company')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Customer Type</p>
                                    @foreach(\Botble\Ecommerce\Models\Customer::$customerType as $type)
                                        <input class="ml-2" type="checkbox" name="customer_type[]" value="{{ $type }}" @if(in_array($type, json_decode($user->details->customer_type)) || old('customer_type') == $type) checked @endif>
                                        <label class="mr-2" for="vehicle1"> {{ $type }}</label>
                                    @endforeach
                                    @error('customer_type')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Store’s Facebook</p>
                                    <input class="input-textbox" type="text" name="store_facebook" value="{{ old('store_facebook', $user->details->store_facebook) }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Store’s Instagram</p>
                                    <input class="input-textbox" type="text" name="store_instagram" value="{{ old('store_instagram', $user->details->store_instagram) }}" />
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Store’s Brick & Mortar address</p>
                                    <input class="input-textbox" type="text" name="mortar_address" value="{{ old('mortar_address', $user->details->mortar_address) }}" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Where did they find us from?</p>
                                    <select class="input-textbox" name="hear_us">
                                        <option @if(is_null($user->details->hear_us)) selected @endif disabled hidden>Select an Option</option>
                                        @foreach(\Botble\Ecommerce\Models\Customer::$hearUs as $key => $hearUs)
                                            <option value="{{ $key }}" @if($user->details->hear_us == $key || old('hear_us') == $key) selected @endif>{{ $hearUs }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Preffered Way of Communication</p>
                                    <select class="input-textbox" name="preferred_communication">
                                        <option @if(is_null($user->details->preferred_communication)) selected @endif disabled hidden>Select an Option</option>
                                        @foreach(\Botble\Ecommerce\Models\Customer::$preferredCommunication as $key => $preferred)
                                            <option value="{{ $key }}" @if($user->details->preferred_communication == $key || old('preferred_communication') == $key) selected @endif>{{ $preferred }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Sales Tax ID</p>
                                    <input class="input-textbox form-control @error('sales_tax_id') is-invalid @enderror" type="text" name="sales_tax_id" value="{{ old('sales_tax_id', $user->details->sales_tax_id) }}" />
                                    @error('sales_tax_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Which shows/events do you attend?</p>
                                    <input class="input-textbox" type="text" name="events_attended" value="{{ old('events_attended' , $user->details->events_attended )}}" />
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Comments</p>
                                    <textarea rows="4" name="comments" class="input-textbox">{{ old('comments',$user->details->comments) }}</textarea>
                                </div>
                                <div class="col-lg-3 mt-3">
                                   {{-- <a href="#" class=" btn cart-btn w-100">Save</a>--}}
                                    <input class=" btn cart-btn w-100" type="submit" placeholder="Save" value="Save"/>
                                </div>
                                <div class="col-lg-3 mt-3">
                                    <a href="{{ url()->current() }}" class=" btn border-btn w-100">Revert</a>
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <div class="refer-area">
                                <p class="refer-link-head ">Referral link</p>
                                <p class="refer-link-para mt-3">Invite a friend to L&B and get rewards for the first order of every person!</p>
                                <input class="mt-3 textbox-refer" type="text" value="http://landbapparel.com/profiles-add.html?ref_code=2447461738" />
                                <div class="mt-3">
                                    <a href="#"><img class="refer-icon" src="./img/icons/rinvision.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rfacebook.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rtwitter.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rmail.png" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg-2 mt-5">
                            <a href="#" class=" btn border-btn w-100">Add a New Address</a>
                        </div>
                        <div class="col-lg-9"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <h4 class="profile-light-txt mt-5">Shipping information</h4>
                            <form method="POST" action="{{ route('customer.edit-account-post', 'address') }}">
                                @csrf
                            <div class="row">
                                    <input type="hidden" name="shipping_id" value="{{ @$user->shippingAddress[0]->id }}">
                                <div class="col-lg-6">
                                    <p class="textbox-label">First Name</p>
                                    <input class="input-textbox form-control @error('shipping_first_name') is-invalid @enderror" type="text" name="shipping_first_name" value="{{ old('shipping_first_name',@$user->shippingAddress[0]->first_name) }}"/>
                                    @error('shipping_first_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Last Name</p>
                                    <input class="input-textbox form-control @error('shipping_last_name') is-invalid @enderror" type="text"  name="shipping_last_name" value="{{ old('shipping_last_name',@$user->shippingAddress[0]->last_name) }}"/>
                                    @error('shipping_last_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Company Name</p>
                                    <input class="input-textbox form-control @error('shipping_company') is-invalid @enderror" type="text"  name="shipping_company" value="{{ old('shipping_company',@$user->shippingAddress[0]->company) }}"/>
                                    @error('shipping_company')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Mobile</p>
                                    <input class="input-textbox form-control @error('shipping_phone') is-invalid @enderror" type="text"  name="shipping_phone" value="{{ old('shipping_phone',@$user->shippingAddress[0]->phone) }}"/>
                                    @error('shipping_phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Address</p>
                                    <input class="input-textbox form-control @error('shipping_address') is-invalid @enderror" type="text"  name="shipping_address" value="{{ old('shipping_address',@$user->shippingAddress[0]->address) }}"/>
                                    @error('shipping_address')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">City</p>
                                    <input class="input-textbox form-control @error('shipping_city') is-invalid @enderror" type="text"  name="shipping_city" value="{{ old('shipping_city',@$user->shippingAddress[0]->city) }}"/>
                                    @error('shipping_city')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Country</p>
                                    {{--<select class="input-textbox">
                                        <option></option>
                                    </select>--}}
                                    <input class="input-textbox form-control @error('shipping_country') is-invalid @enderror" type="text"  name="shipping_country" value="{{ old('shipping_country',@$user->shippingAddress[0]->country) }}"/>
                                    @error('shipping_country')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">State/Province/Region</p>
                                    {{--<select class="input-textbox">
                                        <option></option>
                                    </select>--}}
                                    <input class="input-textbox form-control @error('shipping_state') is-invalid @enderror" type="text" name="shipping_state" value="{{ old('shipping_state',@$user->shippingAddress[0]->state) }}"/>
                                    @error('shipping_state')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Zip/Postal Code</p>
                                    <input class="input-textbox form-control @error('shipping_zip_code') is-invalid @enderror" type="text"  name="shipping_zip_code" value="{{ old('shipping_zip_code',@$user->shippingAddress[0]->zip_code) }}"/>
                                    @error('shipping_zip_code')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <input class="ml-2" type="checkbox" {{ (@$user->shippingAddress[0]->is_default == 1) ?'checked' : ''  }} name="set_default" value="1">
                                    <label class="ml-2 label-setdefault" for="vehicle1"> Set as Default</label>
                                </div>
                                <div class="col-lg-12">
                                    <div class="shipping-checkbox-area mt-2 d-flex">
                                        <p>Billing and shipping addresses are the same</p>
                                        <div class="ml-3">
                                            <input class="ml-2" type="radio" name="billing" value="1" data-toggle="collapse" data-target=".collapseOne.show">
                                            <label class="mr-2 mb-0" for="vehicle1"> Yes</label>
                                            <input class="ml-2" type="radio" name="billing" value="0" data-toggle="collapse" data-target=".collapseOne:not(.show)" checked>
                                            <label class="mr-2 mb-0" for="vehicle1"> No</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="collapseOne panel-collapse collapse show">
                                            <div class="panel-body">
                                                <h4 class="profile-light-txt mt-5">Billing address</h4>
                                                <input type="hidden" name="billing_id" value="{{ @$user->billingAddress[0]->id }}">

                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">First Name</p>
                                                        <input class="input-textbox form-control @error('billing_first_name') is-invalid @enderror" type="text" name="billing_first_name" value="{{ old('billing_first_name',@$user->billingAddress[0]->first_name) }}"/>
                                                        @error('billing_first_name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">Last Name</p>
                                                        <input class="input-textbox form-control @error('billing_last_name') is-invalid @enderror" type="text"  name="billing_last_name" value="{{ old('billing_last_name',@$user->billingAddress[0]->last_name) }}"/>
                                                        @error('billing_last_name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">Company Name</p>
                                                        <input class="input-textbox form-control @error('billing_company') is-invalid @enderror" type="text"  name="billing_company" value="{{ old('billing_company',@$user->billingAddress[0]->company) }}"/>
                                                        @error('billing_company')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">Mobile</p>
                                                        <input class="input-textbox form-control @error('billing_phone') is-invalid @enderror" type="text"  name="billing_phone" value="{{ old('billing_phone',@$user->billingAddress[0]->phone) }}"/>
                                                        @error('billing_phone')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <p class="textbox-label">Address</p>
                                                        <input class="input-textbox form-control @error('billing_address') is-invalid @enderror" type="text"  name="billing_address" value="{{ old('billing_address',@$user->billingAddress[0]->address) }}"/>
                                                        @error('billing_address')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">City</p>
                                                        <input class="input-textbox form-control @error('billing_city') is-invalid @enderror" type="text"  name="billing_city" value="{{ old('billing_city',@$user->billingAddress[0]->city) }}"/>
                                                        @error('billing_city')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">Country</p>
                                                        {{--<select class="input-textbox">
                                                            <option></option>
                                                        </select>--}}
                                                        <input class="input-textbox form-control @error('billing_country') is-invalid @enderror" type="text"  name="billing_country" value="{{ old('billing_country',@$user->billingAddress[0]->country) }}"/>
                                                        @error('billing_country')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">State/Province/Region</p>
                                                        {{--<select class="input-textbox">
                                                            <option></option>
                                                        </select>--}}
                                                        <input class="input-textbox form-control @error('billing_state') is-invalid @enderror" type="text"  name="billing_state" value="{{ old('billing_state',@$user->billingAddress[0]->state) }}"/>
                                                        @error('billing_state')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p class="textbox-label">Zip/Postal Code</p>
                                                        <input class="input-textbox form-control @error('billing_zip_code') is-invalid @enderror" type="text"  name="billing_zip_code" value="{{ old('billing_zip_code',@$user->billingAddress[0]->zip_code) }}"/>
                                                        @error('billing_zip_code')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-5">
                                    <input type="submit" class="btn cart-btn w-100" value="Save">
                                    {{--<a href="#" class=" btn cart-btn w-100">Register</a>--}}
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <div class="refer-area">
                                <p class="refer-link-head ">Referral link</p>
                                <p class="refer-link-para mt-3">Invite a friend to L&B and get rewards for the first order of every person!</p>
                                <input class="mt-3 textbox-refer" type="text" value="http://landbapparel.com/profiles-add.html?ref_code=2447461738" />
                                <div class="mt-3">
                                    <a href="#"><img class="refer-icon" src="./img/icons/rinvision.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rfacebook.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rtwitter.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rmail.png" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <form method="POST" action="{{ route('customer.edit-account-post', 'tax_certificate') }}">
                        @csrf
                    <div class="row mt-4">
                        <div class="col-lg-12 text-center">
                            <h1 class="texas-heading">TEXAS SALES AND USE TAX RESALE CERTIFICATE</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="refer-area mt-4">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <p class="textbox-label">Name of purchaser, firm or agency as shown on permit </p>
                                        <input class="input-textbox bg-white form-control @error('purchaser_name') is-invalid @enderror" type="text"  name="purchaser_name" value="{{ old('purchaser_name',@$user->taxCertificate->purchaser_name) }}"/>
                                        @error('purchaser_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-5">
                                        <p class="textbox-label">Phone (Area code and number)</p>
                                        <input class="input-textbox bg-white form-control @error('purchaser_phone') is-invalid @enderror" type="text"  name="purchaser_phone" value="{{ old('purchaser_phone',@$user->taxCertificate->purchaser_phone) }}"/>
                                        @error('purchaser_phone')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12">
                                        <p class="textbox-label">Address (Street & number, P.O. Box or Route number)</p>
                                        <input class="input-textbox bg-white form-control @error('purchaser_address') is-invalid @enderror" type="text"  name="purchaser_address" value="{{ old('purchaser_address',@$user->taxCertificate->purchaser_address) }}"/>
                                        @error('purchaser_address')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="textbox-label">City, State, ZIP code </p>
                                        <input class="input-textbox bg-white form-control @error('purchaser_city') is-invalid @enderror" type="text"  name="purchaser_city" value="{{ old('purchaser_city',@$user->taxCertificate->purchaser_city) }}"/>
                                        @error('purchaser_city')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-5">
                                        <p class="textbox-label">Texas Sales and Use Tax Permit Number (must contain 11 digits)</p>
                                        <input class="input-textbox bg-white form-control @error('permit_no') is-invalid @enderror" type="text"  name="permit_no" value="{{ old('permit_no',@$user->taxCertificate->permit_no) }}"/>
                                        @error('permit_no')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12">
                                        <p class="textbox-label">Out-of-state retailer”s registration number or Fedral Taxpayers Registry (RFC) number for retailers based in Mexico</p>
                                        <input class="input-textbox bg-white form-control @error('registration_no') is-invalid @enderror" type="text"  name="registration_no" value="{{ old('registration_no',@$user->taxCertificate->registration_no) }}"/>
                                        @error('registration_no')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12">
                                        <h4 class="profile-light-txt mt-4">(Retailers based in Mexico must also provide a copy of their Mexico registration form to the seller.)</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="refer-area mt-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4 class="profile-light-txt mt-4">I, the purchaser named above, claim the right to make a non-taxable purchase (for resale of the taxable items described below or on the attached order or invoice) from:
                                        </h4>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="row mt-3">
                                            <div class="col-lg-2 col-5">
                                                <p class="tax-label ">Seller:</p>
                                            </div>
                                            <div class="col-lg-3 col-7">
                                                <p class="tax-address">L&B</p>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-2 col-5">
                                                <p class="tax-label ">Street address:</p>
                                            </div>
                                            <div class="col-lg-3 col-7">
                                                <p class="tax-address">12801 N STEMMONS FWY STE 710</p>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-2 col-5">
                                                <p class="tax-label ">City, State, ZIP code:</p>
                                            </div>
                                            <div class="col-lg-3 col-7">
                                                <p class="tax-address">FARMERS BRANCH, TX 75234</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <p class="textbox-label">Description of items to be purchased on the attached order or invoice.</p>
                                        <textarea rows="4" class="input-textbox bg-white form-control @error('items_description') is-invalid @enderror" name="items_description">{{ old('items_description',@$user->taxCertificate->items_description) }}</textarea>
                                        @error('items_description')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12">
                                        <p class="textbox-label">Description of the type of business activity generally engaged in or type of items normally sold by the purchaser.</p>
                                        <textarea rows="4" class="input-textbox bg-white form-control @error('business_description') is-invalid @enderror" name="business_description">{{ old('business_description',@$user->taxCertificate->business_description) }}</textarea>
                                        @error('business_description')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="d-flex">
                                            <input class="mt-1 mr-2" type="checkbox" name=" " value=" ">
                                            <label class="mr-2 tax-checkbox-label" for="vehicle1"> The taxable items described above, or on the attached order or invoice, will be resold, rented or leased by me within the geographical limits of the United States of America, its territories and possessions or within the geographical limits of the United Mexican States, in their present form or attached to other taxable items to be sold.</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-12">
                                        <div class="d-flex">
                                            <input class="mt-1 mr-2" type="checkbox" name=" " value=" ">
                                            <label class="mr-2 tax-checkbox-label" for="vehicle1"> I understand that if I make any use of the items in other retention, demonstration or display while holding them for sale, lease or rental, I must pay sales tax on the the items at the time of use based upon either the purchase price or the fair market rental value for the period of time used.</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-12">
                                        <div class="d-flex">
                                            <input class="mt-1 mr-2" type="checkbox" name=" " value=" ">
                                            <label class="mr-2 tax-checkbox-label" for="vehicle1"> I understand that it is a criminal offense to give a resale certificate to the seller for taxable items that i know, at the time of purchase, are purchased for use rather than for the purpose of resale, lease or rental, and depending on the amount of tax evaded, the offence may range from a Class C misdemeanor to a felony of the second degree.</label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="refer-area mt-4">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <p class="textbox-label">Title</p>
                                        <input class="input-textbox bg-white form-control @error('title') is-invalid @enderror" type="text"  name="title" value="{{ old('title',@$user->taxCertificate->title) }}"/>
                                        @error('title')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-5">
                                        <p class="textbox-label">Date</p>
                                        <input class="input-textbox bg-white form-control @error('date') is-invalid @enderror" type="date"  name="date" value="{{ old('date',@$user->taxCertificate->date) }}"/>
                                        @error('date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="textbox-label">Purchaser Sign</p>
                                        <textarea rows="4" class="input-textbox bg-white form-control @error('purchaser_sign') is-invalid @enderror" name="purchaser_sign">{{ old('purchaser_sign',@$user->taxCertificate->purchaser_sign) }}</textarea>
                                        @error('purchaser_sign')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <p class="cert-label">This certificate should be furnished to the supplier. Do not send the completed certificate to the Comptroller of Public Accounts.</p>

                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-lg-2">
                            <input type="submit" class="btn cart-btn w-100" value="Submit">
                        </div>
                    </div>
                    </form>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <div class="row mt-5">
                        <div class="col-lg-6">
                            <div class="d-flex">
                                <input class="mt-1 mr-2" type="checkbox" name=" " value=" ">
                                <label class="mr-2 tax-checkbox-label" for="vehicle1"> Add me to the Store Locator </label>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="textbox-label">Company Name</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Mobile</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Website</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Address</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">City</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Country</p>
                                    <select class="input-textbox">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">State/Province/Region</p>
                                    <select class="input-textbox">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Zip/Postal Code</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-5">
                                    <p class="textbox-label">Customer Type</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-5">
                                    <p class="textbox-label">Customer Type</p>
                                    <input class="input-textbox" type="text" />
                                </div>
                                <div class="col-lg-2">
                                    <a href="#" class=" btn cart-btn w-100 mt-w">Select</a>

                                </div>
                                <div class="col-lg-3 mt-5">
                                    <a href="#" class=" btn cart-btn w-100">Submit</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <div class="refer-area">
                                <p class="refer-link-head ">Referral link</p>
                                <p class="refer-link-para mt-3">Invite a friend to L&B and get rewards for the first order of every person!</p>
                                <input class="mt-3 textbox-refer" type="text" value="http://landbapparel.com/profiles-add.html?ref_code=2447461738" />
                                <div class="mt-3">
                                    <a href="#"><img class="refer-icon" src="./img/icons/rinvision.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rfacebook.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rtwitter.png" /></a>
                                    <a href="#"><img class="refer-icon ml-3" src="./img/icons/rmail.png" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
