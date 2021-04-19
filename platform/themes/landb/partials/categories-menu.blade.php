<div class="container">
    <a href="#"> <img src="{{ asset('landb/img/Logo.png') }}" alt=""></a>

    <nav class="main-nav" {!! $options !!}>
        @foreach ($menu_nodes as $key => $row)
            <div class="nav-col {{ $row->css_class }} @if ($row->url == Request::url()) active @endif">
                <h5><a href="{{ $row->url }}" target="{{ $row->target }}"> <i class='{{ trim($row->icon_font) }}'></i> <span>{{ $row->name }}</span></a></h5>
                @if ($row->has_child)
                    {!! Menu::generateMenu([
                        'slug' => $menu->slug,
                        'parent_id' => $row->id
                    ]) !!}
                @endif
        </div>
        @endforeach
        {{--<div class="nav-col">
            <h5>Women</h5>
            <ul>
                <li>
                    <a href="#">
                        Jeans
                    </a>
                </li>
                <li>
                    <a href="#">
                        Top
                    </a>
                </li>

                <li>
                    <a href="#">
                        Dresses
                    </a>
                </li>
                <li>
                    <a href="#">
                        Pants
                    </a>
                </li>
                <li>
                    <a href="#">
                        Skirts
                    </a>
                </li>
                <li>
                    <a href="#">
                        Shorts
                    </a>
                </li>
                <li>
                    <a href="#">
                        Kinonos
                    </a>
                </li>
            </ul>
        </div>
        <div class="nav-col">
            <h5>Plus</h5>
            <ul>
                <li>
                    <a href="#">
                        Plus Jackets & Quterwear
                    </a>
                </li>
                <li>
                    <a href="#">
                        Plus Jeans
                    </a>
                </li>

                <li>
                    <a href="#">
                        Plus Dresses
                    </a>
                </li>
                <li>
                    <a href="#">
                        Plus Skirts
                    </a>
                </li>
                <li>
                    <a href="#">
                        Plus Jackets & Outwear
                    </a>
                </li>
                <li>
                    <a href="#">
                        Plus Shorts
                    </a>
                </li>
                <li>
                    <a href="#">
                        Kinonos
                    </a>
                </li>
            </ul>
        </div>
        <div class="nav-col">
            <h5>Men's</h5>
            <ul>
                <li>
                    <a href="#">
                        Belts
                    </a>
                </li>
                <li>
                    <a href="#">
                        Shirts
                    </a>
                </li>


            </ul>
        </div>
        <div class="nav-col">
            <h5>Men's Plus</h5>
            <ul>

                <li>
                    <a href="#">
                        Shirts
                    </a>
                </li>


            </ul>
        </div>
        <div class="nav-col">
            <h5>Footwear</h5>
            <ul>

                <li>
                    <a href="#">
                        Booties
                    </a>
                </li>
                <li>
                    <a href="#">
                        Boots
                    </a>
                </li>
                <li>
                    <a href="#">
                        Hells
                    </a>
                </li>
                <li>
                    <a href="#">
                        Rain Boots
                    </a>
                </li>


            </ul>
        </div>
        <div class="nav-col">
            <h5>Accessories</h5>
            <ul>

                <li>
                    <a href="#">
                        Jewellary
                    </a>
                </li>
                <li>
                    <a href="#">
                        Headwear
                    </a>
                </li>
                <li>
                    <a href="#">
                        Keychain
                    </a>
                </li>
                <li>
                    <a href="#">
                        Bags
                    </a>
                </li>
                <li>
                    <a href="#">
                        Blankets
                    </a>
                </li>
                <li>
                    <a href="#">
                        Scarves
                    </a>
                </li>
                <li>
                    <a href="#">
                        Cups/Bottles
                    </a>
                </li>
                <li>
                    <a href="#">
                        Tech Accessories
                    </a>
                </li>


            </ul>
        </div>
        <div class="nav-col">
            <h5>Kids</h5>
            <ul>

                <li>
                    <a href="#">
                        Tops
                    </a>
                </li>
                <li>
                    <a href="#">
                        Jeans
                    </a>
                </li>
                <li>
                    <a href="#">
                        Pants
                    </a>
                </li>
                <li>
                    <a href="#">
                        Jumpsuit
                    </a>
                </li>
                <li>
                    <a href="#">
                        Kimono
                    </a>
                </li>
                <li>
                    <a href="#">
                        Shorts
                    </a>
                </li>
                <li>
                    <a href="#">
                        Cups/Bottles
                    </a>
                </li>
                <li>
                    <a href="#">
                        Baby Accessories
                    </a>
                </li>


            </ul>
        </div>--}}
    </nav>

    <footer class="menu-footer">
        <nav class="footer-nav">

            <ul>
                <li><span>Social Media</span></li>
                {!!
                   Menu::renderMenuLocation('social-media-menu', [
                       'options' => [],
                       'theme' => true,
                       'view' => 'social-media-menu',
                   ])
               !!}
            </ul>
                {!!
                   Menu::renderMenuLocation('main-menu', [
                       'options' => [],
                       'theme' => true,
                       'view' => 'internal-main-menu',
                   ])
               !!}
        </nav>
    </footer>
</div>