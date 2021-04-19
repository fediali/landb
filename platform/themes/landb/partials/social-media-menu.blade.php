@foreach ($menu_nodes as $key => $row)
    <li>
        <a target="{{ $row->target }} @if ($row->url == Request::url()) active @endif" href="{{ $row->url }}" >
            <i class="{{ trim($row->icon_font) }}"></i>
        </a>
    </li>
@endforeach