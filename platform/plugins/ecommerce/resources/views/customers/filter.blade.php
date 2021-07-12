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
                        <option
                            value="{{ $key }}" {{request('search_id') == $key ? 'selected' : ''}}>{{ $value }}</option>
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
                        <span class="variation-name"></span></h4>
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
                        <input type="text" name="manager" class="form-control" value="{{request('manager')}}">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label class="font-bold">Status:</label>
                        <div class="ui-select-wrapper">
                            {!! Form::select('status', \Botble\Base\Enums\BaseStatusEnum::$CUSTOMER,  request('status'), ['class' => 'form-control ui-select','placeholder'=>'Select Status']) !!}
                        </div>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label class="font-bold">Last Order:</label>
                        <input type="date" name="last_order" class="form-control" value="{{request('last_order')}}">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label class="font-bold">Last Visit:</label>
                        <input type="date" name="last_visit" class="form-control" value="{{request('last_visit')}}">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label class="font-bold">Spend:</label>
                        <input type="number" name="spend" class="form-control" step="0.1" value="{{request('spend')}}">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="d-flex">
                        <div class="col-md-4">
                            <label class="font-bold">No Sales Rep:</label>
                            <input style="width: auto; margin: -7px 0.5rem 0 0;" type="checkbox" name="no_sales_rep"
                                   class="form-control" value="1" {{request('no_sales_rep') == 1 ? 'checked' : ''}}>
                            <p class="mr-1"></p>
                        </div>
                        <div class="col-md-4 ">
                            <label class="font-bold">Merged Account:</label>
                            <input style="width: auto; margin: -7px 0.5rem 0 0;" type="checkbox" name="merged_account"
                                   class="form-control" value="1" {{request('merged_account') == 1 ? 'checked' : ''}}>
                            <p class="mr-1"></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="mt-4 font-bold">Order Report:</label><br>
                    {{--{{ Form::open(['method' => 'GET', 'class' => 'filter-form']) }}--}}
                    <div class="ui-select-wrapper">
                        <select name="report_type" class="ui-select">
                            <option value="">Select Report Type</option>
                            @foreach($report_types as $key => $value)
                                <option
                                    value="{{ $key }}" {{request('report_type') == $key ? 'selected' : ''}}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <svg class="svg-next-icon svg-next-icon-size-16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                        </svg>
                    </div>
                    {{--{{ Form::close() }}--}}
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


<script>
    $(document).ready(function () {
        $(document).on('click', '#adv-save-search', function () {
            if ($('input#search-name').val() == '') {
                toastr['warning']('Search Name Required.', 'Validation Error');
                return;
            }
            $.ajax({
                url: '{{ route('orders.save.advance.search','customers') }}',
                type: 'POST',
                data: $('#adv-search-form').serialize(),
                success: function (data) {
                    location.reload();
                },
                error: function (request, status, error) {
                    //location.reload();
                    toastr['warning']('Something went wrong.', 'Validation Error');
                }
            });
        });
    });
</script>
