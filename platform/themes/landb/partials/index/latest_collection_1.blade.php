<section class="latest_collection">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="collectiontext">
                    <div class="small_h">
                        Latest Collection
                    </div>
                    <h1 class="revealUp">{{ setting('theme-landb-home_section_1_heading', 'Latest Collection') }} </h1>
                    <p>{{ setting('theme-landb-home_section_1_description', 'Latest Collection') }}</p>
                @if(!empty(setting('theme-landb-home_section_1_link')))
                    <a href="{{ setting('theme-landb-home_section_1_link') }}" class="lookbook_btn">LookBook</a>
                @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="collection_img">
                    @php
                        $product_ids = setting('theme-landb-home_section_1_products', json_encode(\Botble\Ecommerce\Models\Product::inRandomOrder()->limit(2)->pluck('id')->all()));
                       $product_ids = json_decode($product_ids);
                        $products = \Botble\Ecommerce\Models\Product::whereIn('id', $product_ids)->get();
                    @endphp
                    @if(count($products))
                        <div class="collec-imgbox">
                            {!! image_html_generator(@$products[0]->images[0]) !!}
                            <div class="imgcaption">
                                <a href="{!! generate_product_url('detail', $products[0]->id, $products[0]->product_slug)  !!}" class="save"><i class="fas fa-save"></i> Save</a>
                                <a href="{!!  generate_product_url('detail', $products[0]->id, $products[0]->product_slug)  !!}" class="search"><i class="fal fa-search"></i></a>
                            </div>
                        </div>
                        @if(isset($products[1]))
                        <div class="overlap">
                            <div class="collec-imgbox">
                                {!! image_html_generator(@$products[1]->images[0], @$products[1]->name, null, null, true, 'imgtop') !!}
                                <div class="imgcaption">
                                    <a href="{!! generate_product_url('detail', $products[1]->id, $products[1]->product_slug)  !!}" class="save"><i class="fas fa-save"></i> Save</a>
                                    <a href="{!!  generate_product_url('detail', $products[1]->id, $products[1]->product_slug)  !!}" class="search"><i class="fal fa-search"></i></a>
                                </div>
                            </div>
                        </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>