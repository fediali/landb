<section class="needhelp">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="help_heading">
                    <h3>Need <br> <em>Help?</em></h3>
                </div>
            </div>
            <div class="col-md-8">

                <div class="row">
                    <div class="col-md-4">
                        <div class="helpbox">
                            <h5>Where to buy?</h5>
                            <p>Interested? Find the nearest store to me</p>
                            <strong>Product Locator</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="helpbox">
                            <h5>Wholesale</h5>
                            <p>Already a member? Login to discover our collections.</p>
                            <strong>Login</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="helpbox">
                            <h5>Need Help?</h5>
                            <p>Haven't found what you're looking for? Contact us.</p>
                            <strong>Customer support</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<footer class="footer_sec">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-4">
                        <h6>Navigate</h6>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Collection</a></li>
                            <li><a href="#">Lookbook</a></li>
                            <li><a href="#">About</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-4">
                        <h6>Need Help</h6>
                        <ul>
                            <li><a href="#">Customer Service</a></li>
                            <li><a href="#">Store Loacator</a></li>
                            <li><a href="#">Login</a></li>

                        </ul>
                    </div>
                    <div class="col-4">
                        <h6>Social Media</h6>
                        <ul>
                            <li><a href="#">Instagram</a></li>
                            <li><a href="#">Facebook</a></li>
                            <li><a href="#">Snapchat</a></li>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="footerlogo">
                    <a href="#"><img class="revealUp" src="img/footer-logo.png" alt=""></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>All rights reserved - 2021 © Landbappreal</p>
            <p>Terms & Conditions</p>
            <p> Website by xyz</p>
        </div>
    </div>
</footer>
<script src="{{ asset('landb/js/jquery.js') }}"></script>
<script src="{{ asset('landb/js/popper.js') }}"></script>
<script src="{{ asset('landb/js/bootstrap.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<!-- <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> -->
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.3/MotionPathPlugin.min.js"></script>
<script src="{{ asset('landb/js/custom.js') }}"></script>
{{--<script>
  $(document).ready(function() {
    $("#filtertoggle").click(function() {
      $(this).toggleClass("on");
      $("#filtermenu").slideToggle();
    });
  });
</script>--}}
<script>
  jQuery(document).ready(function() {
    // This button will increment the value
    $('.qtyplus').click(function(e) {
      var newVal = 0;
      // Stop acting like a button
      e.preventDefault();
      // Get the field name
      fieldName = $(this).attr('field');
      // Get its current value
      var currentVal = parseInt($(this).prev('input[name=' + fieldName + ']').val());
      // If is not undefined
      if (!isNaN(currentVal)) {
        // Increment
        newVal = currentVal + 1;
        $(this).prev('input[name=' + fieldName + ']').val(currentVal + 1);
      } else {
        // Otherwise put a 0 there
        $(this).prev('input[name=' + fieldName + ']').val(0);
      }
      if($(this).data('update') == '1') {
        update_cart_item($(this).data('id'), newVal, '{{ route('public.cart.update_cart') }}');
      }
    });
    // This button will decrement the value till 0
    $(".qtyminus").click(function(e) {
      var newVal = 0;
      // Stop acting like a button
      e.preventDefault();
      // Get the field name
      fieldName = $(this).attr('field');
      // Get its current value
      var currentVal = parseInt($(this).next('input[name=' + fieldName + ']').val());
      // If it isn't undefined or its greater than 0
      if (!isNaN(currentVal) && currentVal > 0) {
        newVal = currentVal - 1;
        // Decrement one
        $(this).next('input[name=' + fieldName + ']').val(currentVal - 1);
      } else {
        // Otherwise put a 0 there
        $(this).next('input[name=' + fieldName + ']').val(0);
      }
      if($(this).data('update') == '1'){
        update_cart_item($(this).data('id'), newVal, '{{ route('public.cart.update_cart') }}');
      }

    });
  });
  
  function toggle_loader(event) {
    if(event){
      $('.loading-overlay').addClass('is-active');
    }else{
      $('.loading-overlay').removeClass('is-active');
    }

  }
</script>

</body>

</html>