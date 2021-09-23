<a href="{{ route('products.edit', $item->id) }}" title="{{ $item->name }}">

    @if (@getimagesize(asset('storage/'. $item->image)))
{{--        <img src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" width="50">--}}
        <img width="50" src="{{ asset('landb/defaultLogo.png') }}" alt="Product image" loading="lazy" class="lazyload " data-src="{{ RvMedia::getImageUrl($item->image, null , false, RvMedia::getDefaultImage()) }}" onerror="this.src='{{ asset('images/default.jpg') }}'">
    @else
        @php
            $image1 = str_replace('.JPG', '.jpg', @$item->image);
            $image2 = str_replace('.jpg', '.JPG', @$item->image);
        @endphp
        @if (@getimagesize(asset('storage/'. $image1)))
            <img width="50" src="{{ asset('landb/defaultLogo.png') }}" alt="Product image" loading="lazy" class="lazyload " data-src="{{ RvMedia::getImageUrl($image1, null , false, RvMedia::getDefaultImage()) }}" onerror="this.src='{{ asset('images/default.jpg') }}'">
{{--            <img src="{{ RvMedia::getImageUrl($image1, null , false, RvMedia::getDefaultImage()) }}" width="50">--}}
        @elseif(@getimagesize(asset('storage/'. $image2)))
            <img width="50" src="{{ asset('landb/defaultLogo.png') }}" alt="Product image" loading="lazy" class="lazyload " data-src="{{ RvMedia::getImageUrl($image2, null , false, RvMedia::getDefaultImage()) }}" onerror="this.src='{{ asset('images/default.jpg') }}'">
{{--            <img src="{{ RvMedia::getImageUrl($image2, null , false, RvMedia::getDefaultImage()) }}" width="50">--}}
        @endif
    @endif

</a>
