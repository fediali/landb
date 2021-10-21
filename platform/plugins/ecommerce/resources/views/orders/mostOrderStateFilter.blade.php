<div class="wrapper-filter">
    <p>Search</p>
    <input type="hidden" class="filter-data-url" value="{{ route('tables.get-filter-input') }}">
    {{ Form::open(['method' => 'GET', 'class' => 'filter-form']) }}
    <div class="filter_list inline-block filter-items-wrap">
        <div class="filter-item form-filter filter-item-default">
            <div class="col-md-12 p-0 mb-3">
                <label class="font-bold mb-0">Date:</label>
                <div class="w-100 c-datepicker-date-editor  J-datepicker-range-day">
                    <i class="c-datepicker-range__icon kxiconfont icon-clock"></i>
                    <input placeholder="From Date" name="from_date" style="width:48% !important;" class="c-datepicker-data-input only-date" value="{{@$data['search_items']['from_date']}}">
                    <span class="c-datepicker-range-separator">-</span>
                    <input placeholder="To Date" name="to_date" style="width:48% !important;" class="c-datepicker-data-input only-date" value="{{@$data['search_items']['to_date']}}">
                </div>
            </div>
            {{--<div class="col-md-12">
                <label class="mt-4 font-bold">Order Status:</label><br>
                <select class="form-control mb-3" name="order_status" style="width: 100%">
                    <option selected="selected" value="" disabled="">Select Order Status</option>
                    @foreach($data['order_statuses'] as $order_status)
                        <option value="{{strtolower($order_status)}}" {{@$data['search_items']['order_status'] == strtolower($order_status) ? 'selected' : ''}}>{{ $order_status }}</option>
                    @endforeach
                </select>
            </div>--}}
            <button type="submit" class="btn btn-primary btn-apply">Search</button>
            <a href="{{ URL::current() }}" class="btn btn-info">{{ trans('core/table::table.reset') }}</a>
        </div>
    </div>
    {{ Form::close() }}
</div>


<script>
    $(document).ready(function () {});
</script>
