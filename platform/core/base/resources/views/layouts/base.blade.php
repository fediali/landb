<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>{{ page_title()->getTitle() }}</title>

    <meta name="robots" content="noindex,follow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (setting('admin_logo') || config('core.base.general.logo'))
        <meta property="og:image"
              content="{{ setting('admin_logo') ? RvMedia::getImageUrl(setting('admin_logo')) : url(config('core.base.general.logo')) }}">
    @endif
    <meta name="description"
          content="{{ strip_tags(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) }}">
    <meta property="og:description"
          content="{{ strip_tags(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) }}">

    @if (setting('admin_favicon') || config('core.base.general.favicon'))
        <link rel="icon shortcut"
              href="{{ setting('admin_favicon') ? RvMedia::getImageUrl(setting('admin_favicon'), 'thumb') : url(config('core.base.general.favicon')) }}">
    @endif

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    {!! Assets::renderHeader(['core']) !!}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" />
    @yield('head')

    @stack('header')
</head>
<body class="@yield('body-class', 'page-sidebar-closed-hide-logo page-content-white page-container-bg-solid')"
      style="@yield('body-style')">
{!! apply_filters(BASE_FILTER_HEADER_LAYOUT_TEMPLATE, null) !!}

@yield('page')

@include('core/base::elements.common')

{!! Assets::renderFooter() !!}

@yield('javascript')

<div id="stack-footer">
    @stack('footer')
</div>

{!! apply_filters(BASE_FILTER_FOOTER_LAYOUT_TEMPLATE, null) !!}

<script>
  window.laravel_echo_port='{{env("LARAVEL_ECHO_PORT")}}';
</script>
<script src="//{{ Request::getHost() }}:{{env('LARAVEL_ECHO_PORT')}}/socket.io/socket.io.js"></script>
{{--
<script src="{{ url('public/vendor/core/core/base/js/laravel-echo-setup.js') }}" type="text/javascript"></script>
--}}
<script src="{{ asset('vendor/core/core/base/js/laravel-echo-setup.js') }}"></script>

<script type="text/javascript">
  window.Echo.channel('push_thread_notification_{{(Auth::check()) ? Auth::user()->id : ''}}')
      .listen('.ThreadEvent', (data) => {
  toastr['success'](data.title, 'New Thread Notification');
  });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous"></script>
<!-- <script>
    $('.carousel').owlCarousel({
      center: false,
      nav: true,
      items: 1,
      margin: 1
    });
</script> --> 
</body>
</html>
