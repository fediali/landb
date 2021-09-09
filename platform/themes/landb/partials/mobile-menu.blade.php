<!-- <button class="toggle-menu">
    <span></span>
</button> -->
<div class="navigation nav-custom">
    <nav class="vg-nav vg-nav-lg">
        <ul {!! $options !!}>
            @foreach ($menu_nodes as $key => $row)
                @if($row->has_child)
                    <li class="dropdown">
                        <a href="{{ $row->url }}" target="{{ $row->target }}">{{ $row->name }}</a>
                        <ul class="left">
                            @foreach($row->child as $child)
                                @if($child->has_child)
                                    <li class="dropdown">
                                        <a href="{{ $child->url }}" target="{{ $child->target }}">{{ $child->name }}</a>
                                        <ul class="left">
                                            @foreach($child->child as $sub_child)
                                                <li>
                                                    <a href="{{ $sub_child->url }}" target="{{ $sub_child->target }}">{{ $sub_child->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>

                                @else
                                    <li>
                                        <a href="{{ $child->url }}" target="{{ $child->target }}">{{ $child->name }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>

                @else
                    <li>
                        <a href="{{ $row->url }}" target="{{ $row->target }}">{{ $row->name }}</a>
                    </li>
                @endif
            @endforeach

        </ul>
    </nav>
</div>