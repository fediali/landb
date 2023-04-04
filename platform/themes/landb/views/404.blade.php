@php
    SeoHelper::setTitle(__('404 - Not found'));
    Theme::fire('beforeRenderTheme', app(\Botble\Theme\Contracts\Theme::class));
@endphp

{!! Theme::partial('header') !!}
<style>
    .error-code {
        color: #22292f;
        font-size: 3rem;
    }

    .error-border {
        background-color: var(--color-1st);
        height: .25rem;
        width: 6rem;
        margin-bottom: 1.5rem;
    }

    .error-page a {
        color: var(--color-1st);
    }

    .error-page {
        margin-top: 140px !important;
    }

    .error-page ul li {
        margin-bottom : 5px;
        font-size: 18px;
    }
</style>

<div class="container error-page">
    <div class="error-code text-center mt-5">
        404
    </div>

    <div class="error-border text-center"></div>
        <h5 class="text-center">{{ __('This may have occurred because of several reasons') }}:</h5>
        <ul class="mt-4 mb-4">
            <li>{{ __('The page you requested does not exist.') }}</li>
            <li>{{ __('The link you clicked is no longer.') }}</li>
            <li>{{ __('The page may have moved to a new location.') }}</li>
            <li>{{ __('An error may have occurred.') }}</li>
            <li>{{ __('You are not authorized to view the requested resource.') }}</li>
        </ul>
        <br>
        <strong class="text-center mb-5 d-block">{!! clean(__('Please try again in a few minutes, or alternatively return to the homepage by <a href=":link">clicking here</a>.', ['link' => route('public.single')])) !!}</strong>
    </div>
</div>
{!! Theme::partial('footer') !!}


