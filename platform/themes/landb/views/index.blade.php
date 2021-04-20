<div class="banner">
    <!-- <img src="landb/img/Banner.png" alt=""> -->
    <div id="demo" class="carousel slide mainslide" data-ride="carousel">

        <!-- Indicators -->
        <ul class="carousel-indicators">
            <li data-target="#demo" data-slide-to="0" class="active"></li>
            <li data-target="#demo" data-slide-to="1"></li>
            <li data-target="#demo" data-slide-to="2"></li>
        </ul>
        
        <!-- The slideshow -->
        <div class="carousel-inner">
            <a href="#" class="carousel-item active">
            <img src="landb/img/Banner.png" alt="Los Angeles asds" width="100%" height="500">
            </a>
            <a href="#" class="carousel-item">
            <img src="landb/img/Banner.png" alt="Chicago" width="100%" height="500">
            </a>
            <a href="#" class="carousel-item">
            <img src="landb/img/Banner.png" alt="New York" width="100%" height="500">
            </a>
        </div>
        
        <!-- Left and right controls -->
        <a class="carousel-control-prev" href="#demo" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#demo" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
        </div>
</div>

<div class="logocenter text-center">
    <img class="revealUp"src="landb/img/lucky&blessed_logo_sign_Black 1.png" alt="">
</div>
@if(count($home_featured))
    {!! Theme::partial('index/featured', compact('home_featured')) !!}
@endif

@if(count($latest_collection))
    {!! Theme::partial('index/latest_collection_1', compact('latest_collection')) !!}
@endif


<section class="browse_collection py-4">
    <div class="browse_img">
        <div class="imgframe1 ">
            <img  class="bone" src="landb/img/browse-img-1.png" alt="">
            <div class="imgcaption">
                <a href="#" class="save"><i class="fas fa-save"></i> Save</a>
                <a href="#" class="search"><i class="fal fa-search"></i></a>
            </div>

        </div>
        <div class="imgframe2"><img class="btwo" src="landb/img/browse-img-2.png" alt="">
            <div class="imgcaption">
                <a href="#" class="save"><i class="fas fa-save"></i> Save</a>
                <a href="#" class="search"><i class="fal fa-search"></i></a>
            </div>
        </div>
    </div>
    <div class="b_text">
        <div class="small_h">
            Browse Collection
        </div>
        <h1 class="revealUp"> <span></span> <small>Jeans</small> <br> Dresses Pants Skirts Shorts Booties </h1>
        <a href="{{ URL::to('products') }}" class="lookbook_btn">View Everything</a>
    </div>
    <div class="browse_img">
        <div class="imgframe3">
            <img class="bthree" src="landb/img/browse-img-3.png" alt="">
            <div class="imgcaption">
                <a href="#" class="save"><i class="fas fa-save"></i> Save</a>
                <a href="#" class="search"><i class="fal fa-search"></i></a>
            </div>
        </div>
        <div class="imgframe4"><img class="bfour" src="landb/img/browse-img-4.png" alt="">
            <div class="imgcaption">
                <a href="#" class="save"><i class="fas fa-save"></i> Save</a>
                <a href="#" class="search"><i class="fal fa-search"></i></a>
            </div>
        </div>
    </div>
</section>
<section class="parallax_video">
    <video src="https://css-tricks-post-videos.s3.us-east-1.amazonaws.com/708209935.mp4" autoplay loop playsinline muted></video>
    <!-- <img src="landb/img/Video.png" alt=""> -->
</section>
@if(count($latest_collection) > 2)
    {!! Theme::partial('index/latest_collection_2', compact('latest_collection')) !!}
@endif
@if(count($latest_collection) > 4)
    {!! Theme::partial('index/latest_collection_carousel', compact('latest_collection')) !!}
@endif

<section class="gallerysection" id="gallery">
    <div class="container">
        <div class="gallerywrap">
            <div class="gdiv">
                <img class="imgsize1" src="landb/img/g-img-3.png" alt="">
                <br>
                <img class="imgsize2" class="float-right" src="landb/img/g-img-6.png" alt="">
            </div>
            <div class="gdiv">
                <div class="topgimg">
                    <img class="imgsize3" src="landb/img/g-img-1.png" alt="">
                    <img class="imgsize4" src="landb/img/g-img-2.png" alt="">
                </div>
                <img class="imgsize5" src="landb/img/g-img-4.png" alt="">
                <br>
                <img class="imgsize6" class="float-right" src="landb/img/g-img-7.png" alt="">
            </div>
            <div class="gdiv">
                <div class="instaicon">
                    <i class="fa fa-instagram"></i>
                </div>
                <img class="imgsize7" src="landb/img/g-img-5.png" alt="">
                <br>
                <img class="imgsize8" src="landb/img/g-img-8.png" alt="">
            </div>
        </div>
    </div>
</section>
<div id="night"></div>
<section class="newsletter">
    <div class="container">
        <div class="news_text">
            <div class="small_h" >Newsletter
            </div>
            <h1 class="revealUp"> <em>Subscribe</em> to get <br> the latest updates </h1>
            <div class="inputarea">
                <span class="type">Type</span>
                <span class="customer">Customer</span>
                <input type="text" class="inputtext" placeholder="Email address">
                <a href="#" class="lookbook_btn">LookBook</a>
            </div>

        </div>
    </div>
</section>

<section class="needhelp">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="help_heading">
                    <h3>Need <br> <em>Help?</em></h3>
                </div>
            </div>
            <div class="col-md-8">

                <div class="row">
                    <div class="col-md-4">
                        <div class="helpbox">
                            <h5>Where to buy?</h5>
                            <p>Interested? Find the nearest store to me</p>
                            <strong>Product Locator</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="helpbox">
                            <h5>Wholesale</h5>
                            <p>Already a member? Login to discover our collections.</p>
                            <strong>Login</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="helpbox">
                            <h5>Need Help?</h5>
                            <p>Haven't found what you're looking for? Contact us.</p>
                            <strong>Customer support</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>