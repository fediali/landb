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
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="{{ asset('landb/css/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" />
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
                    <a href="tel:+1234567890"><i class="fal fa-phone-alt"></i> <span>(123) 456-7890</span></a>
                </li>
            </ul>
            <ul class="d-flex">
                <li>
                    <a href="#" onclick="openSearch()"><i class="fal fa-search"></i></a>
                </li>
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
                ">1</span>
                <li>
                    <a href="#"><i class="fal fa-user"></i></a>
                </li>
                <li>
                    @if(auth('customer')->user())
                        <a>{{ auth('customer')->user()->name }}</a> | <a href="{{ route('public.logout') }}"><i
                                class="fa fa-sign-out"></i></a>
                    @else
                        <a href="{{ route('customer.login') }}">Sign In</a>
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

<div id="myOverlay" class="overlay">
  <span class="closebtn" onclick="closeSearch()" title="Close Overlay">×</span>
  <div class="overlay-content">
    <form action="/action_page.php">
      <input type="text" placeholder="Search.." name="search">
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>
  </div>
</div>

<style> 
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
</style>

<script>
function openSearch() {
  document.getElementById("myOverlay").style.display = "block";
}

function closeSearch() {
  document.getElementById("myOverlay").style.display = "none";
}
</script>