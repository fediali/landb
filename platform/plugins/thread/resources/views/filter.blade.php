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
                        <span class="variation-name"></span>
                    </h4>
                </div>
            </div>
            {{ Form::open(['method' => 'GET', 'class' => 'filter-form', 'id' => 'adv-search-form']) }}
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-4">
                        <label class="font-bold">Status:</label>
                        {!! Form::select('status', \Botble\Base\Enums\BaseStatusEnum::$THREAD,  request('status'), ['class' => 'form-control ui-select', 'style="background-color: #fff !important;"', 'placeholder' => 'Select Status']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="font-bold">Techpack Status:</label>
                        {!! Form::select('thread_status', \Botble\Thread\Models\Thread::$thread_statuses,  request('thread_status'), ['class' => 'form-control ui-select', 'style="background-color: #fff !important;"', 'placeholder' => 'Techpack Status']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="font-bold">Vendor:</label>

                        {!! Form::select('vendor', $vendor,  request('vendor'), ['class' => 'form-control ui-select', 'style="background-color: #fff !important;"', 'placeholder' => 'Select Vendor']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="font-bold">Designer:</label>
                        {!! Form::select('designer', $designer,  request('designer'), ['class' => 'form-control ui-select', 'style="background-color: #fff !important;"', 'placeholder' => 'Select Designer']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="font-bold">Ready To Order:</label>
                        {!! Form::select('order_status', \Botble\Thread\Models\Thread::$READY,  request('order_status'), ['class' => 'form-control ui-select', 'style="background-color: #fff !important;"', 'placeholder' => 'Select Order Status']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="font-bold">PP Sample:</label>
                        {!! Form::select('pp_sample', \Botble\Thread\Models\Thread::$READY,  request('pp_sample'), ['class' => 'form-control ui-select', 'style="background-color: #fff !important;"', 'placeholder' => 'Select PP Sample']) !!}
                    </div>
                </div>

                <div class="d-flex mb-3 mt-3">
                    <div class="d-flex adv-input">
                        <input type="text" name="search_name" class="form-control mr-2" id="search-name">
                        <input type="button" class="btn btn-info" value="Save Search" id="adv-save-search">
                    </div>
                    <div class="text-right adv-input">
                        <button type="button" class="btn btn-danger pull-left ml-5 mr-2" data-dismiss="modal">Close
                        </button>
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
                url: '{{ route('thread.save.advance.search','threads') }}',
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
