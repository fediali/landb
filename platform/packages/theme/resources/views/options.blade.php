@extends('core/base::layouts.master')
@section('content')
    @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), THEME_OPTIONS_MODULE_SCREEN_NAME) @endphp
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
                                        <h2>Home Section 1</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">Latest Collections Placements</p>
                                    </div>
                                </div>

                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        <div class="form-group">
                                            <label for="home_section_1_heading" class="control-label">Heading</label>
                                            <input class="form-control" placeholder="Heading text" data-counter="120" name="home_section_1_heading" type="text" value="{{ setting('theme-landb-home_section_1_heading') }}" id="home_section_1_heading">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_section_1_description" class="control-label">Descriptions</label>
                                            <input class="form-control" placeholder="Description text" data-counter="120" name="home_section_1_description" type="text" value="" id="home_section_1_description">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_section_1_link" class="control-label">Link</label>
                                            <input class="form-control" placeholder="Link" data-counter="120" name="home_section_1_link" type="text" value="" id="home_section_1_link">
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
                            <div class="flexbox-annotated-section">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-title pd-all-20">
                                        <h2>Home Section 2</h2>
                                    </div>
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">Browse Collections Placements</p>
                                    </div>
                                </div>

                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        <div class="form-group">
                                            <label for="home_section_2_heading" class="control-label">Heading</label>
                                            <input class="form-control" placeholder="Heading text" data-counter="120" name="home_section_2_heading" type="text" value="" id="home_section_2_heading">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_section_2_link" class="control-label">Link</label>
                                            <input class="form-control" placeholder="Link" data-counter="120" name="home_section_2_link" type="text" value="" id="home_section_2_link">
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
                                        <p class="color-note">Video Placement</p>
                                    </div>
                                </div>

                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        <div class="form-group">
                                            <label for="home_section_3_video" class="control-label">Video URL</label>
                                            <input class="form-control" placeholder="Link" data-counter="120" name="home_section_3_video" type="text" value="" id="home_section_3_video">
                                            @include('core/base::forms.partials.images', ['name' => 'home_section_3_video', 'values' => ''])
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

                                <div class="flexbox-annotated-section-content">
                                    <div class="wrapper-content pd-all-20">
                                        <div class="form-group">
                                            <label for="home_section_4_heading" class="control-label">Heading</label>
                                            <input class="form-control" placeholder="Heading text" data-counter="120" name="home_section_4_heading" type="text" value="" id="home_section_4_heading">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_section_4_description" class="control-label">Descriptions</label>
                                            <input class="form-control" placeholder="Description text" data-counter="120" name="home_section_4_description" type="text" value="" id="home_section_4_description">
                                        </div>
                                        <div class="form-group">
                                            <label for="home_section_4_link" class="control-label">Link</label>
                                            <input class="form-control" placeholder="Link" data-counter="120" name="home_section_4_link" type="text" value="" id="home_section_4_link">
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
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
