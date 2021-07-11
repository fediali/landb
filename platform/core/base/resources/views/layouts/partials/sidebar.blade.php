@foreach ($menus = dashboard_menu()->getAll() as $menu)
    <li class="nav-item @if ($menu['active']) active @endif" id="{{ $menu['id'] }}">
        <a href="{{ $menu['url'] }}" class="nav-link nav-toggle">
            <i class="{{ $menu['icon'] }}"></i>
            <span class="title">
                {{ !is_array(trans($menu['name'])) ? trans($menu['name']) : $menu['name'] }}
                {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $menu['id']) !!}
            </span>
            @if (isset($menu['children']) && count($menu['children'])) <span class="arrow @if ($menu['active']) open @endif"></span> @endif
        </a>
        @if (isset($menu['children']) && count($menu['children']))
            <ul class="sub-menu @if (!$menu['active']) hidden-ul @endif">
                @foreach ($menu['children'] as $item)
                    <li class="nav-item @if ($item['active']) active @endif" id="{{ $item['id'] }}">
                        <a href="{{ $item['url'] }}" class="nav-link">
                            <i class="{{ $item['icon'] }}"></i>
                            <span class="title">
                                {{ trans($item['name']) }}
                                {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $item['id']) !!}
                            </span>
                            @if (isset($item['children']) && count($item['children'])) <span class="arrow @if ($item['active']) open @endif"></span> @endif
                        </a>
                        @if (isset($item['children']) && count($item['children']))
                            <ul class="sub-menu @if (!$item['active']) hidden-ul @endif">
                                @foreach ($item['children'] as $item2)
                                    <li class="nav-item @if ($item2['active']) active @endif" id="{{ $item2['id'] }}">
                                        <a href="{{ $item2['url'] }}" class="nav-link">
                                            <i class="{{ $item2['icon'] }}"></i>
                                            <span class="title">
                                                {{ trans($item2['name']) }}
                                                {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $item2['id']) !!}
                                            </span>
                                            @if (isset($item2['children']) && count($item2['children'])) <span class="arrow @if ($item2['active']) open @endif"></span> @endif
                                        </a>
                                        @if (isset($item2['children']) && count($item2['children']))
                                            <ul class="sub-menu @if (!$item2['active']) hidden-ul @endif">
                                                @foreach ($item2['children'] as $item3)
                                                    <li class="nav-item @if ($item3['active']) active @endif" id="{{ $item3['id'] }}">
                                                        <a href="{{ $item3['url'] }}" class="nav-link">
                                                            <i class="{{ $item3['icon'] }}"></i>
                                                            <span class="title">
                                                                {{ trans($item2['name']) }}
                                                                {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $item2['id']) !!}
                                                            </span>
                                                            {{--@if (isset($item3['children']) && count($item3['children'])) <span class="arrow @if ($item3['active']) open @endif"></span> @endif--}}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
