<ul class="breadcrumb_wrap custom-breadcrumb mt-5" itemscope itemtype="http://schema.org/BreadcrumbList">
<div class="container">
    @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
        @if ($i != (count($crumbs) - 1))
            <li itemprop="itemListElement breadcrumb-item" itemscope itemtype="http://schema.org/ListItem">
                <meta itemprop="position" content="{{ $i + 1}}" />
                <a class="breadcrumb-a" href="{{ $crumb['url'] }}" itemprop="item" title="{{ $crumb['label'] }}">
                    {{ $crumb['label'] }}
                    <meta itemprop="name" content="{{ $crumb['label'] }}" />
                </a>
            </li>
        @else
            <li class="breadcrumb-item-act active">{{ $crumb['label'] }}</li>
        @endif
    @endforeach
    </div>
</ul>
