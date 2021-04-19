<ul class="ml-auto " {!! $options !!}>
    @foreach ($menu_nodes as $key => $row)
        <li class="nav-item {{ $row->css_class }} @if ($row->url == Request::url()) active @endif">
            <a href="{{ $row->url }}" target="{{ $row->target }}" class="nav-link">
                <i class='{{ trim($row->icon_font) }}'></i> <span>{{ $row->name }}</span>
            </a>
            {{--@if ($row->has_child)
                {!! Menu::generateMenu([
                    'slug' => $menu->slug,
                    'parent_id' => $row->id
                ]) !!}
            @endif--}}
        </li>
    @endforeach
    {{--<li class="nav-item active">
        <a class="nav-link" href="#">Women </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Man</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Footwear</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Accesories</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Lookbook</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Stores</a>
    </li>--}}
</ul>