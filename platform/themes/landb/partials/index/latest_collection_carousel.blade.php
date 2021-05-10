<section class="explore_seasonal  py-5 ">
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <div class="collectiontext">
                    <div class="small_h">
                        Latest Collection
                    </div>
                    <h1 class="revealUp">Explore our<br> Seasonal <small>Highlights</small> </h1>
                </div>
                <div id="home-slider" class="owl-carousel">
                    @foreach($latest_collection as $key => $product)
                        @if($key > 3)
                            <div>
                               <a href="{!! generate_product_url('detail', $product->id) !!}">
                                   {!! image_html_generator(@$product->images[0]) !!}
                                   <div class="caro_text">
                                       <h5>Dresses</h5>
                                       <p>Sandon vegan Leatther Dress</p>
                                   </div>
                               </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>


<script>
    var ACP = {
  init: function()
  {
    ACP.owl('#home-slider');
  },
  owl: function(owlID)
  {
    var owlSlider = $(owlID).owlCarousel(
      {
        items              : 1,
        itemsDesktop       : false,
        itemsDesktopSmall  : false,
        itemsTablet        : false,
        itemsMobile        : false,
        loop               : false,
        autoplay           : true,
        autoplayTimeout    : 2000,
        autoplayHoverPause : true,
        nav : false,
        dots							 : true

      });

    $('.play-control').on('click', '.play', function(eve)
      {
        owlSlider.trigger('play.owl.autoplay', 1000);
      });

    $('.play-control').on('click', '.stop', function()
      {
        owlSlider.trigger('stop.owl.autoplay');
        console.log('Stop!');
      });
  }
}
ACP.init();    

</script>