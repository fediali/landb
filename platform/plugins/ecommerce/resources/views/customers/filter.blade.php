<div class="wrapper-filter">
    <p>{{ trans('core/table::table.filters') }}</p>

    <input type="hidden" class="filter-data-url" value="{{ route('tables.get-filter-input') }}">

    {{ Form::open(['method' => 'GET', 'class' => 'filter-form']) }}
    <div class="filter_list inline-block filter-items-wrap">
        <div class="filter-item form-filter filter-item-default">
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
        </div>
    </div>
    <div style="margin-top: 10px;">
        <a href="{{ URL::current() }}" class="btn btn-info">{{ trans('core/table::table.reset') }}</a>
        <button type="submit" class="btn btn-primary btn-apply">{{ trans('core/table::table.apply') }}</button>
    </div>
    {{ Form::close() }}


</div>
