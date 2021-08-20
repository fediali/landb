<a href="{{ route('products.edit', $item->id) }}" title="{{ $item->name }}">
    {{--<img src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" width="50">--}}
    <img width="50" src="{{ asset('landb/defaultLogo.png') }}" alt="Product image" loading="lazy" class="lazyload " data-src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" onerror="this.src='{{ asset('images/default.jpg') }}'">
</a>
