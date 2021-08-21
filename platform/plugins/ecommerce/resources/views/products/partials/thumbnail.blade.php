<a href="{{ route('products.edit', $item->id) }}" title="{{ $item->name }}">
    {{--<img src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" width="50">--}}
    {!! image_html_generator(RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()), null, null, 50) !!}
</a>
