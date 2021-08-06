@php
    $variationData = \Botble\Ecommerce\Models\ProductVariation::join('ec_products as ep', 'ep.id', 'ec_product_variations.product_id')
                        ->where('ep.quantity', '>', 0)
                        ->where('ec_product_variations.configurable_product_id', $product->id)
                        ->orderBy('ec_product_variations.is_default', 'desc')
                        ->select('ec_product_variations.id','ec_product_variations.product_id', 'ep.price' )
                        ->get();
    $default = $variationData->first();

 $productVariationsInfo = app(\Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface::class)
                             ->getVariationsInfo($variationData->pluck('id')->toArray());

@endphp
<div class="listbox mb-3 col-lg-{{ isset($col) ? $col : '4' }}">
    <div class="img">
        {!! image_html_generator(@$product->images[0]) !!}
        {{--<div class="caro_text">
            <h5>{{ @$product->category->name }}</h5>
            <p>{{ $product->name }}</p>
        </div>--}}
        @if($product->tags()->where('name','Pre-Order')->value('name'))
            <p class="pre-label">Pre-Order</p>
        @endif
        @if($product->product_label_id)
            @if($product->product_label_id == 3)
                <img class="usa-label" src="{{ asset('landb/img/usa-label.svg') }}"/>
            @else
                <span>{{$product->label->name}}</span>
            @endif()
        @endif()

        <div class="imgoverlay">
            <a href="{!! generate_product_url('detail', $product->id, $product->product_slug) !!}"><i
                        class="far fa-eye"></i></a>
            @if(auth('customer')->user())
                <a class="add-to-wishlist" id="wishlist-icon-{{$product->id}}"
                   href="{!! generate_product_url('wishlist', $product->id) !!}"
                   data-id="{{$product->id}}"><i class="far fa-heart"></i></a>
                <form id='myform-{{$product->id}}' class="add_to_cart_form"
                      data-id="{{ $default->product_id }}" method='POST'
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
    <a href="{!! generate_product_url('detail', $product->id, $product->product_slug) !!}">
        <div class="caption">
            <h4>{{ $product->name }}</h4>
            <div class="price">
                $<span id="price-of-{{$product->id}}">{{ $default->price/$product->prod_pieces }}</span>
            </div>

        </div>
    </a>
    <div class="text-center">
        {{--<button style="padding: 12px 20px;" class="w-100 addTobag product-tile__add-to-cart" data-id="{{ $product->id }}">ADD TO BAG &nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>

                </button>--}}
        <form id='myform2-{{$product->id}}' class="add_to_cart_form"
              data-id="{{ $default->product_id }}" method='POST'
              action='{{ route('public.cart.add_to_cart') }}'>
            <div class="col-lg-4">
                <input type='hidden' name='quantity' value='1' class='qty'/>
            </div>
            <button type="submit" class="product-tile__add-to-cart"><span>Add to Bag</span></button>
        </form>
    </div>

    {{--<div class="product-tile__variants-area welcomeDiv{{ $product->id }}">
        <div class="product-tile__variants-heading">
            Select a Size
        </div>
        <button type="button" class="product-tile__hide-variants" data-id="{{ $product->id }}">
            <span>X</span>
        </button>
        <div class="product-tile__variants"><label>
                @foreach($variationData as $variation)
                    @foreach ($productVariationsInfo->where('variation_id', $variation->id)->where('attribute_set_id', 2) as $key => $item)
                        <input class="product-tile__variant-input" type="radio" name="variation_idd" value="{{ $variation->product_id }}" data-title="{{ $item->title }}" data-parent="{{ $product->id }}" data-price="{{ $variation->price }}" required="">
                        <span class="product-tile__variant">{{ @explode('-', $item->title)[0] }}</span>
                        </label><label>
                    @endforeach
                @endforeach
        </div>
        <form id='myform2-{{$product->id}}' class="add_to_cart_form" data-id="{{ $default->product_id }}" method='POST' action='{{ route('public.cart.add_to_cart') }}'>
            <div class="col-lg-4">
                <input type='hidden' name='quantity' value='1' class='qty' />
            </div>
            <button type="submit" class="product-tile__add-to-cart" ><span>Add to Bag</span></button>
        </form>

    </div>--}}
</div>