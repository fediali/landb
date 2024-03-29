<!-- <section class="needhelp">
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
</section> -->
<?php
$product_detail = (request()->segment(1) == 'products' && !empty(request()->segment(2))) ? true : false;
$home = empty(request()->segment(1)) ? true : false;
?>
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
                            <li><a href="/about-us">About</a></li>
                            <!-- <li><a href="/about-us">Trade Shows</a></li> -->
                            <li><a href="/contact-us">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-4">
                        <h6>Need Help</h6>
                        <ul>
                            <li><a href="/wholesale-info">Wholesale Info</a></li>
                            <li><a href="/size-chart">Size Chart</a></li>
                            <li><a href="/refund-policy">Refund Policy</a></li>
                            <li><a href="/faqs">Faqs</a></li>
                            <li><a href="/login">Login</a></li>

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
            <p>All rights reserved - 2021 © Lucky and Blessed</p>
            <!-- <p>Terms & Conditions</p> -->
            <!-- <p> Website by xyz</p> -->
        </div>
    </div>
</footer>

<button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fa fa-angle-up" aria-hidden="true"></i></button>
<!--<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/164071/Drift.min.js"></script>-->
<script src="{{ asset('landb/js/jquery.js') }}"></script>
<!--<script src='https://unpkg.com/xzoom/dist/xzoom.min.js'></script>-->
<!--<script src="{{ asset('landb/js/popper.js') }}"></script>-->
<!--<script src="{{ asset('landb/js/jquery.fancybox.js?v=2.1.4') }}"></script>-->
<script src="{{ asset('landb/js/bootstrap.js') }}"></script>
<!--<script src="{{ asset('landb/js/jquery-mask.min.js') }}"></script>-->
@if($home || $product_detail)
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js'></script>
    <script>
        $('.luxurious_wrap .owl-carousel').owlCarousel({
            center: true,
            loop: true,
            items: 5,
            autoplay: true,
            autoplayHoverPause: true,
            margin: 10,
            slideTransition: 'linear',
            autoplayTimeout: 3000,
            autoplaySpeed: 3000,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1170: {
                    items: 5
                }
            }
        })


        $('.explore_seasonal .owl-carousel').owlCarousel({
            center: true,
            loop: true,
            items: 3,
            autoplay: true,
            autoplayHoverPause: true,
            margin: 10,
            slideTransition: 'linear',
            autoplayTimeout: 3000,
            autoplaySpeed: 3000,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 1
                },
                1170: {
                    items: 3
                }
            }
        })
        $(".owl-carousel").owlCarousel({
            loop: true,
            items: 1,
            margin: 0,
            stagePadding: 0,
            autoplay: false
        });
    </script>
@endif
<!-- <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> -->
<!--<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.3/MotionPathPlugin.min.js"></script>-->

<script  src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous"></script>
<script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
@if(request()->segment(1) == 'customer' && request()->segment(2) == 'contract-form')
<script type="text/javascript" src="{{ asset('landb/jsignature/flashcanvas.js') }}"></script>
<script src="{{ asset('landb/jsignature/jSignature.min.js') }}"></script>
@endif
<script src="{{ asset('landb/js/custom.js') }}"></script>
<!--<script src='https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js'></script>-->
@if($product_detail)
<script src="{{ asset('landb/js/jquery.magnify.js') }}"></script>
<script>
    $('[data-magnify]').magnify({
        resizable: false,
        initMaximized: true,
        headerToolbar: [
            'close'
        ],
    });

</script>
@endif
<script>
    var coll = document.getElementsByClassName("collapsible");
    var i;

    for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    }
</script>

<script>


    $(document).ready(function () {

        /*$('.checkk').on('click', function () {
            /!* console.log($('#check1').prop("checked")+ ' : asdadad')*!/
            if ($('#check1').prop("checked") == true && $('#check2').prop("checked") == true && $('#check3').prop("checked") == true) {
                $('.tax-submit-btn').attr('disabled', false)
            }
        });*/
        @if(request()->segment(1) == 'customer' && request()->segment(2) == 'contract-form')
        $("#signature").jSignature({
            // line width
            lineWidth: 2,

            // width/height of signature pad
            width: 150,
            height: 100,
            background_color: "#0f0"


        });
        @endif
        $("#undo-sign").on('click', function () {
            $("#signature").jSignature('reset');
        });

        $("#signature").bind('change', function (e) {
            var base64 = $("#signature").jSignature("getData");
            $("input[name='purchaser_sign']").val(base64);
        })

    })
</script>
<script>
    $(document).ready(function () {
        $("#filtertoggle").click(function () {
            $(this).toggleClass("on");
            $("#filtermenu").slideToggle();
        });
        /*$("input[name='shipping_postal_code']").mask('99999?-9999');*/

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
            $('#varition_notice').html(data.product.quantity);
            $('#variation-quantity').val(1);
        });

        $('.product-tile__variant-input').on('click', function () {
            $('#myform2-' + $(this).data('parent')).attr('data-id', $(this).val());
            $('#price-of-' + $(this).data('parent')).html($(this).data('price'));
        });

        $('.set-default').on('change', function () {
            toggle_loader(true);
            var status = 1;
            if ($(this).is(':checked')) {
                status = 1;
            } else {
                status = 0;
            }
            location.replace("{{ URL::to('customer/update-default') }}?id=" + $(this).data('id') + "&type=" + $(this).data('type') + "&status=" + status);
            console.log(status, $(this).data('id'), $(this).data('type'))
        });
    });
</script>
<script>
    jQuery(document).ready(function () {
        // This button will increment the value
        $('.qtyplus').click(function (e) {
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
                if (newVal > max) {
                    newVal = currentVal;
                }
                /* $(this).prev('input[name=' + fieldName + ']').val(currentVal + 1);*/
            } else {
                // Otherwise put a 0 there
                newVal = 0;
                /*$(this).prev('input[name=' + fieldName + ']').val(0);*/
            }

            console.log($(this).data('update'))
            if ($(this).data('update') == '1') {
                var update = update_cart_item($(this), newVal, '{{ route('public.cart.update_cart') }}', 'inc');
                if (update == 0) {
                    $(this).prev('input[name=' + fieldName + ']').val(newVal);
                }
                console.log('result: ' + newVal);
            } else {
                $(this).prev('input[name=' + fieldName + ']').val(newVal);
            }
        });
        // This button will decrement the value till 0
        $(".qtyminus").click(function (e) {
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
            if ($(this).data('update') == '1') {
                var update = update_cart_item($(this), newVal, '{{ route('public.cart.update_cart') }}', 'dec');
                if (update == 0) {
                    $(this).next('input[name=' + fieldName + ']').val(newVal);
                }
            } else {
                if (newVal > 0) {
                    $(this).next('input[name=' + fieldName + ']').val(newVal);
                }
            }

        });

        $('input[name="payment_method"]').on('click', function () {
            if (this.value == 'omni_payment') {
                $('.paypal-payment').hide();
                $('.card-payment').show();
            } else {
                $('.card-payment').hide();
                $('.paypal-payment').show();
            }
        });

        var tokenizeButton = document.querySelector('#tokenizebutton');

        // Init FattMerchant API
        //sandbox LandB-Apparel-c03e1af6c561
        var fattJs = new FattJs('Lucky-and-BlessedLLC-b9b76900aed8', {
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
            $('#tokenizebutton').attr('disabled', true);
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
                    $('#tokenizebutton').attr('disabled', false);
                });
        }
        @endif
    });

    function functionAddCard(result, customer_id) {
        console.log('addingCard', result);
        $.ajax({
            url: '{{ route('customer.create-customer-payment') }}',
            type: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                'customer_data': result,
                'customer_id': customer_id,
            },
            success: function (data) {
                //console.log('data:'+data)
                toastr['success']('Card has been added Successfully', 'Thanks!');
                $('.payment_id').val(result.id);
                $('#checkout-main-form').submit();
            },
            error: function (request, status, error) {
                $('#tokenizebutton').attr('disabled', false);
                toastr['warning']('Notification Unreadable', 'Reading Error');
            }
        });
    }

    $('.card_list').on('change', function () {
        var card = $("select.card_list option:selected").val();
        $('.payment_id').val(card);
        if (card == 0) {
            $('#add_card').show();
            $('#add_payment').hide();
        } else {
            $('#add_card').hide();
            $('#add_payment').show();
        }
    })

    $(document).ready(function () {
        $('.welcomeDiv').hide();
        $('.addTobag').on('click', function () {
            var id = $(this).data('id');
            $('.welcomeDiv' + id).toggle();
        })
        $('.product-tile__hide-variants').on('click', function () {
            var id = $(this).data('id');
            $('.welcomeDiv' + id).hide();
        })

        $('#add_payment_btn').on('click', function () {
            $('#checkout-main-form').submit();
            $('#add_payment_btn').attr('disabled', true)
        })
    })

</script>
<script>

    $(document).ready(function () {

        $(".search-custom").on('click', function () {
            $("#top-sear").show();
        });

        $(".tp-close-btn").on('click', function () {
            $("#top-sear").hide().attr("#top-sear");
        });

        //   $(".gallery .full").hover(
        // function () {
        //   $(".detail-magnify").addClass('detail');
        // },
        // function () {
        //   $(".detail-magnify").removeClass('detail');
        // }
        // );

    });


</script>
<script>
    $(document).ready(function () {
        $('.fancy-container').on('click', 'a', function () {
            var largeImage = $(this).attr('data-full');
            $('.selected').removeClass();
            $(this).addClass('selected');
            $('.full img').hide();
            $('.full img').attr('src', largeImage);
            $('.full a').attr('href', largeImage);
            $('.full img').fadeIn();


        }); // closing the listening on a click
        // $('.full img').on('click', function() {
        //     var modalImage = $(this).attr('src');
        //     $.fancybox.open(modalImage);
        // });
        /*var main_search = document.getElementById("main_search");
        main_search.addEventListener("keyup", function(){
          var keyword = $('#main_search').val();
          if(keyword.length >= 2){
            setTimeout( function(){
              console.log('search')
              get_products(keyword);
            }  , 400 );
          }else if(keyword.length == 0){

            } else{}
          });

          function get_institutes(keyword) {
            $.ajax({
              type        : 'GET',
              url         : '',
              data        : {'keyword' : keyword},
              success: function (result) {
                var data = result.data;
                var status = result.status;
                if(status == 1){

                }else{

                }
              },
              error: function (result) {

              }
            });
          }
*/
    }); //closing our doc ready
</script>
<!-- <script type="text/javascript">

$('.containert').imagesLoaded( function() {
$("#exzoom").exzoom({
    autoPlay: false,
});
$("#exzoom").removeClass('hidden')
});

</script> -->
<script src="{{ asset('landb/js/vgnav.min.js') }}"></script>

<script>
    $(document).ready(function () {
        $(window).scroll(function () {
            var scroll = $(window).scrollTop();

            if (scroll >= 100) {
                $(".topbar").addClass("d-none");
            } else {
                $(".topbar").removeClass("d-none");
            }
        });
        $('.vg-nav').vegasMenu();
    })
    @if(is_null(@request()->segments()[0]))
    $(document).ready(function () {

        function dp_scroll_text() {
            $(".dp-animate-hide").appendTo(".dp-scroll-text").removeClass("dp-animate-hide up-top");
            $(".dp-scroll-text p:first-child").removeClass("dp-run-script dp-animate-1").addClass("dp-animate-hide up-top");
            var products = $("p.dp-run-script.dp-animate-2").next().data('products');
            //console.log(products[0].images[0])
            $("p.dp-run-script.dp-animate-4").next().addClass("dp-run-script dp-animate-4");
            $(".dp-run-script").removeClass("dp-animate-1 dp-animate-2 dp-animate-3 dp-animate-4");
            //console.log('first: '+images[0][0])
            $('#vslider1').attr("src", '{{ URL::to("/") }}/storage/' + products[0].images[0]);
            $('#vslider1').parent('a').attr('href', '{{URL::to("/")}}/products/' + products[0].slugable.key);

            $('#vslider2').attr("src", '{{ URL::to("/") }}/storage/' + products[1].images[0]);
            $('#vslider2').parent('a').attr('href', '{{URL::to("/")}}/products/' + products[1].slugable.key);

            $('#vslider3').attr("src", '{{ URL::to("/") }}/storage/' + products[2].images[0]);
            $('#vslider3').parent('a').attr('href', '{{URL::to("/")}}/products/' + products[2].slugable.key);

            $('#vslider4').attr("src", '{{ URL::to("/") }}/storage/' + products[3].images[0]);
            $('#vslider4').parent('a').attr('href', '{{URL::to("/")}}/products/' + products[3].slugable.key);

            $('#vslider5').attr("src", '{{ URL::to("/") }}/storage/' + products[4].images[0]);
            $('#vslider5').parent('a').attr('href', '{{URL::to("/")}}/products/' + products[4].slugable.key);

            $('#vslider6').attr("src", '{{ URL::to("/") }}/storage/' + products[5].images[0]);
            $('#vslider6').parent('a').attr('href', '{{URL::to("/")}}/products/' + products[5].slugable.key);


            $.each($('.dp-run-script'), function (index, runscript) {
                index++;
                $(runscript).addClass('dp-animate-' + index);
            });
        }

        setInterval(function () {
            dp_scroll_text();
        }, 10000);


    });

    @endif

</script>
<script>
    //Get the button
    var mybutton = document.getElementById("myBtn");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function () {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
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

@if(isset(request()->payment) && request()->payment == 'false')
    <script>toastr['error']('Sorry! The Payment was cancelled', 'Payment Error');</script>
    @endif


    </body>

    </html>
