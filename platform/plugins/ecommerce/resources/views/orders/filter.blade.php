<div class="wrapper-filter">
    <p>Saved Search</p>
    <input type="hidden" class="filter-data-url" value="{{ route('tables.get-filter-input') }}">
    {{ Form::open(['method' => 'GET', 'class' => 'filter-form']) }}
    <div class="filter_list inline-block filter-items-wrap">
        <div class="filter-item form-filter filter-item-default">
            <div class="ui-select-wrapper">
                <select name="search_id" class="ui-select" required>
                    <option value="">Saved Search</option>
                    @foreach($searches as $key => $value)
                        <option value="{{ $key }}" {{request('search_id') == $key ? 'selected' : ''}}>{{ $value }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
            </div>
            <button type="submit" class="btn btn-primary btn-apply">Search</button>
            <a href="{{ URL::current() }}" class="btn btn-info">{{ trans('core/table::table.reset') }}</a>
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-adv-search">
                Advance Search
            </button>
        </div>
    </div>
    {{ Form::close() }}
</div>


<div class="modal fade in" id="modal-adv-search" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex w-100">
                    <button type="button" class="close color-white" data-dismiss="modal" aria-label="Close">X</button>
                    <h4 class="modal-title text-center w-100 thread-pop-head search-head color-white">Advance Search
                        <span class="variation-name"></span>
                    </h4>
                </div>
            </div>
            {{ Form::open(['method' => 'GET', 'class' => 'filter-form', 'id' => 'adv-search-form']) }}
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-4">
                        <label class="font-bold">Company:</label>
                        <input type="text" name="company" class="form-control" value="{{request('company')}}">
                    </div>

                    <div class="col-md-4">
                        <label class="font-bold">Customer:</label>
                        <input type="text" name="customer_name" class="form-control"
                               value="{{request('customer_name')}}">
                    </div>

                    <div class="col-md-4">
                        <label class="font-bold">Email:</label>
                        <input type="email" name="customer_email" class="form-control"
                               value="{{request('customer_email')}}">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label class="font-bold">Manager:</label>
{{--                        <input type="text" name="manager" class="form-control" value="{{request('manager')}}">--}}
                        {!! Form::select('manager', get_salesperson(),  request('manager'), ['class' => 'form-control','placeholder'=>'Select Manager']) !!}
                    </div>
                    <div class="col-md-4 mt-3">
                        <label class="font-bold">Type:</label>
                        {!! Form::select('order_type', \Botble\Ecommerce\Models\Order::$ORDER_TYPES,  request('order_type'), ['class' => 'form-control','placeholder'=>'Select Status']) !!}
                    </div>

                    <div class="col-md-4 mt-3">
                        <label class="font-bold">Total ($):</label>
                        <div class="d-flex">
                            <div class="col-md-6 pl-0">
                                <input type="number" name="order_min_total" step="0.1" class="form-control" value="{{request('order_min_total')}}">
                            </div>
                            --
                            <div class="col-md-6 pr-0">
                                <input type="number" name="order_max_total" step="0.1" class="form-control" value="{{request('order_max_total')}}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="font-bold mb-0">Date:</label>
                        <div class="w-100 c-datepicker-date-editor  J-datepicker-range-day">
                            <i class="c-datepicker-range__icon kxiconfont icon-clock"></i>
                            <input placeholder="Start" name="order_from_date" style="width:48% !important;" class="c-datepicker-data-input only-date" value="{{request('order_from_date')}}">
                            <span class="c-datepicker-range-separator">-</span>
                            <input placeholder="End" name="order_to_date" style="width:48% !important;" class="c-datepicker-data-input only-date" value="{{request('order_to_date')}}">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="font-bold">Order Status:</label>
                        <div>
                            @foreach($data['order_statuses'] as $order_status)
                                <div style="display:inline-flex" class="chk-orders">
                                    <input style="width: auto; margin: -7px 0.5rem 0 0;" type="checkbox" name="order_status[]" class="form-control" value="{{strtolower($order_status)}}" {{request('order_status') == strtolower($order_status) ? 'checked' : ''}}>
                                    <p class="mr-1">{{$order_status}}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="font-bold">Payment Methods:</label>
                        <div>
                            @foreach($data['payment_methods'] as $key => $payment_method)
                                <div style="display:inline-flex">
                                    <input style="width: auto; margin: -7px 0.5rem 0 0;" type="checkbox" name="payment_method" class="form-control" value="{{$key}}" {{request('payment_method') == $key ? 'checked' : ''}}>
                                    <p class="mr-1">{{$payment_method}}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex">
                            <input style="width: auto; margin: -7px 0.5rem 0 0;" type="checkbox" name="online_order" class="form-control" value="{{\Botble\Ecommerce\Models\Order::ONLINE}}" {{request('online_order') == \Botble\Ecommerce\Models\Order::ONLINE ? 'checked' : ''}}>
                            {{\Botble\Ecommerce\Models\Order::$PLATFORMS[\Botble\Ecommerce\Models\Order::ONLINE]}}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="mt-4 font-bold">Promotions:</label><br>
                        <select class="form-control" name="coupon_code" style="width: 100%">
                            <option selected="selected" value="" disabled="">Select Promotion</option>
                            @foreach($data['coupon_codes'] as $key => $coupon_code)
                                <option value="{{ $key }}" {{request('coupon_code') == $key ? 'selected' : ''}}>{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="d-flex mb-3 mt-3">
                    <div class="d-flex adv-input">
                        <input type="text" name="search_name" class="form-control mr-2" id="search-name">
                        <input type="button" class="btn btn-info" value="Save Search" id="adv-save-search">
                    </div>
                    <div class="text-right adv-input">
                        <button type="button" class="btn btn-danger pull-left ml-5 mr-2" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Search">
                    </div>
                </div>

            </div>

            {{ Form::close() }}
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $(document).on('click', '#adv-save-search', function () {
            if ($('input#search-name').val() == '') {
                toastr['warning']('Search Name Required.', 'Validation Error');
                return;
            }
            $.ajax({
                url: '{{ route('orders.save.advance.search','orders') }}',
                type: 'POST',
                data: $('#adv-search-form').serialize(),
                success: function (data) {
                    location.reload();
                },
                error: function (request, status, error) {
                    location.reload();
                    toastr['warning']('Something went wrong.', 'Validation Error');
                }
            });
        });
    });
</script>
