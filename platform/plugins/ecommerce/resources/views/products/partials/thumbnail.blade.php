<a href="{{ route('products.edit', $item->id) }}" title="{{ $item->name }}">
    <img src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" width="50">
</a>
