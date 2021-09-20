<?php
    $metas = null;
    $category = null;
    $product_slug = (count(request()->segments()) > 1 ? (request()->segment(1) == 'products' ? request()->segment(2) : null) : null);

    if(empty($product_slug)){
      $category = get_category(request()->path('c_slug'));
      if($category){
        $metas = get_meta($category, 'seo_meta');
      }
    }else{
      $slug = \Illuminate\Support\Facades\DB::table('slugs')
                ->where('key',$product_slug)
                ->where('reference_type',  \Botble\Ecommerce\Models\Product::class)
                ->where('prefix', SlugHelper::getPrefix(\Botble\Ecommerce\Models\Product::class))
                ->first();
      if($slug){
        $product = \Botble\Ecommerce\Models\Product::find($slug->reference_id);
        if($product){
          $metas = get_meta($product, 'seo_meta');
        }
      }
    }
    /*dd($metas, $product);*/

?>
@if(!empty($metas))
<title>{{ $metas->meta_value[0]['seo_title'] }}</title>
<meta name="description" content="{{ $metas->meta_value[0]['seo_description'] }}">
<link rel="canonical" href="{{ url()->current() }}"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:site_name" content="">
<meta property="og:locale" content="en_US">
<meta property="og:title" content="{{ $metas->meta_value[0]['seo_title'] }}">
<meta property="og:description" content="{{ $metas->meta_value[0]['seo_description'] }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
@endif