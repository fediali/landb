<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <!-- <link rel="icon" href="img/SwingsationsFavicon.png" sizes="36x36" type="image/png"> -->
    <!--    Font Awesome 5.9-->
    <script src="https://kit.fontawesome.com/9c7309bfe2.js"></script>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.14.0/css/all.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <!-- <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> -->
    <!--    Bootstrap 4.3.1-->
    <link rel="stylesheet" href="{{ asset('landb/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('landb/css/jquery.fancybox.css') }}"/>
    <!-- Custom Style Sheet -->
    <link type="text/css" media="screen" rel="stylesheet" href="{{ asset('landb/css/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" />
    <script src="https://fattjs.fattpay.com/js/fattmerchant.js"></script>
    <script src="{{ asset('js/barcodeScanner.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('landb/css/datepickk.min.css') }}">
    <script src="{{ asset('landb/js/datepickk.min.js') }}"></script>
    <script src="{{ asset('landb/js/lazyload.min.js') }}"></script>
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
        <p>Welcome To Lucky and Blessed</p>
    </div>
    <div class="pl-3 pr-3">
{{--      <p class="text-right m-0">Welcome, <b>Ryan</b></p>--}}
        <div class="topnav d-flex pb-0 pt-2">

            <ul>
                <li>
                    <a href="tel:+1234567890"><i class="fal fa-phone-alt"></i> <span>972-243-7860

</span></a>
                </li>
            </ul>
            <ul class="d-flex">
                <li class="search-custom">
                    <a href="#"><i class="fal fa-search"></i></a>

                </li>
                <div id="top-sear" class="top-search">
                      <form action="#">
                        <div class="d-flex main-search">
                        <input class="search-inp" type="text" autocomplete="off" placeholder="Search.." name="search">
                        <button class="tp-search-btn" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                        <span class="tp-close-btn pl-2"><i class="fa fa-times" aria-hidden="true"></i></span>
                        </div>
                      </form>
                    </div>
                <li>
                    <a href="{{ route('public.cart_index') }}"><i class="fal fa-shopping-cart"></i></a>
                </li>
                <span style="
                    background: black;
                    color: white;
                    /* position: absolute; */
                    margin-left: -15px;
                    height: 20px;
                    width: 20px;
                    text-align: center;
                    border-radius: 30px;
                    font-size: 12px;
                    /* vertical-align: -webkit-baseline-middle; */
                    /* display: inherit; */
                    margin: a;
                    margin-top: -10px;
                " id="user-cart-count">{{ cart_count() }}</span>
                <li>
                    <a href="{{ route('customer.overview') }}"><i class="fal fa-user"></i></a>
                </li>
                <li>
                    @if(auth('customer')->user())
                        <a href="{{ route('customer.edit-account') }}">Welcome, {{ auth('customer')->user()->name }}</a> | <a href="{{ route('public.logout') }}"><i
                                class="fa fa-sign-out"></i></a>
                    @else
                        <a href="{{ route('customer.login') }}">Sign In</a>
                        <a href="{{ route('customer.login') }}">Sign Up</a>
                    @endauth

                </li>
            </ul>
        </div>
        <nav class="navbar d-block">
          <div class="row">
            <div class="col-lg-12 text-center">
            <a class="navbar-brand" href="{{ route('public.index') }}"> <img src="{{ asset('landb/img/Logo.png') }}" alt=""> </a>

            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 d-grid text-center header-col">
            <div class="d-flex navbar-parent m-auto">
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
            </div>
          </div>


        </nav>
    </div>
</header>
<!-- <div class="loading-overlay">
    <span class="fas fa-spinner fa-3x fa-spin"></span>
</div>

<div id="myOverlay" class="overlay">
  <span class="closebtn" onclick="closeSearch()" title="Close Overlay">×</span>
  <div class="overlay-content">
    <form action="/action_page.php">
      <input type="text" autocomplete="off" placeholder="Search.." name="search">
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>
  </div>
</div> -->

<!-- <style>
* {
  box-sizing: border-box;
}

.overlay {
  height: 100%;
  width: 100%;
  display: none;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: rgb(0,0,0);
  background-color: rgba(0,0,0, 0.9);
}

.overlay-content {
  position: relative;
  top: 46%;
  width: 80%;
  text-align: center;
  margin-top: 30px;
  margin: auto;
}

.overlay .closebtn {
  position: absolute;
  top: 20px;
  right: 45px;
  font-size: 60px;
  cursor: pointer;
  color: white;
}

.overlay .closebtn:hover {
  color: #ccc;
}

.overlay input[type=text] {
  padding: 15px;
  font-size: 17px;
  border: none;
  float: left;
  width: 80%;
  background: white;
}

.overlay input[type=text]:hover {
  background: #f1f1f1;
}

.overlay button {
  float: left;
  width: 20%;
  padding: 15px;
  background: #ddd;
  font-size: 17px;
  border: none;
  cursor: pointer;
}

.overlay button:hover {
  background: #bbb;
}
</style> -->
<script src="{{ asset('landb/js/flipbook.js') }}"></script>

<!-- <script>
function openSearch() {
  document.getElementById("myOverlay").style.display = "block";
}

function closeSearch() {
  document.getElementById("myOverlay").style.display = "none";
}
</script> -->

<script>
// function openSearch() {
//   document.getElementById("top-sear").style.display = "block";
// }

// function closeSearch() {
//   document.getElementById("top-sear").style.display = "flex";
// }

</script>
