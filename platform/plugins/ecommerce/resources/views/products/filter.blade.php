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
                    <h4 class="modal-title text-center w-100 thread-pop-head search-head color-white">Advance Search <span class="variation-name"></span></h4>
                </div>
            </div>
            {{ Form::open(['method' => 'GET', 'class' => 'filter-form', 'id' => 'adv-search-form']) }}
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4 mt-3">
                            <label class="font-bold">Price ($):</label>
                            <div class="d-flex">
                                <div class="col-md-6 pl-0">
                                    <input type="number" name="product_min_price" step="0.1" class="form-control" value="{{request('product_min_price')}}">
                                </div>
                                --
                                <div class="col-md-6 pr-0">
                                    <input type="number" name="product_max_price" step="0.1" class="form-control" value="{{request('product_max_price')}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="mt-4 font-bold">Search in Category:</label><br>
                            <select class="form-control select-search-full" name="prod_category" style="width: 100%">
                                <option selected="selected" value="" disabled="">Select Category</option>
                                @foreach($data['prod_categories'] as $key => $prod_category)
                                    <option value="{{ $key }}" {{request('prod_category') == $key ? 'selected' : ''}}>{{ $prod_category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="font-bold">SEC:</label>
                            <input type="text" name="prod_sec" class="form-control" value="{{request('prod_sec')}}">
                        </div>

                        <div class="col-md-12">
                            <label class="font-bold">Status:</label>
                            <div>
                            @foreach(\Botble\Base\Enums\BaseStatusEnum::$PRODUCT as $prod_status)
                                <div style="display:inline-flex" class="chk-orders">
                                    <input style="width: auto; margin: -7px 0.5rem 0 0;" type="checkbox" name="prod_status" class="form-control" value="{{strtolower($prod_status)}}" {{request('prod_status') == strtolower($prod_status) ? 'checked' : ''}}> <p class="mr-1">{{$prod_status}}</p>
                                </div>
                            @endforeach
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="mt-4 font-bold">Product Type:</label><br>
                            <select class="form-control" name="prod_type" style="width: 100%">
                                <option selected="selected" value="" disabled="">Select Product Type</option>
                                @foreach($data['prod_types'] as $k => $prod_type)
                                    <option value="{{ $k }}" {{request('prod_type') == $k ? 'selected' : ''}}>{{ $prod_type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="mt-4 font-bold">Show Products:</label><br>
                            <select class="form-control" name="show_products" style="width: 100%">
                                <option selected="selected" value="" disabled="">Show Products</option>
                                @foreach($data['show_products'] as $k => $show_product)
                                    <option value="{{ $k }}" {{request('show_products') == $k ? 'selected' : ''}}>{{ $show_product }}</option>
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
                url: '{{ route('orders.save.advance.search','products') }}',
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
