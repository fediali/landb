<?php
$related = get_like_products_modded($product);
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
                                            ->where('ec_product_variations.is_default', 1)
                                            ->select('ec_product_variations.id','ec_product_variations.product_id', 'ep.price' )
                                            ->get();
                        $default = $variationData->first();

                     $productVariationsInfo = app(\Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface::class)
                                                 ->getVariationsInfo($variationData->pluck('id')->toArray());

                    @endphp
                    {!! Theme::partial('product-card', ['product' => $product , 'col' => '']) !!}
                @endforeach
            </div>
        </div>
    </div>
@endif
