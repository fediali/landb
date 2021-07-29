@extends('core/base::layouts.master')
@section('content')
    @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), THEME_OPTIONS_MODULE_SCREEN_NAME) @endphp
    @php
        $products = get_products_data();
    @endphp
    <div id="theme-option-header">
        <div class="display_header">
            <h2>{{ trans('packages/theme::theme.theme_options') }}</h2>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="theme-option-container">
        {!! Form::open(['route' => 'theme.options', 'method' => 'POST']) !!}
            <div class="theme-option-sticky">
                <div class="info_bar">
                    <div class="float-left">
                        @if (ThemeOption::getArg('debug') == true) <span class="theme-option-dev-mode-notice">{{ trans('packages/theme::theme.developer_mode') }}</span>@endif
                    </div>
                    <div class="theme-option-action_bar">
                        {!! apply_filters(THEME_OPTIONS_ACTION_META_BOXES, null, THEME_OPTIONS_MODULE_SCREEN_NAME) !!}
                        <button type="submit" class="btn btn-primary button-save-theme-options">{{ trans('packages/theme::theme.save_changes') }}</button>
                    </div>
                </div>
            </div>
            <div class="theme-option-sidebar">
                <ul class="nav nav-tabs tab-in-left">
                    @foreach (ThemeOption::constructSections() as $section)
                        <li class="nav-item">
                            <a href="#tab_{{ $section['id'] }}" class="nav-link @if ($loop->first) active @endif" data-toggle="tab">@if (!empty($section['icon']))<i class="{{ $section['icon'] }}"></i> @endif {{ __($section['title']) }}</a>
                        </li>
                    @endforeach
                        <li class="nav-item">
                            <a href="#tab_home_sections" class="nav-link" data-toggle="tab"><i class="fa fa-home"></i> Home page</a>
                        </li>
                </ul>
            </div>
            <div class="theme-option-main">
                <div class="tab-content tab-content-in-right">
                    @foreach(ThemeOption::constructSections() as $section)
                        <div class="tab-pane @if ($loop->first) active @endif" id="tab_{{ $section['id'] }}">
                            @foreach (ThemeOption::constructFields($section['id']) as $field)
                                <div class="form-group @if ($errors->has($field['attributes']['name'])) has-error @endif">
                                    {!! Form::label($field['attributes']['name'], __($field['label']), ['class' => 'control-label']) !!}
                                    {!! ThemeOption::renderField($field) !!}
                                    @if (array_key_exists('helper', $field))
                                        <span class="help-block">{!! __($field['helper']) !!}</span>
                                    @endif
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    @endforeach

                        <div class="tab-pane " id="tab_home_sections">
                            <div class="flexbox-annotated-section">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-title pd-all-20">
                                        <h2>Home Main Section</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">Top heading</p>
                                    </div>
                                </div><input type="checkbox" name="home_main_section_status" value="1" {!! (setting('theme-landb-home_main_section_status') == 1) ? 'checked': 1 !!}>

                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        <div class="form-group">
                                            <label for="home_main_section_heading" class="control-label">Heading</label>
                                            <input class="form-control" placeholder="Heading text" data-counter="120" name="home_main_section_heading" type="text" value="{{ setting('theme-landb-home_main_section_heading') }}" id="home_main_section_heading">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_main_section_heading" class="control-label">Description</label>
                                            <textarea class="form-control" placeholder="Description here" data-counter="300" name="home_main_section_description" type="text" value="{{ setting('theme-landb-home_main_section_description') }}" id="home_main_section_description"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
                            <div class="flexbox-annotated-section">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-title pd-all-20">
                                        <h2>This Just In</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">This Just In's Placements</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="home_section_1_status" value="1" {!! (setting('theme-landb-home_section_1_status') == 1) ? 'checked': 1 !!}>
                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                       {{-- <div class="form-group">
                                            <label for="home_section_1_heading" class="control-label">Heading</label>
                                            <input class="form-control" placeholder="Heading text" data-counter="120" name="home_section_1_heading" type="text" value="{{ setting('theme-landb-home_section_1_heading') }}" id="home_section_1_heading">
                                        </div>--}}
                                        <div class="form-group form-group-no-margin">
                                            <label for="home_section_1_link" class="control-label">Products(Max: 4)</label>
                                            <div class="multi-choices-widget list-item-checkbox">
                                                {!! Form::customSelect("home_section_1_products[]", $products, json_decode(setting('theme-landb-home_section_1_products')), ['class'    => 'select-search-full','multiple' => 'multiple']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
                            <div class="flexbox-annotated-section">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-title pd-all-20">
                                        <h2>Hey Y'All</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">Het Y'All Image Placement</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="home_section_2_status" value="1" {!! (setting('theme-landb-home_section_2_status') == 1) ? 'checked': 1 !!}>
                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        {{--<div class="form-group">
                                            <label for="home_section_2_heading" class="control-label">Heading</label>
                                            <input class="form-control" placeholder="Heading text" data-counter="120" name="home_section_2_heading" type="text" value="{{ setting('theme-landb-home_section_2_heading') }}" id="home_section_2_heading">
                                        </div>--}}
                                       {{-- <div class="form-group">
                                            <label for="home_section_2_link" class="control-label">Link</label>
                                            <input class="form-control" placeholder="Link" data-counter="120" name="home_section_2_link" type="text" value="{{ setting('theme-landb-home_section_2_link') }}" id="home_section_2_link">
                                        </div>--}}
                                        <div class="form-group form-group-no-margin">
                                            <label for="home_section_2_link" class="control-label">Images(Max: 1)</label>
                                            <div class="multi-choices-widget list-item-checkbox">

                                                {{--<input class="form-control" placeholder="Link" data-counter="120" name="home_section_2_image" type="text" value="{{ setting('theme-landb-home_section_2_image') }}" id="home_section_2_image">--}}
                                                {{--{!! Form::customSelect("home_section_2_products[]", $products, json_decode(setting('theme-landb-home_section_2_products')), ['class'    => 'select-search-full','multiple' => 'multiple']) !!}--}}
                                                @include('core/base::forms.partials.images', ['name' => 'home_section_2_image[]', 'values' => setting('theme-landb-home_section_2_image')])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
                            <div class="flexbox-annotated-section">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-title pd-all-20">
                                        <h2>Home Section 3</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">Our Products images Placement</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="home_section_3_status" value="1"  {!! (setting('theme-landb-home_section_3_status') == 1) ? 'checked': 1 !!}>
                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        <div class="form-group">
                                            <label for="home_section_3_video" class="control-label">Images</label>
                                            {{--<input class="form-control" placeholder="Link" data-counter="120" name="home_section_3_images" type="text" value="{{ setting('theme-landb-home_section_3_images') }}" id="home_section_3_images">--}}
                                            @include('core/base::forms.partials.images', ['name' => 'home_section_3_images[]', 'values' => setting('theme-landb-home_section_3_images')])
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
                            <div class="flexbox-annotated-section">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-title pd-all-20">
                                        <h2>Home Section 4</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">Latest Collections Placements</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="home_section_4_status" value="1"  {!! (setting('theme-landb-home_section_4_status') == 1) ? 'checked': 1 !!}>
                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        {{--<div class="form-group">
                                            <label for="home_section_4_heading" class="control-label">Heading</label>
                                            <input class="form-control" placeholder="Heading text" data-counter="120" name="home_section_4_heading" type="text" value="{{ setting('theme-landb-home_section_4_heading') }}" id="home_section_4_heading">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_section_4_description" class="control-label">Descriptions</label>
                                            <input class="form-control" placeholder="Description text" data-counter="120" name="home_section_4_description" type="text" value="{{ setting('theme-landb-home_section_4_description') }}" id="home_section_4_description">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_section_4_link" class="control-label">Link</label>
                                            <input class="form-control" placeholder="Link" data-counter="120" name="home_section_4_link" type="text" value="{{ setting('theme-landb-home_section_4_link') }}" id="home_section_4_link">
                                        </div>
                                        <div class="form-group form-group-no-margin">
                                            <label for="home_section_4_link" class="control-label">Products(Max: 2)</label>
                                            <div class="multi-choices-widget list-item-checkbox">
                                               --}}{{-- @include('plugins/ecommerce::product-categories.partials.categories-checkbox-option-line', [
                                                    'categories' => $products,
                                                    'value'      => json_decode(setting('theme-landb-home_section_4_products')),
                                                    'currentId'  => null,
                                                    'name'       => 'home_section_4_products[]'
                                                ])--}}{{--
                                                {!! Form::customSelect("home_section_4_products[]", $products, json_decode(setting('theme-landb-home_section_4_products')), ['class'    => 'select-search-full','multiple' => 'multiple']) !!}
                                            </div>
                                        </div>--}}
                                    </div>
                                </div>
                            </div><hr>
                            {{--<div class="flexbox-annotated-section">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-title pd-all-20">
                                        <h2>Home Section 5</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">Products gallery Placements</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="home_section_5_status" value="1"  {!! (setting('theme-landb-home_section_5_status') == 1) ? 'checked': 1 !!}>
                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        <div class="form-group form-group-no-margin">
                                            <label for="home_section_5_products" class="control-label">Products(Max: 9)</label>
                                            <div class="multi-choices-widget list-item-checkbox">
                                                --}}{{--@include('core/base::forms.fields.multi-check-list', [
                                                    'categories' => $products,
                                                    'value'      => json_decode(setting('theme-landb-home_section_5_products')),
                                                    'currentId'  => null,
                                                    'name'       => 'home_section_5_products[]'
                                                ]
                                                )--}}{{--
                                                --}}{{--<select class="select-search-full ui-select ui-select select2-hidden-accessible" multiple="" id="home_section_5_products[]" name="home_section_5_products[]" tabindex="-1" aria-hidden="true">
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" @if (in_array($product->id,  json_decode(setting('theme-landb-home_section_5_products')))) checked="checked" @endif>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>--}}{{--
                                                {!! Form::customSelect("home_section_5_products[]", $products, json_decode(setting('theme-landb-home_section_5_products')), ['class'    => 'select-search-full','multiple' => 'multiple']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><hr>--}}
                        </div>
                </div>
            </div>
            <div class="theme-option-sticky">
                <div class="info_bar">
                    <div class="theme-option-action_bar">
                        {!! apply_filters(THEME_OPTIONS_ACTION_META_BOXES, null, THEME_OPTIONS_MODULE_SCREEN_NAME) !!}
                        <button type="submit" class="btn btn-primary button-save-theme-options">{{ trans('packages/theme::theme.save_changes') }}</button>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@stop
