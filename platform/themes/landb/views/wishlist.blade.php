<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-2 mbtb-pr-2">
    <div class="row d-mbtb-none">
        <div class="col-lg-5 mt-2 mb-3">
            <p class="cart-head">Product</p>
        </div>
        <div class="col-lg-1 mt-2 mb-3 text-center">
            <p class="cart-head">Price</p>

        </div>
        <div class="col-lg-2 mt-2 mb-3">
            <p class="cart-head">Quantity</p>

        </div>
        <div class="col-lg-1 mt-2 mb-3">
            <p class="cart-head">Total</p>

        </div>
        <div class="col-lg-2 mt-2 mb-3">
            <p class="cart-head"></p>

        </div>
    </div>
    @foreach($wishlist->wishlistItems as $wishlistItem)
        <div class="row mb-4 mt-4 wishlistitem-{{ $wishlistItem->id }}">
            <div class="col-lg-5 mt-2">
                <div class="d-flex">
                    {!! image_html_generator(@$wishlistItem->product->images[0], @$wishlistItem->product->name, '95px', '75px' ) !!}
                    <div class="ml-3">
                        <p class="cart-product-name mt-2 mb-2">{{ $wishlistItem->product->name }}</p>
                        <p class="cart-product-code mb-2">CODE: {{ $wishlistItem->product->sku }}</p>
                        @php $sizes = get_category_sizes_by_id($wishlistItem->product->category_id); @endphp
                        @if(!empty($sizes->category_sizes))
                            <p class="cart-product-size">SIZE: @foreach($sizes->category_sizes as $size) {{ @$size->name }} {!! ($loop->last) ? '':',' !!} @endforeach</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-1 mt-2 text-center">
                <p class="mt-2">$ {{ $wishlistItem->product->final_price }}</p>
            </div>
            <div class="col-lg-2 mt-2">
                <form id='myform' method='POST' action='#'>
                    <input style="height: 35px;" type='button' value='-' class='qtyminus' field='quantity' />
                    <input style="height: 35px;" type='text' name='quantity' value='{{ $wishlistItem->quantity }}' class='qty' />
                    <input style="height: 35px;" type='button' value='+' class='qtyplus' field='quantity' />
                </form>
            </div>
            <div class="col-lg-1 mt-2 mb-txt-center">
                <p class="mt-2">$ {{ $wishlistItem->product->final_price * $wishlistItem->quantity }}</p>
            </div>
            <div class="col-lg-2 mt-2 text-center">
                <a href="#" class=" btn cart-btn w-100">Add to Cart</a>
            </div>
            <div class="col-lg-1 mt-2 mb-txt-center">
                <a href="#" class="mt-2"><i class="fa fa-trash"></i></a>
            </div>
        </div>
        <hr>
    @endforeach
   {{-- <div class="row mb-4 mt-4">
        <div class="col-lg-5 mt-2">
            <div class="d-flex">
                <img style="height: 95px; width: 75px;" src="./img/product/Front.png" />
                <div class="ml-3">
                    <p class="cart-product-name mt-2 mb-2">Coral Cut Out V-neck Basic Tee Plus Size</p>
                    <p class="cart-product-code mb-2">CODE: MH0315-RSER</p>
                    <p class="cart-product-size">SIZE: 2(XL), 2(2XL), 2(3XL) </p>
                </div>
            </div>
        </div>
        <div class="col-lg-1 mt-2 text-center">
            <p class="mt-2">$ 25.00</p>
        </div>
        <div class="col-lg-2 mt-2">
            <form id='myform' method='POST' action='#'>
                <input style="height: 35px;" type='button' value='-' class='qtyminus' field='quantity' />
                <input style="height: 35px;" type='text' name='quantity' value='0' class='qty' />
                <input style="height: 35px;" type='button' value='+' class='qtyplus' field='quantity' />
            </form>
        </div>
        <div class="col-lg-1 mt-2 mb-txt-center">
            <p class="mt-2">$ 150.00</p>
        </div>
        <div class="col-lg-2 mt-2 text-center">
            <a href="#" class=" btn cart-btn w-100">Add to Cart</a>
        </div>
        <div class="col-lg-1 mt-2 mb-txt-center">
            <a href="#" class="mt-2"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <hr>--}}


</section>