<a href="{{ route('products.edit', $item->id) }}" title="{{ $item->name }}">
    {{--<img src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" width="50">--}}
    <img width="50" src="{{ asset('landb/defaultLogo.png') }}" alt="Product image" loading="" class=""
         data-src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" onerror="this.src='{{ asset('images/oops.png') }}'">
</a>
