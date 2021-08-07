<?php
$related = get_related_products($product);
?>
@if(count($related))
    <div class="row">
        <div class="col-lg-12 mt-4">
            <h1 class="detail-subheading mt-4">You Might Also Like</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-4">
            <div class="shoplisting detail-listing detail-shoplist">
                @foreach($related as $key => $product)
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
                    <div class="listbox">
                        <div class="img">
                            {!! image_html_generator(@$product->images[0]) !!}
                            {{--<div class="caro_text">
                                <h5>{{ @$product->category->name }}</h5>
                                <p>{{ $product->name }}</p>
                            </div>--}}
                            <span>Restock</span>

                            <div class="imgoverlay">
                                <a href="{!! generate_product_url('detail', $product->id, $product->product_slug) !!}" ><i class="far fa-eye"></i></a>
                                @if(auth('customer')->user())
                                    <a class="add-to-wishlist" id="wishlist-icon-{{$product->id}}" href="{!! generate_product_url('wishlist', $product->id) !!}" data-id="{{$product->id}}"><i class="far fa-heart"></i></a>
                                    <form id='myform-{{$product->id}}' class="add_to_cart_form" data-id="{{ $default->product_id }}" method='POST' action='{{ route('public.cart.add_to_cart') }}'>
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
                        <a href="{!! generate_product_url('detail', $product->id, $product->product_slug) !!}">
                            <div class="caption">
                                <h4>{{ $product->name }}</h4>
                                <div class="price">
                                    $<span id="price-of-{{$product->id}}">{{ $default->price }}</span>
                                </div>

                            </div>
                        </a>
                        <div class="text-center">
                            {{--<button style="padding: 12px 20px;" class="w-100 addTobag product-tile__add-to-cart" data-id="{{ $product->id }}">ADD TO BAG &nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>

                                    </button>--}}
                            <form id='myform2-{{$product->id}}' class="add_to_cart_form" data-id="{{ $default->product_id }}" method='POST' action='{{ route('public.cart.add_to_cart') }}'>
                                <div class="col-lg-4">
                                    <input type='hidden' name='quantity' value='1' class='qty' />
                                </div>
                                <button type="submit" class="product-tile__add-to-cart" ><span>Add to Bag</span></button>
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
                @endforeach
            </div>
        </div>
    </div>
@endif