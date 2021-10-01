<?php

if (!function_exists('shortcode')) {
    /**
     * @return \Botble\Shortcode\Shortcode
     */
    function shortcode()
    {
        return app('shortcode');
    }
}

if (!function_exists('add_shortcode')) {
    /**
     * @param string $key
     * @param string $name
     * @param null|string $description
     * @param Callable|string $callback
     * @return \Botble\Shortcode\Shortcode
     */
    function add_shortcode($key, $name, $description = null, $callback = null)
    {
        return shortcode()->register($key, $name, $description, $callback);
    }
}

if (!function_exists('do_shortcode')) {
    /**
     * @param string $content
     * @return string
     */
    function do_shortcode($content)
    {
        return shortcode()->compile($content);
    }
}
if (!function_exists('generate_shortcode')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function generate_shortcode($name, array $attributes = [])
    {
        return shortcode()->generateShortcode($name, $attributes);
    }
}

if (!function_exists('image_fallback')) {
    function image_fallback($image_url){
        if (@getimagesize(asset('storage/'. $image_url))) {
            return $image_url;
        } else {
            $images1 = str_replace('.JPG', '.jpg', $image_url);
            $images2 = str_replace('.jpg', '.JPG', $image_url);
            if (@getimagesize(asset('storage/'. $images1))) {
                return $images1;
            } elseif (@getimagesize(asset('storage/'. $images2))) {
                return $images2;
            }
        }
    }
}

if (!function_exists('image_html_generator')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function image_html_generator($img, $alt = null, $height = null, $width = null, $lazy = true, $class = '', $id = '', $original = '')
    {

        /*$html = '<img
            ' . (!is_null($height) ? 'height="' . $height . 'px"' : '') . '
            ' . (!is_null($width) ? 'width="' . $width . 'px"' : '') . '
            src="' . asset('landb/defaultLogo.png') . '"
            alt="' . (!is_null($alt) ? $alt : 'Product image') . '"
            loading="lazy"
            class="lazyload ' . $class . '"
            id="' . $id . '"
            xoriginal="' . (!empty($original) ? asset('storage/' . $img) : '') . '"
            data-src="' . (!empty($img) ? asset('storage/' . $img) : asset('images/default.jpg')) . '"
            onerror = "this.src=\'' . asset('images/default.jpg') . '\'">';*/

        $html = '<img
              ' . (!is_null($height) ? 'height="' . $height . 'px"' : '') . '
              ' . (!is_null($width) ? 'width="' . $width . 'px"' : '') . '
              src="' . asset('storage/'. $img) . '"
              alt="' . (!is_null($alt) ? $alt : 'No image') . '"
              loading="lazy"
              class=" ' . $class . '"
              id="' . $id . '"
              onerror = "this.src=\'' . asset('images/oops.png') . '\'">';

        return $html;
    }
}

if (!function_exists('format_price')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function format_price($number)
    {
        return number_format((float)$number, 2, '.', '');
    }
}
if (!function_exists('generate_product_url')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function generate_product_url($type, $id, $slug = null)
    {

        if ($type == 'save') {
            return URL::to('/product/save/' . $id);
        } elseif ($type == 'detail') {
            return route('public.singleProduct', ['slug' => $slug]);
            /*return URL::to('product/detail/'.$slug);*/
        } elseif ($type == 'cart') {
            return URL::to('/product/add/cart/' . $id);
        } elseif ($type == 'wishlist') {
            return route('public.add_to_wishlist', ['id' => $id]);
        } else {
            return URL::to('/products');
        }

    }
}

if (!function_exists('parent_categories')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function parent_categories()
    {
        $categories = \Botble\Ecommerce\Models\ProductCategory::where('ec_product_categories.parent_id', 0)->where('ec_product_categories.status', 'published')->join('slugs', 'slugs.reference_id', 'ec_product_categories.id')->where('slugs.prefix', 'product-categories')->select('slugs.key', 'ec_product_categories.name', 'ec_product_categories.id')->get();
        return $categories;
    }
}

if (!function_exists('product_tags')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function product_tags()
    {
        $tags = \Botble\Ecommerce\Models\ProductTag::where('ec_product_tags.status', 'published')->join('slugs', 'slugs.reference_id', 'ec_product_tags.id')->where('slugs.prefix', 'product-tags')->select('slugs.key', 'ec_product_tags.name', 'ec_product_tags.id')->get();
        return $tags;
    }
}

if (!function_exists('category_sizes')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function category_sizes()
    {
        $sizes = \Botble\Categorysizes\Models\Categorysizes::where('status', 'published')->get();
        return $sizes;
    }
}

if (!function_exists('cart_count')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function cart_count()
    {
        if (auth('customer')->user()) {
            $total = 0;
            $order = \Botble\Ecommerce\Models\Order::with('products')->where('user_id', auth('customer')->user()->id)->where('is_finished', 0)->first();
            if ($order) {
                foreach ($order->products as $product) {
                    $total = $total + $product->qty;
                }
            }
            return $total;
        } else {
            return 0;
        }
    }

}

if (!function_exists('update_product_quantity')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function update_product_quantity($id, $qty, $type = 'inc')
    {
        $product = \Botble\Ecommerce\Models\Product::find($id);
        if ($product) {
            if (!checkIfProductPreOrder($id)) {
                if ($type == 'inc') {
                    $product->increment('quantity', $qty);
                } elseif ($type == 'dec') {
                    if ($product->quantity > $qty) {
                        $product->decrement('quantity', $qty);
                    } else {
                        $product->update(['quantity' => 0]);
                    }
                }
            }
        }

    }

}

if (!function_exists('checkIfProductPreOrder')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function checkIfProductPreOrder($productId)
    {

        $product = \Botble\Ecommerce\Models\ProductVariation::where('ec_product_variations.product_id', $productId)
            ->join('ec_product_tag_product as eptp', 'eptp.product_id', 'ec_product_variations.configurable_product_id')
            ->where('tag_id', 3)->first();
        /*$product = Product::where('ec_products.id', 21)
            ->join('ec_product_tag_product as eptp', 'eptp.product_id','ec_products.id')
            ->where('tag_id', 3)->first();*/
        if ($product) {
            return true;
        } else {
            return false;
        }
    }

}


if (!function_exists('get_countries')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function get_countries()
    {
        return \CountryState::getCountries();
    }

}


if (!function_exists('get_states')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function get_states($country = '')
    {
        return !empty($country) ? \CountryState::getStates($country) : [];
    }

}


if (!function_exists('get_state_name')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function get_state_name($state, $country)
    {
        $name = '';
        try {
            $name = \CountryState::getStateName($state, $country);
        } catch (Exception $e) {

        }
        return $name;
    }

}

if (!function_exists('get_country_name')) {
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function get_country_name($country)
    {
        $name = '';
        try {
            $name = \CountryState::getCountryName($country);
        } catch (Exception $e) {

        }
        return $name;
    }

}

if (!function_exists('get_parent_product_by_variant')) {
    /**
     * Helper get parent of product variation
     * @param int $variationId
     * @param array $with
     * @return Product
     */
    function get_parent_product_by_variant($variationId, array $with = [])
    {
        return app(\Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface::class)->getParentOfVariation($variationId, $with);
    }
}
