@php

    //dd($productVariations);
    $default = 0;
    $default_max = 1;
    $sale_price = null;
    $default_price = $product->final_price;
    $fixed_price = null;
    foreach ($productVariations as $variation){
        if($variation->is_default == 1){
            $default = $variation->product_id;
            $default_max = $variation->product->quantity;
            $default_price = $variation->product->final_price;
            $fixed_price = $variation->product->price;
            $sale_price =  $variation->product->sale_price;
        }
    }
    if($default == 0 && count($productVariations)){
        $variation = $productVariations->first();

        $default = $variation->product_id;
        $default_max = $variation->product->quantity;
        $default_price = $variation->product->final_price;
        $fixed_price = $variation->product->price;
        $sale_price =  $variation->product->sale_price;
    }
    //dd($default);
//dd($productVariations);
    $productVariationsInfo = app(\Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface::class)
          ->getVariationsInfo($productVariations->pluck('id')->toArray());

      $check = \Botble\Slug\Models\Slug::where('slugs.key', 'pre-order')->where('slugs.prefix', 'product-tags')
                    ->join('ec_product_tag_product as eptp', 'eptp.tag_id','slugs.reference_id')
                    ->where('eptp.product_id', $product->id)->first();

        if($check){
            $pre_order = true;
        }else{
            $pre_order = false;
        }

//dd($productVariationsInfo, $productVariations);
@endphp

<section class="breadcrumb_wrap">
    <div class="pl-3 pr-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Shop</li>
                <li class="breadcrumb-item active" aria-current="page">{{ @$product->categories[0]->name }}</li>
                {{--<li class="breadcrumb-item active" aria-current="page">Plus Top</li>--}}
                <li class="breadcrumb-item active" aria-current="page"><b>{{ @$product->name }}</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-2 mbtb-pr-2">
    <div class="row">
        <div class="col-lg-6">
        <div class="content-carousel product-carousel">
        <div class="owl-carousel " >
            @if(count($product->images))
                    @foreach($product->images as $image)
                    <div class="image-set">
                        <a data-magnify="gallery" href="{{ asset('storage/'.$image) }}">
                        <img class="" src="{{ asset('storage/'.$image) }}" />
                        </a> 
                        <!-- {!! image_html_generator($image, $product->name, null, null, true, 'product-zoomer', '') !!} -->
                    </div>
                    @endforeach
                @else
                <div><img src="{{ asset('images/default.jpg') }}"/></div>
            @endif
        </div>
        </div>

            <!-- <div class="exzoom hidden mr-2" id="exzoom">
            <div class="exzoom_btn d-flex mt-3">
            <div class="exzoom_nav"></div>
            </div>
                <div class="exzoom_img_box">
                    <ul class='exzoom_img_ul'>
                        @if(count($product->images))
                            @foreach($product->images as $image)
                                <li>{!! image_html_generator($image, $product->name, null, null, true) !!}</li>
                            @endforeach
                        @else
                            <li><img src="{{ asset('images/default.jpg') }}"/></li>
                    @endif
                    </ul>
                </div>
            </div>  -->
        </div>

        <div class="col-lg-6">
{{--        <p class="pre-label-detail">Pre-Order</p>--}}
        <h1 class="detail-h1 mb-2"> {{ $product->name }}</h1>
                <p class="detail-price mb-2"><span id="product_price">
                    @if(!empty($sale_price))
                        <del>${{ format_price($fixed_price / $product->prod_pieces)  }} </del>
                        $ {{ format_price($sale_price/ $product->prod_pieces) }}
                    @endif
                        <small>(${{$default_price}} pack price)</small></span>
                </p>
            <p class="short-description mb-2">{!! $product->description !!} </p>
            <div class="row mt-3">
                <div class="col-md-6">
                    <p class="detail-size-p mb-2"><span
                            class="detail-size">Size:</span>
                        {{--                @foreach($productVariations as $variation)--}}
                        {{--                    @foreach ($productVariationsInfo->where('variation_id', $variation->id)->where('attribute_set_id', 2) as $key => $item)--}}
                        {{--                        {{  @explode('-', $item->title)[0] }} ,--}}
                        {{--                    @endforeach--}}
                        {{--                @endforeach--}}
                        {{$product->sizes}}
                    </p>
                    <p class="detail-size-p mb-2"><span
                            class="detail-size">Pieces In Pack:</span>
                        {{$product->prod_pieces}}
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="detail-size-p mb-2 font-bold">
                        <a style="text-decoration:none !important" href="#" class="size-chart-a" data-toggle="modal"
                           data-target="#myModal"><i class="fa fa-bar-chart" aria-hidden="true"></i> &nbsp;
                            Size Chart</a>
                    </p>
                </div>
            </div>


            {{-- <select class="detail-size-select" id="variation-select">
                 --}}{{--@if(isset($product->category))
                     @foreach($product->category->category_sizes as $cat_size)
                         <option value="{{ $cat_size->id }}"> {{ $cat_size->name }} </option>
                     @endforeach
                 @endif--}}{{--
                 @foreach($productVariations as $variation)
                     @foreach ($productVariationsInfo->where('variation_id', $variation->id)->where('attribute_set_id', 2) as $key => $item)
                         <option value="{{ json_encode($variation) }}" @if($variation->is_default == 1) selected @endif>{{  @explode('-', $item->title)[0] }}</option>
                     @endforeach
                 @endforeach
             </select>--}}

            <p class="mt-4 detail-color-text"> Color &nbsp;&nbsp;&nbsp;
            </p>
            <div class="color-area mt-2">
                @foreach($product->product_colors() as $color)
                    <label class="">
                        <a href="{!! generate_product_url('detail', $color->id, $color->product_slug) !!}">
                            {{--<img src="{{ URL::to('storage/'.$color->color_print) }}" height="40" width="40">--}}
                            {!! image_html_generator($color->color_print, $color->name, 40, 40) !!}
                        </a>
                    </label>
                @endforeach
                {{--<label class="container-check">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                </label>
                <label class="container-check">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                </label>
                <label class="container-check">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                </label>
                <label class="container-check">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                </label>--}}
            </div>
            <form class="add_to_cart_form" id="variation-form" data-id="{{ $default }}" method='POST'
                  action='{{ route('public.cart.add_to_cart') }}'>
                <div class="row m-0 mt-4">
                    <div id="myform" class="col-lg-4">
                        <input type='button' value='-' class='qtyminus' data-update="0" field='quantity'/>
                        <input id="variation-quantity" type='text' name='quantity' value='1' min="1"
                               max="{{ (!$pre_order)? $default_max : '' }}" class='qty' readonly/>
                        <input type='button' value='+' class='qtyplus' data-update="0" field='quantity'/>

                    </div>
                    <div class="col-lg-4">
                        <button class="cart-btn w-100 add-to-cart-button cart-submit" id="variation-submit"
                                data-id="{{ $default }}">Add
                            to cart
                        </button>
                    </div>
                </div>
            </form>
            @if(!$pre_order)
                <p class=""><small><strong class="text-danger"><span id="varition_notice">{{ $default_max }}</span>
                            packs in stock!</strong></small></p>
            @endif
            <p class="mt-4 detail-basic">Basic Code: &nbsp;&nbsp;&nbsp;<span
                    class="detail-basic-p">{{ $product->sku }}</span></p>
            <p class="detail-category mt-2">Category: &nbsp;&nbsp;&nbsp;<span
                    class="detail-category-p mt-2">{{ @$product->category->name }}</span></p>
        </div>

    <!-- <div class="col-lg-1">
            @if(count($product->images))
        @foreach($product->images as $image)
            {!! image_html_generator($image, $product->name, null, null, true, 'mt-2 side-img') !!}
        @endforeach
    @else
        <img class="mt-2 side-img" src="{{ asset('images/default.jpg') }}">
            @endif
        </div>
        <div class="col-lg-5 mt-2">
            @if(count($product->images))
        @foreach($product->images as $image)
            {!! image_html_generator($image, $product->name, null, null, true, 'front-img') !!}
        @endforeach
    @else
        <img class="front-img" src="{{ asset('images/default.jpg') }}">
            @endif
        </div> -->
    <!-- <div class="col-lg-6">
            <h1 class="detail-h1 mb-2"> {{ $product->name }}</h1>
            <p class="detail-price mb-2">$ <span id="product_price">{{ $product->price }}</span></p>
            <p class="short-description mb-2">{!! $product->description !!} </p>
            <div class="row">
            <div class="col-md-6">
            <p class="detail-size-p mb-2"><span
                    class="detail-size">Size</span>
                @foreach($productVariations as $variation)
        @foreach ($productVariationsInfo->where('variation_id', $variation->id)->where('attribute_set_id', 2) as $key => $item)
            {{ $item->title }}{{ (!$loop->last) ? ' ,' : '' }}
        @endforeach
    @endforeach
        </p>
        </div>
        <div class="col-md-6">
        <p class="detail-size-p mb-2"><a href="#" class="size-chart-a" data-toggle="modal" data-target="#myModal">Size Chart</a></p>
        </div>
        </div>


        <select class="detail-size-select" id="variation-select">
{{--@if(isset($product->category))
                    @foreach($product->category->category_sizes as $cat_size)
                        <option value="{{ $cat_size->id }}"> {{ $cat_size->name }} </option>
                    @endforeach
                @endif--}}
    @foreach($productVariations as $variation)
        @foreach ($productVariationsInfo->where('variation_id', $variation->id)->where('attribute_set_id', 2) as $key => $item)
            <option value="{{ json_encode($variation) }}" @if($variation->is_default == 1) selected @endif>{{ $item->title }}</option>
                    @endforeach
    @endforeach
        </select> -->

    {{-- <p class="mt-4 detail-color-text"> Color &nbsp;&nbsp;&nbsp;<span class="detail-color-text-p">Peach</span>
     </p>
     <div class="color-area mt-2">
         <label class="container-check">
             <input type="checkbox" checked="checked">
             <span class="checkmark"></span>
         </label>
         <label class="container-check">
             <input type="checkbox">
             <span class="checkmark"></span>
         </label>
         <label class="container-check">
             <input type="checkbox">
             <span class="checkmark"></span>
         </label>
         <label class="container-check">
             <input type="checkbox">
             <span class="checkmark"></span>
         </label>
         <label class="container-check">
             <input type="checkbox">
             <span class="checkmark"></span>
         </label>
     </div>--}}
    <!-- <form class="add_to_cart_form" id="variation-form" data-id="{{ $default }}" method='POST'
                  action='{{ route('public.cart.add_to_cart') }}'>
                <div class="row m-0 mt-4">
                    <div id="myform" class="col-lg-4">
                        <input type='button' value='-' class='qtyminus' data-update="0" field='quantity'/>
                        <input id="variation-quantity" type='text' name='quantity' value='1' min="1" max="{{ $default_max }}" class='qty'  readonly/>
                        <input type='button' value='+' class='qtyplus' data-update="0" field='quantity'/>
                    </div>
                    <div class="col-lg-4">
                        <button class="cart-btn w-100 add-to-cart-button cart-submit" id="variation-submit" data-id="{{ $default }}">Add
                            to cart
                        </button>
                    </div>
                </div>
            </form>
            <p class="mt-4 detail-basic">Basic Code &nbsp;&nbsp;&nbsp;<span
                    class="detail-basic-p">{{ $product->sku }}</span></p>
            <p class="detail-category mt-2">Category: &nbsp;&nbsp;&nbsp;<span
                    class="detail-category-p mt-2">{{ @$product->category->name }}</span></p> -->
    {{--<p class="detail-tag mt-2">Tag:&nbsp;&nbsp;&nbsp;<span class="detail-tag-p">Pottery</span> </p>--}}
    <!-- <div class="d-flex mt-4">
                <p class="share-text pt-1 mr-2"> Share this items :
                </p>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/snapchat.png') }}"/></a>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/facebook.png') }}"/></a>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/Twitter.png') }}"/></a>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/instagram.png') }}"/></a>
            </div>
        </div>-->
    </div>
    <div class="row">
        <div class="col-lg-12 mt-4">
            <ul class="nav nav-tabs tabs-product mt-4">
                <li><a class="active" data-toggle="tab" href="#home">Description&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                </li>
                {{--<li><a data-toggle="tab" href="#menu1">Product Details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                </li>
                <li><a data-toggle="tab" href="#menu2">Reviews</a></li>--}}
            </ul>

            <div class="tab-content product-tab-content">
                <div id="home" class="tab-pane fade in active show">
                    {!! $product->description !!}
                </div>
                {{--<div id="menu1" class="tab-pane fade">
                    <div class="row mt-4">
                        <div class="col-lg-2 col-6">
                            <p class="detail-head">Weight</p>
                        </div>
                        <div class="col-lg-8 col-6">
                            <p class="detail-text">400 g</p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-2 col-6">
                            <p class="detail-head">Dimensions</p>
                        </div>
                        <div class="col-lg-8 col-6">
                            <p class="detail-text">10 * 10 * 15 cm</p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-2 col-6">
                            <p class="detail-head">Materials</p>
                        </div>
                        <div class="col-lg-8 col-6">
                            <p class="detail-text">60% cotton, 40% polyester</p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-2 col-6">
                            <p class="detail-head">Other Info</p>
                        </div>
                        <div class="col-lg-8 col-6">
                            <p class="detail-text">American heirloom jean shorts pugs seitan letterpress</p>
                        </div>
                    </div>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <div class="row mt-4">
                        <div class="col-lg-1">
                            <img class="review-img" src="./img/review.png"/>
                        </div>
                        <div class="col-lg-11">
                            <div class="row">
                                <div class="col-lg-6 col-6 mb-2">
                                    <h4 class="review-name">John Smith</h4>
                                </div>
                                <div class="col-lg-6 col-6 text-right mb-2">
                                    <a href="#" class="review-reply">Reply</a>
                                </div>
                            </div>
                            <p class="review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est viverra
                                velit enim, semper nibh lacus, hendrerit donec. Tellus eget urna amet, cum scelerisque.
                                Nibh arcu orci consequat diam. Libero fermentum scelerisque nam
                                amet. In egestas in quisque vestibulum, eu massa adipiscing. Eget est non ut ornare
                                blandit. Ut cras nunc suspendisse leo sed pharetra.</p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-1">
                            <img class="review-img" src="./img/review.png"/>
                        </div>
                        <div class="col-lg-11">
                            <div class="row">
                                <div class="col-lg-6 col-6 mb-2">
                                    <h4 class="review-name">John Smith</h4>
                                </div>
                                <div class="col-lg-6 col-6 text-right mb-2">
                                    <a href="#" class="review-reply">Reply</a>
                                </div>
                            </div>
                            <p class="review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est viverra
                                velit enim, semper nibh lacus, hendrerit donec. Tellus eget urna amet, cum scelerisque.
                                Nibh arcu orci consequat diam. Libero fermentum scelerisque nam
                                amet. In egestas in quisque vestibulum, eu massa adipiscing. Eget est non ut ornare
                                blandit. Ut cras nunc suspendisse leo sed pharetra.</p>
                        </div>
                    </div>
                </div>--}}
            </div>
        </div>
    </div>
    {!! Theme::partial('related-products', ['product' => $product]) !!}
</section>

<!-- Modal Size Chart Kids -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <button style="position: absolute; right: 10px;font-size: 38px; font-weight: 200; top: -5px;" type="button"
                    class="close" data-dismiss="modal">&times;
            </button>
            <div class="modal-body size-chart mt-4">
                <h3 class="text-center mb-3">SIZE CHART FOR KIDS</h3>
                <div class="table-responsive">
                <table>
                    <tbody>
                    <tr tabindex="0">
                        <td class="text-left pl-2">SIZE CHART FOR KIDS</td>
                        <td>(4/5) XS</td>
                        <td>(6/7) S</td>
                        <td>(8/9) M</td>
                        <td>(10/11) L</td>
                        <td>(12/14) XL</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">BACK</td>
                        <td>10 5/8</td>
                        <td>11 1/4</td>
                        <td>11 7/8</td>
                        <td>12 1/2</td>
                        <td>13 1/8</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">BUST</td>
                        <td>13 1/2</td>
                        <td>14 1/2</td>
                        <td>15 1/2</td>
                        <td>16 1/2</td>
                        <td>17 1/2</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">WAIST</td>
                        <td>19 20 1/2</td>
                        <td>22</td>
                        <td>23 1/2</td>
                        <td>25</td>
                        <td>26 1/2 28</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">WAIST (AVERAGE)</td>
                        <td>19 1/4</td>
                        <td>22</td>
                        <td>23 1/2</td>
                        <td>25</td>
                        <td>27 1/4</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">TOP LENGTH</td>
                        <td>17 1/4</td>
                        <td>18 1/4</td>
                        <td>19 1/4</td>
                        <td>20 1/4</td>
                        <td>21 1/4</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">SLEEVE LENGTH</td>
                        <td>6 1/4</td>
                        <td>6 1/2</td>
                        <td>6 3/4</td>
                        <td>7</td>
                        <td>7 1/4</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">SLEEVE WRIST OPENING</td>
                        <td>5 1/2</td>
                        <td>5 3/4</td>
                        <td>6</td>
                        <td>6 1/4</td>
                        <td>6 1/2</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">SHOULDERS</td>
                        <td>2 5/8</td>
                        <td>2 3/4</td>
                        <td>2 7/8</td>
                        <td>3</td>
                        <td>3 1/8</td>
                    </tr>

                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Size Chart Women Tops -->
<div class="modal fade" id="sizeWomenTopsModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <button style="position: absolute; right: 10px;font-size: 38px; font-weight: 200; top: -5px;" type="button"
                    class="close" data-dismiss="modal">&times;
            </button>
            <div class="modal-body size-chart mt-4">
                <h3 class="text-center mb-3">SIZE CHART FOR WOMEM'S TOPS</h3>
                <div class="table-responsive">
                <table>
                    <tbody>
                    <tr tabindex="0">
                        <td class="text-left pl-2">SIZE</td>
                        <td>S</td>
                        <td>M</td>
                        <td>L</td>
                        <td>XL</td>
                        <td>2XL</td>
                        <td>3XL</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">BUST</td>
                        <td>8 1/2</td>
                        <td>9</td>
                        <td>9 1/2</td>
                        <td>9 3/4</td>
                        <td>10</td>
                        <td>10 1/4</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">WAIST</td>
                        <td>7 1/2</td>
                        <td>8</td>
                        <td>8 1/2</td>
                        <td>9</td>
                        <td>9 1/2</td>
                        <td>10</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">HIPS (STRAIGHT)</td>
                        <td>9</td>
                        <td>9 1/2</td>
                        <td>10</td>
                        <td>10 1/2</td>
                        <td>11</td>
                        <td>11 1/2</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">LENGTH (CAMI)</td>
                        <td>24 3/4</td>
                        <td>25 1/2</td>
                        <td>26 1/4</td>
                        <td>27</td>
                        <td>27 3/4</td>
                        <td>28 1/2</td>
                    </tr>

                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- Modal Size Chart Women -->
<div class="modal fade" id="sizeWomenModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <button style="position: absolute; right: 10px;font-size: 38px; font-weight: 200; top: -5px;" type="button"
                    class="close" data-dismiss="modal">&times;
            </button>
            <div class="modal-body size-chart mt-4">
                <h3 class="text-center mb-3">SIZE CHART FOR WOMEM'S</h3>
                <div class="table-responsive">
                <table>
                    <tbody>
                    <tr tabindex="0">
                        <td class="text-left pl-2">SIZE</td>
                        <td>WAIST SIZE</td>
                        <td>HIP</td>
                        <td>THIGH</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">2</td>
                        <td>27 - 27 1/2"</td>
                        <td>31 - 32"</td>
                        <td>18 - 19"</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">4</td>
                        <td>28 - 28 1/2"</td>
                        <td>32 - 33"</td>
                        <td>19 - 20"</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">6</td>
                        <td>29 - 29 1/2"</td>
                        <td>33 - 34"</td>
                        <td>20 - 21"</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">8</td>
                        <td>30 30 1/2"</td>
                        <td>34 - 35"</td>
                        <td>20 1/2 - 21 1/2"</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">10</td>
                        <td>31 - 31 1/2"</td>
                        <td>35 - 36"</td>
                        <td>21 1/2 - 22 1/2"</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">12</td>
                        <td>32 - 32 1/2"</td>
                        <td>36 - 37"</td>
                        <td>21 1/2 - 22 1/2"</td>
                    </tr>


                    <tr tabindex="0">
                        <td class="text-left pl-2">14</td>
                        <td>34 - 34 1/2"</td>
                        <td>37 - 38"</td>
                        <td>22 1/2 - 23 1/2"</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">16</td>
                        <td>36 - 36 1/2"</td>
                        <td>38 - 39"</td>
                        <td>23 1/2 - 24 1/2"</td>
                    </tr>
                    <tr tabindex="0">
                        <td class="text-left pl-2">18</td>
                        <td>38 - 38 1/2"</td>
                        <td>40 - 41"</td>
                        <td>24 1/2 - 25 1/2"</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">20</td>
                        <td>40 - 40 1/2"</td>
                        <td>42 - 43"</td>
                        <td>25 - 26"</td>
                    </tr>

                    <tr tabindex="0">
                        <td class="text-left pl-2">22</td>
                        <td>42 - 42 1/2"</td>
                        <td>44 - 45"</td>
                        <td>26 1/2 - 27 1/2"</td>
                    </tr>

                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
  </div>



