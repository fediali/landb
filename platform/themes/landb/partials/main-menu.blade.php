<ul class="ml-auto mega-ul " {!! $options !!}>
    @foreach ($menu_nodes as $key => $row)
        @if($row->has_child)
            <li class="nav-item dropdown {{ $row->css_class }} @if ($row->url == Request::url()) active @endif">
                <a href="{{ $row->url }}" target="{{ $row->target }}" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class='{{ trim($row->icon_font) }}'></i> <span>{{ $row->name }}</span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    @foreach($row->child as $child)
                        <a class="dropdown-item {{ $child->css_class }}" href="{{ URL::to($child->url) }}" target="{{ $row->target }}"><i class='{{ trim($child->icon_font) }}' ></i> {{ $child->title }}</a>
                    @endforeach
                </div>
            </li>
        @else
            <li class="nav-item {{ $row->css_class }} @if ($row->url == Request::url()) active @endif">
                <a href="{{ $row->url }}" target="{{ $row->target }}" class="nav-link">
                    <i class='{{ trim($row->icon_font) }}'></i> <span>{{ $row->name }}</span>
                </a>
            </li>
        @endif
    @endforeach

</ul>