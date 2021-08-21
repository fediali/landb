<button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fa fa-angle-up"></i></button>
<div class="banner">
    <!-- <img src="landb/img/Banner.png" alt=""> -->
    <div id="demo" class="carousel slide mainslide" data-ride="carousel">

        <!-- Indicators -->
        <ul class="carousel-indicators">
            @foreach($slider->sliderItems as $item)
                <li data-target="#demo" data-slide-to="{{ $loop->iteration-1 }}"
                    class="{{ ($loop->first) ? 'active': '' }}"></li>
            @endforeach
        </ul>

        <!-- The slideshow -->
        <div class="carousel-inner">
            @foreach($slider->sliderItems as $item)
                <a href="{{ $item->link }}" class="carousel-item {{ ($loop->first) ? 'active': '' }}">
                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" width="100%"
                         height="750px">
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
<div class="row mt-5 mb-3">
    <div class="col-lg-2"></div>
    <div class="col-lg-8 text-center">
        <h2 class="font-north western-txt">
            {!! setting('theme-landb-home_main_section_heading') !!}
        </h2>
        <p class="mt-2 font-madeg ft-20">{!! setting('theme-landb-home_main_section_description') !!}</p>
    </div>
    <div class="col-lg-2"></div>
</div>
<section>
    @if(setting('theme-landb-home_section_1_status') == 1)
        <div class="row mt-2 mb-5">
            <div class="col-lg-12">
                <h2 class="text-center just-head">
                    New Arrivals
                </h2>
                <div class="shoplisting home-listing detail-shoplist ml-4 mr-4">
                    @php
                        $products1 = get_latest_products(4);
                    @endphp
                    @foreach($products1 as $product)
                        @php
                            $variationData = \Botble\Ecommerce\Models\ProductVariation::join('ec_products as ep', 'ep.id', 'ec_product_variations.product_id')
                                                ->where('ep.quantity', '>', 0)
                                                ->where('ec_product_variations.configurable_product_id', $product->id)
                                                ->orderBy('ec_product_variations.is_default', 'desc')
                                                ->select('ec_product_variations.id','ec_product_variations.product_id', 'ep.price' )
                                                ->get();
                            $default = $variationData->first();

                        @endphp
                        <div class="listbox">
                            <a href="{!! generate_product_url('detail', $product->id, $product->product_slug)  !!}">
                                <div class="img">
                                    {!! image_html_generator(@$product->images[0], null, null, null, true, 'bone') !!}
                                    <span>Latest</span>
                                    <div class="imgoverlay">
                                        <a href="{!! generate_product_url('detail', $product->id, $product->product_slug)  !!}">
                                            <i class="far fa-eye"></i></a>
                                        @if(auth('customer')->user())
                                            <a class="add-to-wishlist" id="wishlist-icon-{{$product->id}}"
                                               href="{!! generate_product_url('wishlist', $product->id) !!}"
                                               data-id="{{$product->id}}"><i class="far fa-heart"></i></a>
                                            <form id='myform-{{$product->id}}' class="add_to_cart_form"
                                                  data-id="{{ @$default->product_id }}" method='POST'
                                                  action='{{ route('public.cart.add_to_cart') }}'>
                                                <div class="col-lg-4">
                                                    <input type='hidden' name='quantity' value='1' class='qty'/>
                                                </div>
                                                <a class="cart-submit" id="cart-icon-{{$product->id}}"
                                                   onclick="$('#myform-{{$product->id}}').trigger('submit');"
                                                   href="javascript:void(0);"><i class="far fa-shopping-bag"></i></a>
                                            </form>
                                        @else
                                            <a href="{{ route('customer.login') }}"><i class="far fa-heart"></i></a>
                                            <a href="{{ route('customer.login') }}"><i class="far fa-shopping-bag"></i></a>
                                        @endif
                                    </div>
                                </div>
                                <div class="caption">
                                    <h4 class="text-center">{{ $product->name }}</h4>
                                    <div class="price">
                                        ${{ $product->price/$product->prod_pieces }}
                                    </div>
                                    <form id='myform-{{$product->id}}' class="add_to_cart_form"
                                          data-id="{{ @$default->product_id }}" method='POST'
                                          action='{{ route('public.cart.add_to_cart') }}'>
                                        <div class="col-lg-4">
                                            <input type='hidden' name='quantity' value='1' class='qty'/>
                                        </div>
                                        <a style="padding: 12px 20px;"
                                           class="mt-2 w-auto addTobag product-tile__add-to-cart"
                                           id="cart-icon-{{$product->id}}"
                                           onclick="$('#myform-{{$product->id}}').trigger('submit');"
                                           href="javascript:void(0);">ADD TO BAG &nbsp;&nbsp;<i
                                                    class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                                    </form>{{--
                                    <button style="padding: 12px 20px;" class="mt-2 w-auto addTobag product-tile__add-to-cart"  >ADD TO BAG &nbsp;&nbsp;<i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                    </button>--}}
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>

@php
    /*$not_in = null;
    if(isset($products1) && count($products1)){
     $not_in = $products1->pluck('id')->toArray();
    }
    $products2 = get_latest_products(6, $not_in);*/
        $categories = \Botble\Ecommerce\Models\ProductCategory::with(['products' ])->withCount('products')
            ->orderBy('products_count', 'DESC')
            ->orderBy('order', 'ASC')
            ->limit(7)
            ->get();
@endphp

@if(count($categories) > 5 && setting('theme-landb-home_section_2_status') == 1)
    <div class="row">
        <div class="col-lg-12">
            <h4 class="text-center font-quiche mb-2"> BROWSE COLLECTION </h4>
        </div>
    </div>
    <div class="d-flex slider-t-main">

        <div class="t-one">
            <div class="ml-2 mr-2">
                    {!! image_html_generator(@$categories[0]->products[0]->images[0], null, null, null, true, 'w-100 slidert-left-img', 'vslider1') !!}
            </div>
        </div>
        <div class="t-two">
            <div class="ml-2 mr-2">
                    {!! image_html_generator(@$categories[0]->products[1]->images[1], null, null, null, true, 'w-100 slidert-slim-img', 'vslider2') !!}
                    {!! image_html_generator(@$categories[0]->products[2]->images[2], null, null, null, true, 'w-100 mt-3 slidert-slim-img', 'vslider3') !!}
            </div>
        </div>
        <div class="t-three">
            <div class="dp-scroll-wrapper">
                <div class="dp-scroll-text">
                    @foreach($categories as $category)
                        <p class="{!! (!$loop->first) ? 'dp-run-script  dp-animate-'.($loop->iteration-1) : '' !!}" data-products="{{ $category->products->pluck('images')->take(6) }}"> {{ $category->name }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="t-four">
            <div class="ml-2 mr-2">
                    {!! image_html_generator(@$categories[0]->products[3]->images[3], null, null, null, true, 'w-100 slidert-slim-img', 'vslider4') !!}
                    {!! image_html_generator(@$categories[0]->products[4]->images[4], null, null, null, true, 'w-100 mt-3 slidert-slim-img' , 'vslider5') !!}
            </div>
        </div>
        <div class="t-five">
            <div class="ml-2 mr-2">
                    {!! image_html_generator(@$categories[0]->products[5]->images[5], null, null, null, true, 'w-100 slidert-left-img', 'vslider6') !!}
            </div>
        </div>
    </div>
@endif
@if(setting('theme-landb-home_section_2_status') == 1)
    <section>
        <div class="ml-5 mr-5">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="revealUp hey-text text-center"
                        style="opacity: 1; visibility: inherit; transform: translate(0px, 0px);"> Hey Y'all! </h1>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">

                    <p class="mt-3 font-madeg ft-20">
                        We are Lucky & Blessed, a Texas-based western wholesale clothing vendor. We provide
                        fashion-forward, comfortable styles that give a nod to the American west. We are confident our
                        clothing, accessories, and home goods will satisfy the needs of fiercely independent, unique
                        customers of all ages, shapes, and sizes. We pride ourselves on collections that include luxe
                        denim, bold custom vintage prints with artistic lace, and rich, on-trend hues.
                        <br/>
                        Most importantly, we value the creative, exceptional retailers across the US and internationally
                        that put our brand in the hands of those customers. L&B is a western apparel wholesale
                        distributor that is passionate about developing authentic products that will help your business
                        stand out. We promise to provide excellent customer service and accountability. We are partners
                        in your success.

                    </p>
                </div>
                <div class="col-md-6">
                <!-- {!! image_html_generator(@json_decode(setting('theme-landb-home_section_2_image'))[0], null, null, null, true, 'w-100') !!} -->
                    <img class="w-100" src="{{ asset('landb/img/product.jpg') }}"/>
                </div>
            </div>
        </div>
    </section>
@endif
@if(setting('theme-landb-home_section_3_status') == 1)

    <section>
        <div class="ml-5 mr-5">
            <div class="row">
                <div class="col-md-6">

                    <div class="collection_img home-overlap">
                        <div class="collec-imgbox">
                            {!! image_html_generator(@json_decode(setting('theme-landb-home_section_3_images'))[0], null, null, null, true, 'ls-is-cached') !!}
                            <div class="imgcaption">
                                <a href="#" class="save"><i class="fas fa-save" aria-hidden="true"></i> Save</a>
                                <a href="#" class="search"><i class="fal fa-search" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="overlap">
                            <div class="collec-imgbox">
                                {!! image_html_generator(@json_decode(setting('theme-landb-home_section_3_images'))[1], null, null, null, true, 'imgtop ls-is-cached') !!}
                                <div class="imgcaption">
                                    <a href="#" class="save"><i class="fas fa-save" aria-hidden="true"></i> Save</a>
                                    <a href="#" class="search"><i class="fal fa-search" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h1 class="revealUp pro-text"
                        style="opacity: 1; visibility: inherit; transform: translate(0px, 0px);">Our Products </h1>
                    <p class="mt-3 font-madeg ft-20">
                        Our main offering is western wholesale clothing, of course, but we pride ourselves in providing
                        a creative, quality range of products to cover the western lifestyle. We have accessories,
                        handbags, and home products in addition to our clothing line, which features women’s denim,
                        dresses, outerwear, loungewear, and more in regular and plus sizes. We also offer an extensive
                        kid’s line of clothes.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endif
@if(setting('theme-landb-home_section_4_status') == 1)

    <div class="row">
        <div class="col-lg-12">
            <h1 class="mt-5 revealUp pro-text text-center"
                style="opacity: 1; visibility: inherit; transform: translate(0px, 0px);">Our Promise </h1>
            <img src="{{ asset('landb/img/pr4.png') }}" alt="Product image" loading="lazy"
                 class="mt-2 w-100 lazyloaded">
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10">
                    <p class="text-center mt-2 font-madeg ft-20">
                        L&B is passionate about developing creative, quality western wholesale boutique clothing that
                        will help your business stand out. We are a family-owned business, which extends to our team and
                        our customers. We promise to provide excellent customer service and accountability.<br/>
                        You will find that all our products are inclusive of all ages, shapes, and sizes. We believe in
                        celebrating diversity by providing our clients with clothing solutions that work for everyone.
                        We believe that every person who wears our garments should feel comfortable and stylish.
                    </p>
                </div>
                <div class="col-lg-1"></div>
            </div>

        </div>
    </div>
@endif
{{--<div class="logocenter text-center">
    <img class="revealUp"src="landb/img/lucky&blessed_logo_sign_Black 1.png" alt="">
</div>--}}
{{--@if(count($home_featured) && setting('theme-landb-home_main_section_status') == 1)
    {!! Theme::partial('index/featured', compact('home_featured')) !!}
@endif

@if(setting('theme-landb-home_section_1_status') == 1)
    {!! Theme::partial('index/latest_collection_1', compact('latest_collection')) !!}
@endif
@if(setting('theme-landb-home_section_2_status') == 1)
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
@endif
@if(setting('theme-landb-home_section_3_status') == 1)
    <section class="parallax_video">
    @php
        $video_url = setting('theme-landb-home_section_3_video', 'https://css-tricks-post-videos.s3.us-east-1.amazonaws.com/708209935.mp4');
    @endphp
    <video src="{{ (strpos($video_url, 'http') !== false) ? $video_url : 'storage/'.$video_url }}" autoplay loop playsinline muted></video>
    <!-- <img src="landb/img/Video.png" alt=""> -->
</section>
@endif
@if(setting('theme-landb-home_section_4_status') == 1)
    {!! Theme::partial('index/latest_collection_2', compact('latest_collection')) !!}
@endif
@if(count($latest_collection) && setting('theme-landb-home_section_5_status') == 1)
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
<!-- <div class="grid-container"> -->
    <!-- @php
        $product_ids = setting('theme-landb-home_section_5_products', json_encode(\Botble\Ecommerce\Models\Product::inRandomOrder()->limit(9)->pluck('id')->all()));
       $product_ids = json_decode($product_ids);
        $products5 = \Botble\Ecommerce\Models\Product::whereIn('id', $product_ids)->get();
    @endphp -->
      <!-- <div class="grid">
      @foreach($products5 as $product)
        <div class="gridLayer @if($loop->iteration == 4) centerPiece @endif">
          <div class="gridBlock" style="background-image: url('{{ !empty(@$product->images[0]) ? asset('storage/'.@$product->images[0]):asset('images/default.jpg') }}');">
              <a href="{!!  generate_product_url('detail', $product->id, $product->product_slug)  !!}"></a>
          </div>
        </div>
      @endforeach -->
        --}}{{--<div class="gridLayer">
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
        </div>--}}{{--
      <!-- </div> -->
    <!-- </div> -->
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
</section>--}}

<script>
    $(document).ready(function () {
        $('.carousel').carousel({
            interval: 2000,
            cycle: true,
            pause: "null"
        })
    });
</script>
