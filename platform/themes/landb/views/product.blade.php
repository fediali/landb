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
<section class="shoplisting_wrap">
    <div class="row">
        <div class="col-lg-1">
            @foreach($product->images as $image)
                {!! image_html_generator($image, $product->name, null, null, true, 'mt-2 side-img') !!}
            @endforeach
        </div>
        <div class="col-lg-5 mt-2">
            @foreach($product->images as $image)
                {!! image_html_generator($image, $product->name, null, null, true, 'front-img') !!}
            @endforeach
        </div>
        <div class="col-lg-6">
            <h1 class="detail-h1 mb-2"> {{ $product->name }}</h1>
            <p class="detail-price mb-2">$ {{ $product->price }}</p>
            <p class="short-description mb-2">{!! $product->description !!} </p>
            <p class="detail-size-p mb-2"><span class="detail-size">Size</span>@foreach($product->category->category_sizes as $cat_size) {{ $cat_size->name }} {!! ($loop->last) ? '':',' !!} @endforeach </p>
            <select class="detail-size-select">
                @foreach($product->category->category_sizes as $cat_size)
                    <option value="{{ $cat_size->id }}"> {{ $cat_size->name }} </option>
                @endforeach
            </select>
            <p class="mt-4 detail-color-text"> Color &nbsp;&nbsp;&nbsp;<span class="detail-color-text-p">Peach</span> </p>
            <div class="color-area mt-2">
                <label class="container-check">
                    <input type="checkbox" checked="checked">
                    <span class="checkmark"></span>
                </label>
                <label class="container-check">
                    <input type="checkbox" >
                    <span class="checkmark"></span>
                </label>
                <label class="container-check">
                    <input type="checkbox" >
                    <span class="checkmark"></span>
                </label>
                <label class="container-check">
                    <input type="checkbox" >
                    <span class="checkmark"></span>
                </label>
                <label class="container-check">
                    <input type="checkbox" >
                    <span class="checkmark"></span>
                </label>
            </div>
                <form class="add_to_cart_form" data-id="{{ $product->id }}" method='POST' action='{{ route('public.cart.add_to_cart') }}'>
               <div class="row mt-4"> 
                <div id="myform" class="col-lg-4">
                    <input type='button' value='-' class='qtyminus' data-update="0" field='quantity' />
                    <input type='text' name='quantity' value='1' class='qty' />
                    <input type='button' value='+' class='qtyplus' data-update="0" field='quantity' />
                </div> 
                <div class="col-lg-4">
                    <button class="cart-btn w-100 add-to-cart-button" data-id="{{ $product->id }}">Add to cart</button>
                </div>
               </div>
                </form>
            <p class="mt-4 detail-basic">Basic Code &nbsp;&nbsp;&nbsp;<span class="detail-basic-p">{{ $product->sku }}</span> </p>
            <p class="detail-category mt-2">Category: &nbsp;&nbsp;&nbsp;<span class="detail-category-p mt-2">{{ @$product->category->name }}</span> </p>
            {{--<p class="detail-tag mt-2">Tag:&nbsp;&nbsp;&nbsp;<span class="detail-tag-p">Pottery</span> </p>--}}
            <div class="d-flex mt-4">
                <p class="share-text pt-1 mr-2"> Share this items :
                </p>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/snapchat.png') }}" /></a>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/facebook.png') }}" /></a>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/Twitter.png') }}" /></a>
                <a href="#"><img class="social-img ml-2" src="{{ asset('landb/img/icons/instagram.png') }}" /></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-4">
            <ul class="nav nav-tabs tabs-product mt-4">
                <li><a class="active" data-toggle="tab" href="#home">Description&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li><a data-toggle="tab" href="#menu1">Product Details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
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
                            <img class="review-img" src="./img/review.png" />
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
                            <p class="review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est viverra velit enim, semper nibh lacus, hendrerit donec. Tellus eget urna amet, cum scelerisque. Nibh arcu orci consequat diam. Libero fermentum scelerisque nam
                                amet. In egestas in quisque vestibulum, eu massa adipiscing. Eget est non ut ornare blandit. Ut cras nunc suspendisse leo sed pharetra.</p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-1">
                            <img class="review-img" src="./img/review.png" />
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
                            <p class="review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est viverra velit enim, semper nibh lacus, hendrerit donec. Tellus eget urna amet, cum scelerisque. Nibh arcu orci consequat diam. Libero fermentum scelerisque nam
                                amet. In egestas in quisque vestibulum, eu massa adipiscing. Eget est non ut ornare blandit. Ut cras nunc suspendisse leo sed pharetra.</p>
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
            <div class="shoplisting">
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