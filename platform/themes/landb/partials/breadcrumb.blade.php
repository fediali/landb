
    <section class="breadcrumb_wrap">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">

                        <li class="breadcrumb-item"><a href="{{ url()->route('public.index') }}">Home</a></li>
                    @php
                        $url = URL::to('/').'/';
                    @endphp
                    @foreach(request()->segments() as $segment)
                        @if($segment != 'search')
                            @if($loop->last)
                                @if(isset($category) && !empty($category))
                                    <li class="breadcrumb-item active" aria-current="page">{{ @$category->name }}</li>
                                @elseif(isset($product))
                                    <li class="breadcrumb-item active" aria-current="page">{{ @$product->name }}</li>
                                @else
                                    <li class="breadcrumb-item active" aria-current="page">{{ ucfirst(str_replace(['-', '_'], ' ', $segment)) }}</li>
                                @endif
                            @else
                                @php
                                    $url .= $segment.'/';
                                @endphp
                                @if($segment == 'checkout')
                                    <li class="breadcrumb-item" aria-current="page">{{ ucfirst(str_replace(['-', '_'], ' ', $segment)) }}</li>
                                    @break
                                @else
                                    <li class="breadcrumb-item" aria-current="page"><a href="{{ $url }}">{{ ucfirst(str_replace(['-', '_'], ' ', $segment)) }}</a></li>
                                @endif
                            @endif
                        @endif
                    @endforeach

                </ol>
            </nav>
        </div>
    </section>

