<section class="luxurious_wrap">
    <div class="luxurious-text">
        <h1 class="revealUp">LUXURIOUS <small>and</small> <br> CONTEMPORARY APPEAL <br> <span></span>
            <small>for</small> EVERY WOMAN </h1>

    </div>
    <div class="owl-carousel">
        @foreach($home_featured as $featured)
            <div>{!! image_html_generator(@$featured->images[0]) !!}</div>
        @endforeach


        {{--<div><img src="landb/img/image-2.png" alt=""></div>
        <div><img src="landb/img/image-5.png" alt=""></div>
        <div><img src="landb/img/image-3.png" alt=""></div>
        <div><img src="landb/img/image-4.png" alt=""></div>--}}

    </div>
</section>