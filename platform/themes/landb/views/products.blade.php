<section class="breadcrumb_wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Shop</li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap">
    <div class="container">
        <div class="filterbar d_flex">
            <ul class="leftbar">
                <li>Showing 1-12 of 21 results</li>
                <li class="seprator"></li>
                <li>
                    <div class="dropdown">
                        <button class="sortdropdown" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @switch(request()->query('sort_by'))
                                @case('name-asc') Name @break
                                @case('price-asc') Price: Low to High @break
                                @case('price-desc') Price: High to Low @break
                                @default Sort by @break

                            @endswitch
                             <i class="fal fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'name-asc']) }}">Name</a>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'price-asc']) }}">Price: Low to High</a>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'price-desc']) }}">Price: High to Low</a>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="rightbar">
                <li class="listingicon">
                    <a href="{{ request()->fullUrlWithQuery(['limit' => 3]) }}"><span class="threedots {{ (request()->query('limit') == 3) ? '':'active' }}"></span></a>
                </li>
                <li class="listingicon">
                    <a href="{{ request()->fullUrlWithQuery(['limit' => 4]) }}"><span class="fourdots {{ (request()->query('limit') == 4) ? '':'active' }}"></span></a>
                </li>
                <li class="listingicon">
                    <a href="{{ request()->fullUrlWithQuery(['limit' => 5]) }}"><span class="fivedots {{ (request()->query('limit') == 5) ? '':'active' }}"></span></a>
                </li>
                <li class="seprator"></li>
                <li class="filter"><a href="#" id="filtertoggle" class="filterbtn">Filter <span class="filtericon"></span></a>


                </li>
            </ul>
        </div>
        <div class="" id="filtermenu">
            <nav class="main-nav filter_nav">
                {{--<div class="nav-col">
                    <h5>Color</h5>
                    <ul class="colorbox">
                        <li>
                            <a class="black" href="#">
                                Black
                            </a>
                        </li>
                        <li>
                            <a class="green" href="#">
                                Green
                            </a>
                        </li>

                        <li>
                            <a class="gray" href="#">
                                Grey
                            </a>
                        </li>
                        <li>
                            <a class="red" href="#">
                                Red
                            </a>
                        </li>
                        <li>
                            <a class="white" href="#">
                                White
                            </a>
                        </li>
                        <li>
                            <a class="yellow" href="#">
                                Yellow
                            </a>
                        </li>
                    </ul>
                </div>--}}
                <div class="nav-col">
                    <h5>Size</h5>
                    <div class="d-flex">
                    @php $sizes = category_sizes() @endphp
                    @foreach($sizes->chunk(10) as $chunk)
                        <ul class="ml-4">
                            @foreach($chunk as $size)
                                @php
                                    $selected = (request()->query('size') == $size->id) ? true:false;
                                    if($selected){
                                        $url = request()->fullUrlWithQuery(['size' => null]);
                                    }else{
                                        $url = request()->fullUrlWithQuery(['size' => $size->id]);
                                    }
                                @endphp
                                <li>
                                    <a class="{{ ($selected) ? 'selected':''  }}" href="{{ $url }}">
                                        {{ $size->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                    </div>
                </div>
                <div class="nav-col">
                    <h5>Price</h5>
                    @php $price_ranges =[ ['0', '20'],['20', '40'],['40', '50'],['50', '60'],['60'] ];  @endphp
                    <ul>
                        @foreach($price_ranges as $price_range)
                            @php
                                $selected = (request()->query('price') == $price_range[0].'-'.@$price_range[1]) ? true:false;
                                if($selected){
                                    $url = request()->fullUrlWithQuery(['price' => null]);
                                }else{
                                    $url = request()->fullUrlWithQuery(['price' => $price_range[0].'-'.@$price_range[1]]);
                                }
                            @endphp
                            <li>
                                <a class="{{ ($selected) ? 'selected':'' }}" href="{{ $url }}">
                                    ${{ $price_range[0] }} {!! isset($price_range[1]) ? '- $'.$price_range[1] : '+' !!}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="nav-col">
                    <h5>Categories</h5>
                    <ul>
                        @php $categories = parent_categories() @endphp
                        @foreach($categories as $category)
                            @php
                                $selected = (request()->query('c_slug') == $category->key) ? true:false;
                                if($selected){
                                    $url = request()->fullUrlWithQuery(['c_slug' => null]);
                                }else{
                                    $url = request()->fullUrlWithQuery(['c_slug' => $category->key]);
                                }
                            @endphp
                        <li>
                            <a class="{{ ($selected) ? 'selected':''  }}" href="{{ $url }}">
                                {{ $category->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="nav-col">
                    <h5>Tags</h5>
                    <ul class="tags">
                        <li>
                            @php $tags_filter = product_tags() @endphp
                            @foreach($tags_filter as $tag)
                                @php
                                    $selected = (request()->query('t_slug') == $tag->key)? true:false;
                                    if($selected){
                                        $url = request()->fullUrlWithQuery(['t_slug' => null]);
                                    }else{
                                        $url = request()->fullUrlWithQuery(['t_slug' => $tag->key]);
                                    }
                                @endphp
                                <a class="{{ ($selected) ? 'selected':''  }}" href="{{ $url }}">
                                    {{ $tag->name }}{{ ($loop->last) ? '': ',' }}
                                </a>
                            @endforeach
                        </li>
                    </ul>
                </div>
            </nav>


        </div>
        <div class="shoplisting">
            @if(count($products))
            @foreach($products as $key => $product)
                <div class="listbox">
                <div class="img">
                    {!! image_html_generator(@$product->images[0]) !!}
                    <div class="caro_text">
                        <h5>{{ @$product->category->name }}</h5>
                        <p>{{ $product->name }}</p>
                    </div>
                    <span>Restock</span>
                    <div class="imgoverlay">
                        <a href="#"  data-toggle="modal" data-target="#myModal"><i class="far fa-eye"></i></a>
                        @if(auth('customer')->user())
                            <a class="add-to-wishlist" id="wishlist-icon-{{$product->id}}" href="{!! generate_product_url('wishlist', $product->id) !!}" data-id="{{$product->id}}"><i class="far fa-heart"></i></a>
                            <form id='myform-{{$product->id}}' class="add_to_cart_form" data-id="{{ $product->id }}" method='POST' action='{{ route('public.cart.add_to_cart') }}'>
                                <div class="col-lg-4">
                                    <input type='hidden' name='quantity' value='1' class='qty' />
                                </div>
                                <a class="cart-submit" id="cart-icon-{{$product->id}}" onclick="$('#myform-{{$product->id}}').trigger('submit');" href="javascript:void(0);"><i class="far fa-shopping-bag"></i></a>
                            </form>
                        @else
                            <a href="{{ route('customer.login') }}" ><i class="far fa-heart"></i></a>
                            <a href="{{ route('customer.login') }}"><i class="far fa-shopping-bag"></i></a>
                        @endif

                    </div>
                </div>
                <div class="caption">
                    <h4>{{ $product->name }}</h4>
                    <div class="price">
                        ${{ $product->price }}
                    </div>
                </div>
            </div>
            @endforeach
            @else
                <h3>No Matching Product Found!</h3>
            @endif
        </div>
        {!! $products->appends($_GET)->links() !!}
       {{-- <div class="pagination">

            <ul>
                <li>
                    <a href="#" class="prev"> <i class="fal fa-long-arrow-left"></i> Prev</a>
                </li>
                <li>
                    <a href="#" class="">1</a>
                </li>
                <li>
                    <a href="#" class="">2</a>
                </li>
                <li>
                    <a href="#" class="">3</a>
                </li>
                <li>
                    <a href="#" class="">4</a>
                </li>
                <li>
                    <a href="#" class="">5</a>
                </li>
                <li>
                    <a href="#" class="">6</a>
                </li>
                <li>
                    <a href="#" class="next">Next <i class="fal fa-long-arrow-right"></i></a>
                </li>
            </ul>
            <ul>
                <li>2-16</li>
            </ul>
        </div>--}}
    </div>
</section>

<!-- Modal Quick View -->
<div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-lg modal-quickview">

            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <div class="row">
                        <!-- <div class="col-lg-1">
                            <img class="mt-2 side-img" src="./img//product/back.png" />
                            <img class="mt-2 side-img" src="./img//product/side.png" />
                        </div> -->
                        <div class="col-lg-6 mt-2">
                            <!-- <img class="front-img" src="./img//product/Front.png" /> -->
                            <div class="fancy-container clearfix">
                                <div class="gallery">
                                    <div class="previews">
                                        <a href="javascript:void(0)" class="selected" data-full="img/product/top1large.jpg"><img src="img/product/top1small.jpg" /></a>
                                        <a href="javascript:void(0)" data-full="img/product/top2large.jpg"><img src="img/product/top2small.jpg" /></a>
                                        <a href="javascript:void(0)" data-full="img/product/top3large.jpg"><img src="img/product/top3small.jpg" /></a>
                                        <a href="javascript:void(0)" data-full="img/product/top4large.jpg"><img src="img/product/top4small.jpg" /></a>
                                        <a href="javascript:void(0)" data-full="img/product/top5large.jpg"><img src="img/product/top5small.jpg" /></a>
                                    </div>
                                    <div class="full quick-full">
                                        <!-- first image is viewable to start -->
                                        <img src="img/product/top1large.jpg" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h1 class="detail-h1 mb-2"> Coral Cut Out V-neck Basic Tee Plus Size </h1>
                            <p class="detail-price mb-2">$ 25.00</p>
                            <p class="short-description mb-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Eu vitae eu potenti ut id ultrices rhoncus, </p>
                            <p class="detail-size-p mb-2"><span class="detail-size">Size</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2(XL), 2(2XL), 2(3XL) </p>
                            <select class="detail-size-select">
                                <option>2(XL), 2(2XL), 2(3XL)</option>
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
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <form id='myform' method='POST' action='#'>
                                        <input type='button' value='-' class='qtyminus' field='quantity' />
                                        <input type='text' name='quantity' value='0' class='qty' />
                                        <input type='button' value='+' class='qtyplus' field='quantity' />
                                    </form>
                                </div>
                                <div class="col-lg-6">
                                    <a href="./cart.html" class=" btn cart-btn w-100">Add to cart</a>
                                </div>
                            </div>
                            <p class="mt-4 detail-basic">Basic Code &nbsp;&nbsp;&nbsp;<span class="detail-basic-p">502</span> </p>
                            <p class="detail-category mt-2">Categories: &nbsp;&nbsp;&nbsp;<span class="detail-category-p mt-2">Furniture, Table</span> </p>
                            <p class="detail-tag mt-2">Tag:&nbsp;&nbsp;&nbsp;<span class="detail-tag-p">Pottery</span> </p>
                            <div class="d-flex mt-4">
                                <p class="share-text pt-1 mr-2"> Share this items :
                                </p>
                                <a href="#"><img class="social-img ml-2" src="./img/icons/snapchat.png" /></a>
                                <a href="#"><img class="social-img ml-2" src="./img/icons/facebook.png" /></a>
                                <a href="#"><img class="social-img ml-2" src="./img/icons/Twitter.png" /></a>
                                <a href="#"><img class="social-img ml-2" src="./img/icons/instagram.png" /></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal Quick View -->