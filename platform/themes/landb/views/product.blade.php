@php

    //dd($productVariations);
    $default = 0;
    $default_max = 1;
    $default_price = $product->price;
    foreach ($productVariations as $variation){
        if($variation->is_default == 1){
            $default = $variation->product_id;
            $default_max = $variation->product->quantity;
            $default_price = $variation->product->price;
        }
    }
    if($default == 0 && count($productVariations)){
        $variation = $productVariations->first();
        $default = $variation->product_id;
        $default_max = $variation->product->quantity;
        $default_price = $variation->product->price;
    }
    //dd($default);
//dd($productVariations);
    $productVariationsInfo = app(\Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface::class)
          ->getVariationsInfo($productVariations->pluck('id')->toArray());


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
                <li class="breadcrumb-item active" aria-current="page"><b>{{ $product->name }}</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-2 mbtb-pr-2">
    <div class="row">
        <div class="col-lg-6">
                <div class="fancy-container clearfix">
                    <div class="gallery">
                        <div class="previews">
                        @if(count($product->images))
                            @foreach($product->images as $image)
                                <a href="javascript:void(0)" data-full="{{ asset('storage/'.$image) }}">
                                {!! image_html_generator($image, $product->name, null, null, true, ' side-img') !!}
                                </a>
                            @endforeach
                        @else
                        <a href="javascript:void(0)" class="selected" data-full="{{ asset('images/default.jpg') }}"><img src="{{ asset('images/default.jpg') }}" /></a>
                            <!-- <img class="mt-2 side-img" src="{{ asset('images/default.jpg') }}"> -->
                        @endif
                            <!-- <a href="javascript:void(0)" class="selected" data-full="img/product/top1large.jpg"><img src="img/product/top1small.jpg" /></a>
                            <a href="javascript:void(0)" data-full="img/product/top2large.jpg"><img src="img/product/top2small.jpg" /></a>
                            <a href="javascript:void(0)" data-full="img/product/top3large.jpg"><img src="img/product/top3small.jpg" /></a>
                            <a href="javascript:void(0)" data-full="img/product/top4large.jpg"><img src="img/product/top4small.jpg" /></a>
                            <a href="javascript:void(0)" data-full="img/product/top5large.jpg"><img src="img/product/top5small.jpg" /></a> -->
                        </div>
                        <div class="full text-center">
                        @if(count($product->images))
                        <a class="demo-trigger" href={{asset('storage/'.$product->images[0])}}>
                        {!! image_html_generator($product->images[0], $product->name, null, null, true, 'front-img') !!}
                        </a>

                        @else
                            <img src="{{ asset('images/default.jpg') }}">
                        @endif
                            <!-- first image is viewable to start -->
                        </div>
                </div>
                </div>

        </div>
        <div class="col-lg-6">
        <div class="detail-magnify"></div>
        <h1 class="detail-h1 mb-2"> {{ $product->name }}</h1>
            <p class="detail-price mb-2">$ <span id="product_price">{{ $default_price }}</span></p>
            <p class="short-description mb-2">{!! $product->description !!} </p>
            <div class="row">
            <div class="col-md-6">
            <p class="detail-size-p mb-2"><span
                    class="detail-size">Size</span>
                @foreach($productVariations as $variation)
                    @foreach ($productVariationsInfo->where('variation_id', $variation->id)->where('attribute_set_id', 2) as $key => $item)
                        {{  @explode('-', $item->title)[0] }} ,
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
                        <option value="{{ json_encode($variation) }}" @if($variation->is_default == 1) selected @endif>{{  @explode('-', $item->title)[0] }}</option>
                    @endforeach
                @endforeach
            </select>

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
            <form class="add_to_cart_form" id="variation-form" data-id="{{ $default }}" method='POST'
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
            <p class=""><small><strong class="text-danger"><span id="varition_notice">{{ $default_max }}</span> product(s) in stock!</strong></small></p>
            <p class="mt-4 detail-basic">Basic Code &nbsp;&nbsp;&nbsp;<span
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
                <li><a class="active" data-toggle="tab" href="#home">Description&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                </li>
                <li><a data-toggle="tab" href="#menu1">Product Details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                </li>
                <li><a data-toggle="tab" href="#menu2">Reviews</a></li>
            </ul>

            <div class="tab-content product-tab-content">
                <div id="home" class="tab-pane fade in active show">
                    {!! $product->description !!}
                </div>
                <div id="menu1" class="tab-pane fade">
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
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-4">
            <h1 class="detail-subheading mt-4">You Might Also Like</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-4">
            <div class="shoplisting detail-shoplist">
                <div class="listbox">
                    <div class="img">
                        <img src="img/listing/listimg1.png" alt="">
                        <span>Restock</span>
                        <div class="imgoverlay">
                            <a href="#"><i class="far fa-eye"></i></a>
                            <a href="#"><i class="far fa-heart"></i></a>
                            <a href="#"><i class="far fa-shopping-bag"></i></a>
                        </div>
                    </div>
                    <div class="caption">
                        <h4>Fuchsia Serape Stripe Strapless Faux Leather Romper</h4>
                        <div class="price">
                            $10.00
                        </div>
                    </div>
                </div>
                <div class="listbox">
                    <div class="img">
                        <img src="img/listing/listimg2.png" alt="">
                        <span>Restock</span>
                        <div class="imgoverlay">
                            <a href="#"><i class="far fa-eye"></i></a>
                            <a href="#"><i class="far fa-heart"></i></a>
                            <a href="#"><i class="far fa-shopping-bag"></i></a>
                        </div>
                    </div>
                    <div class="caption">
                        <h4>Fuchsia Serape Stripe Strapless Faux Leather Romper</h4>
                        <div class="price">
                            $10.00
                        </div>
                    </div>
                </div>
                <div class="listbox">
                    <div class="img">
                        <img src="img/listing/listimg3.png" alt="">
                        <span>Restock</span>
                        <div class="imgoverlay">
                            <a href="#"><i class="far fa-eye"></i></a>
                            <a href="#"><i class="far fa-heart"></i></a>
                            <a href="#"><i class="far fa-shopping-bag"></i></a>
                        </div>
                    </div>
                    <div class="caption">
                        <h4>Fuchsia Serape Stripe Strapless Faux Leather Romper</h4>
                        <div class="price">
                            $10.00
                        </div>
                    </div>
                </div>
                <div class="listbox">
                    <div class="img">
                        <img src="img/listing/listimg4.png" alt="">
                        <span>Restock</span>
                        <div class="imgoverlay">
                            <a href="#"><i class="far fa-eye"></i></a>
                            <a href="#"><i class="far fa-heart"></i></a>
                            <a href="#"><i class="far fa-shopping-bag"></i></a>
                        </div>
                    </div>
                    <div class="caption">
                        <h4>Fuchsia Serape Stripe Strapless Faux Leather Romper</h4>
                        <div class="price">
                            $10.00
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
      <button style="position: absolute; right: 10px;font-size: 38px; font-weight: 200; top: -5px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <div class="modal-body size-chart mt-4">
        <table>
<tbody>
<tr tabindex="0">
<td>SIZE</td>
<td>XS</td>
<td>S</td>
<td>M</td>
<td>L</td>
<td>XL</td>
</tr>
<tr tabindex="0">
<td>US/CAN</td>
<td>1</td>
<td>3, 5</td>
<td>7, 9</td>
<td>11, 13</td>
<td>15</td>
</tr>
<tr tabindex="0">
<td>Bust (in)</td>
<td>31-33</td>
<td>33-35</td>
<td>35-37</td>
<td>37-39</td>
<td>39-41</td>
</tr>
<tr tabindex="0">
<td>Waist (in)</td>
<td>24-25</td>
<td>26-27</td>
<td>28-29</td>
<td>30-31</td>
<td>32</td>
</tr>
<tr tabindex="0">
<td>Hips (in)</td>
<td>33-34</td>
<td>35-36</td>
<td>37-38</td>
<td>39-40</td>
<td>41</td>
</tr>
<tr tabindex="0">
<td>UK</td>
<td>2, 4</td>
<td>6, 8</td>
<td>10, 12</td>
<td>14, 16</td>
<td>18</td>
</tr>
<tr tabindex="0">
<td>EU</td>
<td>32, 34</td>
<td>36, 38</td>
<td>40, 42</td>
<td>44, 46</td>
<td>48</td>
</tr>
<tr tabindex="0">
<td>AUS</td>
<td>2, 4</td>
<td>6, 8</td>
<td>10, 12</td>
<td>14, 16</td>
<td>18</td>
</tr>
<tr tabindex="0">
<td>Bust (cm)</td>
<td>78-83</td>
<td>83-89</td>
<td>89-94</td>
<td>94-99</td>
<td>99-104</td>
</tr>
<tr tabindex="0">
<td>Waist (cm)</td>
<td>60-65</td>
<td>65-70</td>
<td>70-75</td>
<td>75-80</td>
<td>80-85</td>
</tr>
<tr tabindex="0">
<td>Hips (cm)</td>
<td>83-88</td>
<td>88-93</td>
<td>93-98</td>
<td>98-103</td>
<td>103-108</td>
</tr>
</tbody>
</table>
        </div>
      </div>

    </div>
  </div>


