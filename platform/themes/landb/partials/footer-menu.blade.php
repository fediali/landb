@foreach ($menu_nodes as $key => $row)
    <div class="col-4">
        <h6><a href="{{ $row->url }}" target="{{ $row->target }}"><i class='{{ trim($row->icon_font) }}'></i><span>{{ $row->name }}</span></a></h6>
        {{--<ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Collection</a></li>
            <li><a href="#">Lookbook</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>--}}
        @if ($row->has_child)
            {!! Menu::generateMenu([
                'slug' => $menu->slug,
                'parent_id' => $row->id
            ]) !!}
        @endif
    </div>
@endforeach
