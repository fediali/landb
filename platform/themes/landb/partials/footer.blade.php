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
                    {!!
                        Menu::renderMenuLocation('footer-menu', [
                            'options' => [],
                            'theme' => true,
                            'view' => 'footer-menu',
                        ])
                    !!}
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
                            <li><a href="https://www.instagram.com/landb_official/">Instagram</a></li>
                            <li><a href="https://www.facebook.com/LANDBOFFICIAL/">Facebook</a></li>
                            <li><a href="https://www.pinterest.com/landb_official/">Pinterest</a></li>
                            <li><a href="https://www.linkedin.com/company/landbofficial/">Linkedin</a></li>
                            <li><a href="https://twitter.com/landb_official/">Twitter</a></li>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="footerlogo">
                    <a href="#"><img src="{{ asset('landb/img/footer-logo.png') }}" alt=""></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>All rights reserved - 2021 © Landbappreal</p>
            <p>Terms & Conditions</p>
            <!-- <p> Website by xyz</p> -->
        </div>
    </div>
</footer>
<script src="{{ asset('landb/js/jquery.js') }}"></script>
<script src="{{ asset('landb/js/popper.js') }}"></script>
<script src="{{ asset('landb/js/bootstrap.js') }}"></script>
<script src="{{ asset('landb/js/jquery-mask.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<!-- <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> -->
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.3/MotionPathPlugin.min.js"></script>
<script src="{{ asset('landb/js/custom.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
<script type="text/javascript" src="{{ asset('landb/jsignature/flashcanvas.js') }}"></script>
<![endif]-->
<script src="{{ asset('landb/jsignature/jSignature.min.js') }}"></script>
<script>
  $(document).ready(function() {
    $("#signature").jSignature({
      // line width
      lineWidth:2,

      // width/height of signature pad
      width:150,
      height:100,
      background_color:"#0f0"


  });
    $("#undo-sign").on('click', function () {
      $("#signature").jSignature('reset');
    });

    $("#signature").bind('change', function(e){
      var base64 = $("#signature").jSignature("getData");
      $("input[name='purchaser_sign']").val(base64);
    })
  })
</script>
<script>
  $(document).ready(function() {
    $("#filtertoggle").click(function() {
      $(this).toggleClass("on");
      $("#filtermenu").slideToggle();
    });
    $("input[name='shipping_postal_code']").mask('99999?-9999');

    $('select[name="shipping_country"]').on('change', function () {
      get_states($('select[name="shipping_state"]'), this.value, '{{ route('ajax.getStates') }}');
    });
    $('select[name="billing_country"]').on('change', function () {
      get_states($('select[name="billing_state"]'), this.value, '{{ route('ajax.getStates') }}');
    });
    $('select[name="locator_country"]').on('change', function () {
      get_states($('select[name="locator_state"]'), this.value, '{{ route('ajax.getStates') }}');
    });
    $('select[name="country"]').on('change', function () {
      get_states($('select[name="state"]'), this.value, '{{ route('ajax.getStates') }}');
    });


    $('#variation-select').on('change', function () {
      var data = JSON.parse($(this).val());

      $('#variation-form').attr('data-id', data.product_id);
      $('#variation-submit').attr('data-id', data.product_id);
      $('#product_price').html(data.product.price);
      $('#variation-quantity').attr('max', data.product.quantity);
      $('#variation-quantity').val(1);
    });

    $('.set-default').on('change', function () {
      toggle_loader(true);
      var status = 1;
      if($(this).is(':checked')){
        status = 1;
      }else{
        status = 0;
      }
      location.replace("{{ URL::to('customer/update-default') }}?id="+ $(this).data('id')+"&type="+ $(this).data('type')+"&status="+status);
      console.log(status, $(this).data('id'), $(this).data('type'))
    });
  });
</script>
<script>
  jQuery(document).ready(function() {
    // This button will increment the value
    $('.qtyplus').click(function(e) {
      /*toggle_loader(true);*/
      var newVal = 0;
      // Stop acting like a button
      e.preventDefault();
      // Get the field name
      fieldName = $(this).attr('field');
      // Get its current value
      var currentVal = parseInt($(this).prev('input[name=' + fieldName + ']').val());
      var max = parseInt($(this).prev('input[name=' + fieldName + ']').attr('max'));
      // If is not undefined
      if (!isNaN(currentVal)) {
        // Increment
        newVal = currentVal + 1;
        if(newVal > max){
          newVal = currentVal;
        }
       /* $(this).prev('input[name=' + fieldName + ']').val(currentVal + 1);*/
      } else {
        // Otherwise put a 0 there
        newVal = 0;
        /*$(this).prev('input[name=' + fieldName + ']').val(0);*/
      }

          console.log($(this).data('update'))
          if($(this).data('update') == '1') {
            var update = update_cart_item($(this), newVal, '{{ route('public.cart.update_cart') }}', 'inc');
            if(update == 0){
              $(this).prev('input[name=' + fieldName + ']').val(newVal);
            }
            console.log('result: '+newVal);
          }else{
            $(this).prev('input[name=' + fieldName + ']').val(newVal);
          }
    });
    // This button will decrement the value till 0
    $(".qtyminus").click(function(e) {
      var newVal = 0;
      var addVal = 0;
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
        /*$(this).next('input[name=' + fieldName + ']').val();*/
      } else {
        newVal = 0;
        // Otherwise put a 0 there
        /*$(this).next('input[name=' + fieldName + ']').val(0);*/
      }
      console.log($(this).data('update'))
      if($(this).data('update') == '1'){
        var update = update_cart_item($(this), newVal, '{{ route('public.cart.update_cart') }}','dec');
        if(update == 0){
          $(this).next('input[name=' + fieldName + ']').val(newVal);
        }
      }else{
        if(newVal > 0){
          $(this).next('input[name=' + fieldName + ']').val(newVal);
        }
      }

    });

    $('input[name="payment_method"]').on('click', function () {
      if(this.value == 'omni_payment'){
        $('.paypal-payment').hide();
        $('.card-payment').show();
      }else{
        $('.card-payment').hide();
        $('.paypal-payment').show();
      }
    });

    var tokenizeButton = document.querySelector('#tokenizebutton');

    // Init FattMerchant API
    var fattJs = new FattJs('LandB-Apparel-c03e1af6c561', {
      number: {
        id: 'fattjs-number',
        placeholder: '0000 0000 0000 0000',
        style: 'width: 90%; height:90%; border-radius: 3px; border: 1px solid #ccc; padding: .5em .5em; font-size: 91%;'
      },
      cvv: {
        id: 'fattjs-cvv',
        placeholder: '000',
        style: 'width: 30px; height:90%; border-radius: 3px; border: 1px solid #ccc; padding: .5em .5em; font-size: 91%;'
      }
    });

    // tell fattJs to load in the card fields
    fattJs.showCardForm().then(handler => {
      console.log('form loaded');
  })
  .catch(err => {
      console.log('error init form ' + err);
    // reinit form
  });

    fattJs.on('card_form_complete', (message) => {
        // activate pay button
        //payButton.disabled = false;
        var billing = $("#billing_address option:selected").text();


        tokenizeButton.disabled = billing === 'Select Address';
        console.log(message);
    });
    fattJs.on('card_form_incomplete', (message) => {
      // deactivate pay button
      //payButton.disabled = true;
      tokenizeButton.disabled = true;
        console.log(message);
    });
@if(auth('customer')->user() && auth('customer')->user()->details && isset(auth('customer')->user()->billingAddress[0]))
    document.querySelector('#tokenizebutton').onclick = (e) => {
      e.preventDefault();
      console.log('working')
      var month = $('.month').val();
      var year = $('.year').val();
      successElement = document.querySelector('.success');
      var errorElement = document.querySelector('.error');
      toggle_loader(true);

      var form = document.querySelector('form');
      var extraDetails = {
        firstname: "{{auth('customer')->user() ? auth('customer')->user()->details->first_name : 'john'}}",
        lastname: "{{auth('customer')->user() ? auth('customer')->user()->detail->last_name : 'doe'}}",
        method: "card",
        month: month,
        year: year,
        phone: "{{auth('customer')->user() ? auth('customer')->user()->phone : null}}",
        address_1: "{{ auth('customer')->user() ? auth('customer')->user()->billingAddress[0]->address: null }}",
        address_city: "{{ auth('customer')->user() ? auth('customer')->user()->billingAddress[0]->city: null }}",
        address_state: "{{ auth('customer')->user() ? auth('customer')->user()->billingAddress[0]->state: null }}",
        address_zip: "{{ auth('customer')->user() ? auth('customer')->user()->billingAddress[0]->zip_code: null }}",
        address_country: "{{ auth('customer')->user() ? auth('customer')->user()->billingAddress[0]->country: null }}",
        url: "https://omni.fattmerchant.com/#/bill/",
        validate: false,
      };
      console.log(extraDetails)
      // call tokenize api
      fattJs.tokenize(extraDetails).then((result) => {
        console.log(result);
      if (result) {
        toastr['success']('Card has been verified!', 'Success');
        functionAddCard(result, {{auth('customer')->user() ?auth('customer')->user()->id : null}});

      }
      toggle_loader(false);
    })
    .catch(err => {
        toastr['error'](err.message, 'Error');
      toggle_loader(false);
    });
    }
 @endif
  });

  function toggle_loader(event) {
    if(event){
        $('.loading-overlay').addClass('is-active');

    }else{
        $('.loading-overlay').removeClass('is-active');
    }

  }

  function functionAddCard(result, customer_id) {
    console.log('addingCard', result);
    $.ajax({
      url: '{{ route('customers.create-customer-payment') }}',
      type: 'post',
      data: {
        '_token': '{{ csrf_token() }}',
        'customer_data': result,
        'customer_id': customer_id,
      },
      success: function (data) {
        console.log(data)
        toastr['success']('Card has been added Successfully', 'Thanks!');

        $('#checkout-main-form').submit();
      },
      error: function (request, status, error) {
        toastr['warning']('Notification Unreadable', 'Reading Error');
      }
    });
  }
</script>

@if(session()->has('success'))
    <script>
      toastr['success']("{{ session()->get('success') }}", 'Success!');
    </script>
@elseif(session()->has('error'))
    <script>
      toastr['error']("{{ session()->get('error') }}", 'Error!');
    </script>
@endif 
</body>

</html>