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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex w-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                    <h4 class="modal-title text-center w-100 thread-pop-head">Advance Search <span class="variation-name"></span></h4>
                </div>
            </div>
            {{ Form::open(['method' => 'GET', 'class' => 'filter-form']) }}
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label class="font-bold">Company:</label>
                            <input type="text" name="company" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="font-bold">Customer:</label>
                            <input type="text" name="customer_name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="font-bold">Email:</label>
                            <input type="email" name="customer_email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="font-bold">Manager:</label>
                            <input type="text" name="manager" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="font-bold">Total ($):</label>
                            <div class="col-md-6">
                                <input type="number" name="order_min_total" step="0.1" class="form-control">
                            </div>
                            --
                            <div class="col-md-6">
                                <input type="number" name="order_max_total" step="0.1" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="font-bold">Order Status:</label>
                            @foreach($data['order_statuses'] as $order_status)
                                <input type="checkbox" name="order_status" class="form-control" value="{{strtolower($order_status)}}"> {{$order_status}}
                            @endforeach
                        </div>
                        <div class="col-md-12">
                            <label class="font-bold">Payment Methods:</label>
                            @foreach($data['payment_methods'] as $key => $payment_method)
                                <input type="checkbox" name="payment_method" class="form-control" value="{{$key}}"> {{$payment_method}}
                            @endforeach
                        </div>
                        <div class="col-md-4">
                            <label class="font-bold">Online Orders:</label>
                            <input type="checkbox" name="online_order" class="form-control" value="{{\Botble\Ecommerce\Models\Order::ONLINE}}">
                            {{\Botble\Ecommerce\Models\Order::$PLATFORMS[\Botble\Ecommerce\Models\Order::ONLINE]}}
                        </div>
                        <div class="col-md-4">
                            <label class="mt-4 font-bold">Promotions:</label><br>
                            <select class="form-control" name="coupon_code" style="width: 100%">
                                <option selected="selected" value="" disabled="">Select Promotion</option>
                                @foreach($data['coupon_codes'] as $key => $coupon_code)
                                    <option value="{{ $key }}">{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input type="button" class="btn btn-primary" value="Save">
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
