<div class="banner">
    <!-- <img src="landb/img/Banner.png" alt=""> -->
    <div id="demo" class="carousel slide mainslide" data-ride="carousel">

        <!-- Indicators -->
        <ul class="carousel-indicators">
            @foreach($slider->sliderItems as $item)
                <li data-target="#demo" data-slide-to="{{ $loop->iteration-1 }}" class="{{ ($loop->first) ? 'active': '' }}"></li>
            @endforeach
        </ul>
        
        <!-- The slideshow -->
        <div class="carousel-inner">
            @foreach($slider->sliderItems as $item)
                <a href="{{ $item->link }}" class="carousel-item {{ ($loop->first) ? 'active': '' }}">
                <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" width="100%" height="750px">
                </a>
            @endforeach
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

    {!! Theme::partial('index/latest_collection_1', compact('latest_collection')) !!}


<section class="browse_collection py-4">
    @php
        $product_ids = setting('theme-landb-home_section_2_products', json_encode(\Botble\Ecommerce\Models\Product::inRandomOrder()->limit(4)->pluck('id')->all()));
       $product_ids = json_decode($product_ids);
        $products2 = \Botble\Ecommerce\Models\Product::whereIn('id', $product_ids)->get();
    @endphp
    @if(count($products2))
        <div class="browse_img">
            <div class="imgframe1 ">
                {!! image_html_generator(@$products2[0]->images[0], null, null, null, true, 'bone') !!}
                <div class="imgcaption">
                    <a href="{!! generate_product_url('detail', $products2[0]->id, $products2[0]->product_slug)  !!}" class="save"><i class="fas fa-save"></i> Save</a>
                    <a href="{!!  generate_product_url('detail', $products2[0]->id, $products2[0]->product_slug)  !!}" class="search"><i class="fal fa-search"></i></a>
                </div>

            </div>
            @if(isset($products2[1]))
                <div class="imgframe2">
                    {!! image_html_generator(@$products2[1]->images[0], null, null, null, true, 'btwo') !!}
                    <div class="imgcaption">
                        <a href="{!! generate_product_url('detail', $products2[1]->id, $products2[1]->product_slug)  !!}" class="save"><i class="fas fa-save"></i> Save</a>
                        <a href="{!!  generate_product_url('detail', $products2[1]->id, $products2[1]->product_slug)  !!}" class="search"><i class="fal fa-search"></i></a>
                    </div>
                </div>
            @endif
        </div>
    @endif
    <div class="b_text">
        <div class="small_h">
            Browse Collection
        </div>
        <h1 class="revealUp"> <span></span>{{ setting('theme-landb-home_section_2_heading', 'Browse Collection') }} </h1>
        @if(!empty(setting('theme-landb-home_section_2_link')))
            <a href="{{ setting('theme-landb-home_section_2_link') }}" class="lookbook_btn">View Everything</a>
        @endif
    </div>
    @if(count($products2) > 2)
    <div class="browse_img">
        <div class="imgframe3">
            {!! image_html_generator(@$products2[2]->images[0], null, null, null, true, 'bthree') !!}
            <div class="imgcaption">
                <a href="{!! generate_product_url('detail', $products2[2]->id, $products2[2]->product_slug)  !!}" class="save"><i class="fas fa-save"></i> Save</a>
                <a href="{!!  generate_product_url('detail', $products2[2]->id, $products2[2]->product_slug)  !!}" class="search"><i class="fal fa-search"></i></a>
            </div>
        </div>
        @if(isset($products2[3]))
            <div class="imgframe4">
                {!! image_html_generator(@$products2[3]->images[0], null, null, null, true, 'bfour') !!}
                <div class="imgcaption">
                    <a href="{!! generate_product_url('detail', $products2[3]->id, $products2[3]->product_slug)  !!}" class="save"><i class="fas fa-save"></i> Save</a>
                    <a href="{!!  generate_product_url('detail', $products2[3]->id, $products2[3]->product_slug)  !!}" class="search"><i class="fal fa-search"></i></a>
                </div>
            </div>
        @endif
    </div>
    @endif
</section>
<section class="parallax_video">
    @php
        $video_url = setting('theme-landb-home_section_3_video', 'https://css-tricks-post-videos.s3.us-east-1.amazonaws.com/708209935.mp4');
    @endphp
    <video src="{{ (strpos($video_url, 'http') !== false) ? $video_url : 'storage/'.$video_url }}" autoplay loop playsinline muted></video>
    <!-- <img src="landb/img/Video.png" alt=""> -->
</section>
    {!! Theme::partial('index/latest_collection_2', compact('latest_collection')) !!}

@if(count($latest_collection))
    {!! Theme::partial('index/latest_collection_carousel', compact('latest_collection')) !!}
@endif

<!-- <section class="gallerysection" id="gallery">
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
</section> -->
<div class="grid-container">
    @php
        $product_ids = setting('theme-landb-home_section_5_products', json_encode(\Botble\Ecommerce\Models\Product::inRandomOrder()->limit(9)->pluck('id')->all()));
       $product_ids = json_decode($product_ids);
        $products5 = \Botble\Ecommerce\Models\Product::whereIn('id', $product_ids)->get();
    @endphp
      <div class="grid">
      @foreach($products5 as $product)
        <div class="gridLayer @if($loop->iteration == 4) centerPiece @endif">
          <div class="gridBlock" style="background-image: url('{{ !empty(@$product->images[0]) ? asset('storage/'.@$product->images[0]):asset('images/default.jpg') }}');">
              <a href="{!!  generate_product_url('detail', $product->id, $product->product_slug)  !!}"></a>
          </div>
        </div>
      @endforeach
        {{--<div class="gridLayer">
          <div class="gridBlock"></div>
        </div>
        <div class="gridLayer">
          <div class="gridBlock"></div>
        </div>
        <div class="gridLayer centerPiece">
          <div class="gridBlock centerBlock"></div>
        </div>
        <div class="gridLayer">
          <div class="gridBlock">
            <a href="https://greensock.com" target="_blank"></a>
          </div>
        </div>
        <div class="gridLayer">
          <div class="gridBlock"></div>
        </div>
        <div class="gridLayer">
          <div class="gridBlock"></div>
        </div>
        <div class="gridLayer">
          <div class="gridBlock"></div>
        </div>
        <div class="gridLayer">
          <div class="gridBlock"></div>
        </div>--}}
      </div>
    </div>
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
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js"></script>
    <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js"></script>

<script>
        console.log(innerHeight,'innerHeight');

      gsap
        .timeline({
          scrollTrigger: {
            trigger: ".grid-container",
            start: "top top",
            end: () => innerHeight * 9,
            scrub: true,
            pin: ".grid",
            anticipatePin: 1,
          },
          
        })
        .set(".gridBlock:not(.centerBlock)", { autoAlpha: 0 })
        .to(
          ".gridBlock:not(.centerBlock)",
          { duration: 0.1, autoAlpha: 1 },
          0.001
        )
        .from(".gridLayer", { scale: 3.3333, ease: "none" });

      // Images to make it look better, not related to the effect
      /*const size = Math.max(innerWidth, innerHeight);
      gsap.set(".gridBlock", {
        backgroundImage: (i) =>
          `url(https://picsum.photos/${size}/${size}?random=${i})`,
      });*/

      const bigImg = new Image();
      bigImg.addEventListener("load", function () {
        gsap.to(".centerPiece .gridBlock", {
          autoAlpha: 1,
          duration: 0.5,
        });
      });

      bigImg.src = `https://picsum.photos/${size}/${size}?random=50`;
    </script>

    <script>
      $( document ).ready(function() {
        $('.carousel').carousel({
    interval: 2000,
    cycle: true,
    pause: "null"
})
});
    </script>
 