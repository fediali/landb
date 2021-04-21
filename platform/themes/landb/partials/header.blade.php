<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <!-- <link rel="icon" href="img/SwingsationsFavicon.png" sizes="36x36" type="image/png"> -->
    <!--    Font Awesome 5.9-->
    <script src="https://kit.fontawesome.com/9c7309bfe2.js"></script>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.14.0/css/all.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <!-- <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> -->
    <!--    Bootstrap 4.3.1-->
    <link rel="stylesheet" href="{{ asset('landb/css/bootstrap.min.css') }}" />
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="{{ asset('landb/css/style.css') }}" />
    <title>LandBAppreal</title>
    <style>
        .loading-overlay {
            display: none;
            background: rgba(255, 255, 255, 0.7);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.is-active {
            display: flex;
        }

        .code {
            font-family: monospace;
            /*   font-size: .9em; */
            color: #dd4a68;
            background-color: rgb(238, 238, 238);
            padding: 0 3px;
        }
    </style>
</head>

<body>
<header>
    <div class="topbar">
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry</p>
    </div>
    <div class="container">
        <div class="topnav d-flex">
            <ul>
                <li>
                    <i class="fal fa-phone-alt"></i> <span>(123) 456-7890</span>
                </li>
            </ul>
            <ul class="d-flex">
                <li>
                    <a href="#"><i class="fal fa-search"></i></a>
                </li>
                <li>
                    <a href="{{ route('public.cart_index') }}"><i class="fal fa-shopping-cart"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fal fa-user"></i></a>
                </li>
                <li>
                    @if(auth('customer')->user())
                        <a>{{ auth('customer')->user()->name }}</a> | <a href="{{ route('public.logout') }}"><i class="fa fa-sign-out"></i></a>
                    @else
                        <a href="{{ route('public.login') }}">Sign In</a>
                    @endauth

                </li>
            </ul>
        </div>
        <nav class="navbar ">
            <a class="navbar-brand" href="#"> <img src="{{ asset('landb/img/Logo.png') }}" alt=""> </a>

            <div class=" navbar-parent d-flex">
                {!!
                    Menu::renderMenuLocation('main-menu', [
                        'options' => [],
                        'theme' => true,
                        'view' => 'main-menu',
                    ])
                !!}
                <button class="toggle-menu">
                    <span></span>
                </button>
                <div id="menu" class="">
                    {!!
                        Menu::renderMenuLocation('categories-menu', [
                            'options' => [],
                            'theme' => true,
                            'view' => 'categories-menu',
                        ])
                    !!}
                </div>
            </div>
        </nav>
    </div>
</header>
<div class="loading-overlay">
    <span class="fas fa-spinner fa-3x fa-spin"></span>
</div>