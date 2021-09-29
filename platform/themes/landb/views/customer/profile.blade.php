<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>My Account</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <div class="tab">
                <button class="tablinks" onclick="openCity(event, 'Dashboard')" id="defaultOpen">Dashboard</button>
                <button class="tablinks" onclick="openCity(event, 'Orders')">Orders</button>
                <button class="tablinks" onclick="openCity(event, 'Addresses')">Addresses</button>
                <button class="tablinks" onclick="openCity(event, 'Account')">Account<span class="cl-white">.</span>Details</button>
                <a href="{{ route('customer.edit-account') }}">
                    <button class="tablinks">Edit<span class="cl-white">.</span>Account</button>
                </a>
                <a href="{{ route('public.logout') }}">
                    <button class="tablinks">Logout</button>
                </a>
            </div>

            <div id="Dashboard" class="tabcontent">
                <p class="mt-3">From your account dashboard you can view your <a href="#" class="color-black"
                                                                                 onclick="openCity(event, 'Orders')">recent
                        orders,</a> manage your <a href="#" class="color-black" onclick="openCity(event, 'Addresses')">shipping
                        and billing addresses</a>,
                    and edit your <a href="#" class="color-black" onclick="openCity(event, 'Account')">password and
                        account details.</a></p>
            </div>

            <div id="Orders" class="tabcontent">
                @if(!count($user->orders))
                    <div class="woocommerce-info"> No order has been made yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Order#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">Status</th>
                                {{--                            <th scope="col">Payment</th>--}}
                                <th scope="col">Subtotal</th>
                                <th scope="col">Shipping</th>
                                <th scope="col">Total</th>
                                <th scope="col">Shipping Method</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user->orders->where('is_finished','=', 1) as $order)
                                <tr>
                                    <th scope="row"><a
                                            href="{{ route('public.order.status', ['id' => $order->id]) }}">{{ $order->id }}</a>
                                    </th>
                                    <td>{{ date('M/d/Y', strtotime($order->created_at)) }}</td>
                                    <td><span style="text-transform: uppercase;">{{ $order->order_type}}</span></td>
                                    <td><span class="badge badge-primary"
                                              style="text-transform: uppercase;">{{ $order->status }}</span></td>
                                    {{--                                    <td>            --}}
                                    {{--                                        @if($order->is_confirmed == 1)--}}
                                    {{--                                            <span class="badge badge-success">Paid</span>--}}
                                    {{--                                        @else--}}
                                    {{--                                            <span class="badge badge-danger">Unpaid</span>--}}
                                    {{--                                        @endif--}}
                                    {{--                                    </td>--}}
                                    <td>{{ $order->sub_total }}</td>
                                    <td>{{ $order->shipping_amount }}</td>
                                    <td>{{ $order->amount }}
                                        <span class="d-block " style=" font-size: 12px; font-weight: 600;"> Discount : {{ $order->discount_amount }}</span>
                                    </td>
                                    <td><span class="badge badge-secondary">{{ $order->shipping_method }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                @endif
            </div>

            <div id="Addresses" class="tabcontent">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="{{ route('customer.edit-account-post', 'address') }}">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="shipping_id" value="{{ @$user->shippingAddress[0]->id }}">
                                <div class="col-lg-6">
                                    <h4 class="profile-light-txt mt-2">Shipping information</h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <p class="textbox-label">First Name</p>
                                            <input
                                                class="input-textbox form-control @error('shipping_first_name') is-invalid @enderror"
                                                type="text" name="shipping_first_name"
                                                value="{{ old('shipping_first_name',@$user->shippingAddress[0]->first_name) }}"/>
                                            @error('shipping_first_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Last Name</p>
                                            <input
                                                class="input-textbox form-control @error('shipping_last_name') is-invalid @enderror"
                                                type="text" name="shipping_last_name"
                                                value="{{ old('shipping_last_name',@$user->shippingAddress[0]->last_name) }}"/>
                                            @error('shipping_last_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Company Name</p>
                                            <input
                                                class="input-textbox form-control @error('shipping_company') is-invalid @enderror"
                                                type="text" name="shipping_company"
                                                value="{{ old('shipping_company',@$user->shippingAddress[0]->company) }}"/>
                                            @error('shipping_company')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Mobile</p>
                                            <input
                                                class="input-textbox form-control @error('shipping_phone') is-invalid @enderror"
                                                type="text" name="shipping_phone"
                                                value="{{ old('shipping_phone',@$user->shippingAddress[0]->phone) }}"/>
                                            @error('shipping_phone')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-12">
                                            <p class="textbox-label">Address</p>
                                            <input
                                                class="input-textbox form-control @error('shipping_address') is-invalid @enderror"
                                                type="text" name="shipping_address"
                                                value="{{ old('shipping_address',@$user->shippingAddress[0]->address) }}"/>
                                            @error('shipping_address')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">City</p>
                                            <input
                                                class="input-textbox form-control @error('shipping_city') is-invalid @enderror"
                                                type="text" name="shipping_city"
                                                value="{{ old('shipping_city',@$user->shippingAddress[0]->city) }}"/>
                                            @error('shipping_city')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Country</p>
                                            {{--<select class="input-textbox">
                                                <option></option>
                                            </select>--}}
                                            {{--<input class="input-textbox form-control @error('shipping_country') is-invalid @enderror" type="text"  name="shipping_country" value="{{ old('shipping_country',@$user->shippingAddress[0]->country) }}"/>--}}
                                            <select
                                                class="input-textbox form-control  @error('shipping_country') is-invalid @enderror"
                                                name="shipping_country">
                                                <option selected hidden disabled>Select a Country</option>
                                                @foreach(get_countries() as $key => $country)
                                                    <option
                                                        @if(old('shipping_country',@$user->shippingAddress[0]->country) == $key) selected
                                                        @endif value="{{ $key }}">{{ $country }}</option>
                                                @endforeach
                                            </select>
                                            @error('shipping_country')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">State/Province/Region</p>
                                            {{--<select class="input-textbox">
                                                <option></option>
                                            </select>--}}
                                            {{--<input class="input-textbox form-control @error('shipping_state') is-invalid @enderror" type="text" name="shipping_state" value="{{ old('shipping_state',@$user->shippingAddress[0]->state) }}"/>--}}
                                            <select
                                                class="input-textbox form-control  @error('shipping_state') is-invalid @enderror"
                                                name="shipping_state">
                                                <option selected hidden disabled>Select a State</option>
                                                @foreach(get_states(@$user->shippingAddress[0]->country) as $key => $state)
                                                    <option
                                                        @if(old('shipping_state',@$user->shippingAddress[0]->state) == $key) selected
                                                        @endif value="{{ $key }}">{{ $state }}</option>
                                                @endforeach
                                            </select>
                                            @error('shipping_state')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Zip/Postal Code</p>
                                            <input
                                                class="input-textbox form-control @error('shipping_zip_code') is-invalid @enderror"
                                                type="text" name="shipping_zip_code"
                                                value="{{ old('shipping_zip_code',@$user->shippingAddress[0]->zip_code) }}"/>
                                            @error('shipping_zip_code')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-12">
                                            <input class="ml-2" type="checkbox"
                                                   {{ (@$user->shippingAddress[0]->is_default == 1) ?'checked' : ''  }} name="set_default"
                                                   value="1">
                                            <label class="ml-2 label-setdefault" for="vehicle1"> Set as Default</label>
                                        </div>
                                        {{--<div class="col-lg-12">
                                            <div class="shipping-checkbox-area mt-2 d-flex">
                                                <p>Billing and shipping addresses are the same</p>
                                                <div class="ml-3">
                                                    <input class="ml-2" type="radio" name="billing" value="1" data-toggle="collapse" data-target=".collapseOne.show">
                                                    <label class="mr-2 mb-0" for="vehicle1"> Yes</label>
                                                    <input class="ml-2" type="radio" name="billing" value="0" data-toggle="collapse" data-target=".collapseOne:not(.show)" checked>
                                                    <label class="mr-2 mb-0" for="vehicle1"> No</label>
                                                </div>

                                            </div>
                                        </div>--}}
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h4 class="profile-light-txt mt-5">Billing address</h4>
                                    <div class="row">
                                        <input type="hidden" name="billing_id"
                                               value="{{ @$user->billingAddress[0]->id }}">
                                        <div class="col-lg-6">
                                            <p class="textbox-label">First Name</p>
                                            <input
                                                class="input-textbox form-control @error('billing_first_name') is-invalid @enderror"
                                                type="text" name="billing_first_name"
                                                value="{{ old('billing_first_name',@$user->billingAddress[0]->first_name) }}"/>
                                            @error('billing_first_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Last Name</p>
                                            <input
                                                class="input-textbox form-control @error('billing_last_name') is-invalid @enderror"
                                                type="text" name="billing_last_name"
                                                value="{{ old('billing_last_name',@$user->billingAddress[0]->last_name) }}"/>
                                            @error('billing_last_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Company Name</p>
                                            <input
                                                class="input-textbox form-control @error('billing_company') is-invalid @enderror"
                                                type="text" name="billing_company"
                                                value="{{ old('billing_company',@$user->billingAddress[0]->company) }}"/>
                                            @error('billing_company')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Mobile</p>
                                            <input
                                                class="input-textbox form-control @error('billing_phone') is-invalid @enderror"
                                                type="text" name="billing_phone"
                                                value="{{ old('billing_phone',@$user->billingAddress[0]->phone) }}"/>
                                            @error('billing_phone')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-12">
                                            <p class="textbox-label">Address</p>
                                            <input
                                                class="input-textbox form-control @error('billing_address') is-invalid @enderror"
                                                type="text" name="billing_address"
                                                value="{{ old('billing_address',@$user->billingAddress[0]->address) }}"/>
                                            @error('billing_address')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">City</p>
                                            <input
                                                class="input-textbox form-control @error('billing_city') is-invalid @enderror"
                                                type="text" name="billing_city"
                                                value="{{ old('billing_city',@$user->billingAddress[0]->city) }}"/>
                                            @error('billing_city')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Country</p>
                                            {{--<select class="input-textbox">
                                                <option></option>
                                            </select>--}}
                                            {{--<input class="input-textbox form-control @error('billing_country') is-invalid @enderror" type="text"  name="billing_country" value="{{ old('billing_country',@$user->billingAddress[0]->country) }}"/>--}}
                                            <select
                                                class="input-textbox form-control  @error('billing_country') is-invalid @enderror"
                                                name="billing_country">
                                                <option selected hidden disabled>Select a Country</option>
                                                @foreach(get_countries() as $key => $country)
                                                    <option
                                                        @if(old('billing_country',@$user->billingAddress[0]->country) == $key) selected
                                                        @endif value="{{ $key }}">{{ $country }}</option>
                                                @endforeach
                                            </select>
                                            @error('billing_country')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">State/Province/Region</p>
                                            {{--<select class="input-textbox">
                                                <option></option>
                                            </select>--}}
                                            {{--<input class="input-textbox form-control @error('billing_state') is-invalid @enderror" type="text"  name="billing_state" value="{{ old('billing_state',@$user->billingAddress[0]->state) }}"/>--}}
                                            <select
                                                class="input-textbox form-control  @error('billing_state') is-invalid @enderror"
                                                name="billing_state">
                                                <option selected hidden disabled>Select a State</option>
                                                @foreach(get_states(@$user->billingAddress[0]->country) as $key => $state)
                                                    <option
                                                        @if(old('shipping_state',@$user->billingAddress[0]->state) == $key) selected
                                                        @endif value="{{ $key }}">{{ $state }}</option>
                                                @endforeach
                                            </select>
                                            @error('billing_state')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="textbox-label">Zip/Postal Code</p>
                                            <input
                                                class="input-textbox form-control @error('billing_zip_code') is-invalid @enderror"
                                                type="text" name="billing_zip_code"
                                                value="{{ old('billing_zip_code',@$user->billingAddress[0]->zip_code) }}"/>
                                            @error('billing_zip_code')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 mt-5">
                                    <input type="submit" class="btn cart-btn w-100" value="Save">
                                    {{--<a href="#" class=" btn cart-btn w-100">Register</a>--}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="Account" class="tabcontent">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="{{ route('customer.edit-account-post', 'account') }}">
                            @csrf
                            <h4 class="profile-light-txt mt-2">User account information</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <p class="textbox-label">Email</p>
                                    <input class="input-textbox" type="email" value="{{ $user->email }}" disabled/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Old Password</p>
                                    <input
                                        class="input-textbox form-control @error('old_password') is-invalid @enderror"
                                        type="password" name="old_password"/>
                                    @error('old_password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Password</p>
                                    <input
                                        class="input-textbox form-control @error('new_password') is-invalid @enderror"
                                        type="password" name="new_password"/>
                                    @error('new_password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Confirm Password</p>
                                    <input
                                        class="input-textbox form-control @error('new_password') is-invalid @enderror"
                                        type="password" name="new_password_confirmation"/>
                                </div>
                            </div>

                            <h4 class="profile-light-txt mt-4">Contact information</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <p class="textbox-label">First Name</p>
                                    <input class="input-textbox form-control @error('first_name') is-invalid @enderror"
                                           type="text" name="first_name"
                                           value="{{ old('first_name',$user->details->first_name) }}"/>
                                    @error('first_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Last Name</p>
                                    <input class="input-textbox form-control @error('last_name') is-invalid @enderror"
                                           type="text" name="last_name"
                                           value="{{ old('last_name',$user->details->last_name) }}"/>
                                    @error('last_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Business Phone</p>
                                    <input
                                        class="input-textbox form-control @error('business_phone') is-invalid @enderror"
                                        type="text" name="business_phone"
                                        value="{{ old('business_phone', $user->details->business_phone )  }}"/>
                                    @error('business_phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Mobile</p>
                                    <input class="input-textbox form-control @error('phone') is-invalid @enderror"
                                           type="text" name="phone" value="{{ old('phone', $user->details->phone) }}"/>
                                    @error('phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Company</p>
                                    <input class="input-textbox form-control @error('company') is-invalid @enderror"
                                           type="text" name="company"
                                           value="{{ old('company', $user->details->company) }}"/>
                                    @error('company')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Customer

                                        Type</p>
                                    @foreach(\Botble\Ecommerce\Models\Customer::$customerType as $type)
                                        <input class="ml-2" type="checkbox" name="customer_type[]" value="{{ $type }}"
                                               @if(in_array($type, json_decode($user->details->customer_type)) || old('customer_type') == $type) checked @endif>
                                        <label class="mr-2" for="vehicle1"> {{ $type }}</label>
                                    @endforeach
                                    @error('customer_type')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Store’s Facebook</p>
                                    <input class="input-textbox" type="text" name="store_facebook"
                                           value="{{ old('store_facebook', $user->details->store_facebook) }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Store’s Instagram</p>
                                    <input class="input-textbox" type="text" name="store_instagram"
                                           value="{{ old('store_instagram', $user->details->store_instagram) }}"/>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Store’s Brick & Mortar address</p>
                                    <input class="input-textbox" type="text" name="mortar_address"
                                           value="{{ old('mortar_address', $user->details->mortar_address) }}"/>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Where did they find us from?</p>
                                    <select class="input-textbox" name="hear_us">
                                        <option @if(is_null($user->details->hear_us)) selected @endif disabled hidden>
                                            Select an Option
                                        </option>
                                        @foreach(\Botble\Ecommerce\Models\Customer::$hearUs as $key => $hearUs)
                                            <option value="{{ $key }}"
                                                    @if($user->details->hear_us == $key || old('hear_us') == $key) selected @endif>{{ $hearUs }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Preffered Way of Communication</p>
                                    <select class="input-textbox" name="preferred_communication">
                                        <option @if(is_null($user->details->preferred_communication)) selected
                                                @endif disabled hidden>Select an Option
                                        </option>
                                        @foreach(\Botble\Ecommerce\Models\Customer::$preferredCommunication as $key => $preferred)
                                            <option value="{{ $key }}"
                                                    @if($user->details->preferred_communication == $key || old('preferred_communication') == $key) selected @endif>{{ $preferred }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Sales Tax ID</p>
                                    <input
                                        class="input-textbox form-control @error('sales_tax_id') is-invalid @enderror"
                                        type="text" name="sales_tax_id"
                                        value="{{ old('sales_tax_id', $user->details->sales_tax_id) }}"/>
                                    @error('sales_tax_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <p class="textbox-label">Which shows/events do you attend?</p>
                                    <input class="input-textbox" type="text" name="events_attended"
                                           value="{{ old('events_attended' , $user->details->events_attended )}}"/>
                                </div>
                                <div class="col-lg-12">
                                    <p class="textbox-label">Comments</p>
                                    <textarea rows="4" name="comments"
                                              class="input-textbox">{{ old('comments',$user->details->comments) }}</textarea>
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
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(document).ready(function () {
        $("#filtertoggle").click(function () {
            $(this).toggleClass("on");
            $("#filtermenu").slideToggle();
        });
    });
</script>
<script>
    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>
