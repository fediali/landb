
$(document).ready(function () {
    var tag = jQuery.parseJSON($('#tag').val());
    console.log(tag, 'asd');
    $.each(tag, function (key, value) {
        if (value.value == 'Pre-Order') {
            $('.eta_pre_product').closest('.widget').show();
        } else {
            $('.eta_pre_product').closest('.widget').hide();
        }
    });


    $('#tag').on('change', function () {
        tag = jQuery.parseJSON($('#tag').val());
        console.log(tag)
        $.each(tag, function (key, value) {
            console.log(value)
            if (value.value == 'Pre-Order') {
                $('.eta_pre_product').closest('.widget').show();
            }
        })
    })
    $('.tagify__tag__removeBtn').on('click', function () {
        tag = jQuery.parseJSON($('#tag').val());
        $.each(tag, function (key, value) {
            if (value.value == 'Pre-Order') {
                $('.eta_pre_product').closest('.widget').show();
            } else {
                $('.eta_pre_product').closest('.widget').hide();
            }
        })

    })
});

function toggle_loader(event) {
    if (event) {
        $('.loading-overlay').addClass('is-active');

    } else {
        $('.loading-overlay').removeClass('is-active');
    }

}

$('.toggle-menu').click(function () {
    $(this).toggleClass('active');
    $('#menu').toggleClass('open');
});




// animation js
/*
gsap.registerPlugin(ScrollTrigger);
// REVEAL //
gsap.utils.toArray(".revealUp").forEach(function (elem) {
    ScrollTrigger.create({
        trigger: elem,
        start: "top 80%",
        end: "bottom 20%",
        markers: false,
        onEnter: function () {
            gsap.fromTo(
                elem, {y: 50, autoAlpha: 0}, {
                    duration: 1,
                    y: 0,
                    autoAlpha: 1,
                    ease: "back",
                    overwrite: "auto"
                }
            );
        },
        onLeave: function () {
            gsap.fromTo(elem, {autoAlpha: 1}, {autoAlpha: 0, overwrite: "auto"});
        },
        onEnterBack: function () {
            gsap.fromTo(
                elem, {y: -50, autoAlpha: 0}, {
                    duration: 1,
                    y: 0,
                    autoAlpha: 1,
                    ease: "back",
                    overwrite: "auto"
                }
            );
        },
        onLeaveBack: function () {
            gsap.fromTo(elem, {autoAlpha: 1}, {autoAlpha: 0, overwrite: "auto"});
        }
    });
});


// delay
gsap.registerPlugin(ScrollTrigger);
gsap.utils.toArray("section").forEach((elem) => {

    let tl = gsap.timeline({
        scrollTrigger: {
            trigger: elem,
            start: 'top 80%',
            end: 'bottom 20%',
            scrub: 1
        },
    })
        .from(elem, {y: 50, duration: 1, ease: 'none'})
});*/


$(document).ready(function () {
    $(document).on('submit', 'form.add_to_cart_form', function (e) {
        console.log('clicked')
        e.preventDefault();
        var id = $(this).attr('data-id');
        var qty = $(this).find('input[name="quantity"]').val();
        var formData = {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'product_id': id,
            'quantity': qty,
        };
        console.log(formData);
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            beforeSend: function () {
                toggle_loader(true);
            },
            success: function (result) {
                toggle_loader(false);
                toastr['success'](result.message, 'Success');
                $('#cart-icon-' + id).css('color', 'green');
                $('#user-cart-count').html(parseFloat($('#user-cart-count').text()) + parseFloat(qty));
            },
            error: function (result) {
                toggle_loader(false);
                if (result.status == 401) {
                    toastr['error']('You need to login first', 'Unauthorized!');
                } else {
                    console.log(result.responseJSON.message)
                    toastr['error'](result.responseJSON.message, 'Error');
                }
            }
        })
    });
    $(document).on('click', 'a.add-to-wishlist', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            beforeSend: function () {
                toggle_loader(true);
            },
            success: function (result) {
                toggle_loader(false);
                toastr['success'](result.message, 'Success');
                $('#wishlist-icon-' + id).css('color', 'red');
            },
            error: function (result) {
                toggle_loader(false);
                if (result.status == 401) {
                    toastr['error']('You need to login first', 'Unauthorized!');
                } else {
                    toastr['error'](result.message, 'Error');
                }
            }
        })
    });
});

function update_cart_item(input, val, url, action) {
    var id = input.data('id');
    var price = input.data('price');
    var status = 0;
    var formData = {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'id': id,
        'quantity': val,
        'action': action
    };
    console.log(formData)
    $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        async: false,
        beforeSend: function () {
            toggle_loader(true);
        },
        success: function (result) {
            if (val == 0) {
                $('.cartitem-' + id).remove();
            }
            toastr['success'](result.message, 'Success');
            var newTotal = val * price;
            var subTotal = parseFloat($('#total-cart-price').text());
            var grandTotal = parseFloat($('#grand-total-cart-price').text());
            $('#cart-item-total-' + id).html(parseFloat(newTotal).toFixed(1));
            $('#total-cart-price').html((action === 'inc') ? parseFloat(subTotal) + parseFloat(price) : subTotal - price);
            $('#grand-total-cart-price').html((action === 'inc') ? parseFloat(grandTotal) + parseFloat(price) : grandTotal - price);
            $('#user-cart-count').html((action === 'inc') ? parseFloat($('#user-cart-count').text()) + 1 : parseFloat($('#user-cart-count').text()) - 1);
            /*toggle_loader(false);*/
        },
        error: function (result) {
            toastr['error'](result.responseJSON.message, 'Error');
            status = 1;
        }
    });
    toggle_loader(false);
    return status;
}

function toggle_product_detail(id) {
    $.ajax({
        type: 'GET',
        url: '/products/' + id,
        beforeSend: function () {
            $('#product-detail-name').html('');
            $('#product-detail-price').html('');
            $('#product-detail-desc').html('');
            $('#product-detail-sizes').html('');
            $('#product-detail-category').html('');
            $('#product-detail-sku').html('');
            $('#product-detail-form').attr('data-id', '');
            $('#product-detail-button').attr('data-id', '');
            $("#product-detail-sizes-drop").html('');
            $("#product-detail-sizes-images").html('');
            $("#product-detail-sizes-image").html('');
            $("#product-detail-qty").val(1);
            toggle_loader(true);
        },
        success: function (result) {
            var images = sizes = categories = tags = '';
            var product = result.product;
            var variations = result.productVariations;
            var image = '<img class="front-img" !important;" src="images/default.jpg" />';
            var image1 = '<img src="images/default.jpg" />';
            if (product.images !== null && product.images.length !== 0) {
                product.images.forEach(function (value, index) {
                    images += '<a href="javascript:void(0)" class="selected" data-full="storage/' + value + '"><img src="storage/' + value + '" /></a>';
                });
                $('#product-detail-images').html(images);
                $('#product-detail-image').html('<img class="front-img" src="storage/' + product.images[0] + '" />');
            } else {
                $('#product-detail-images').html('<a href="javascript:void(0)" class="selected mt-2 side-img" data-full="images/default.jpg">' + image1 + '</a>');
                $('#product-detail-image').html(image);
            }
            if (product.category !== null) {
                /*if (product.category.category_sizes !== null) {
                  product.category.category_sizes.forEach(function (size, index) {
                    sizes += size.name + ',';
                    $("#product-detail-sizes-drop").append(new Option(size.name, size.id));
                  });
                }*/
            }
            $('#product-detail-name').html(product.name);
            $('#product-detail-price').html(variations[0].product.price);
            $('#product-detail-desc').html(product.description);
            $('#product-detail-sizes').html(sizes);
            /*$('#product-detail-category').html((product.category !== null) ? product.category.name : '');*/
            $('#product-detail-sku').html(product.sku);
            $('#product-detail-form').attr('data-id', variations[0].product_id);
            $('#product-detail-button').attr('data-id', variations[0].product_id);
            $('#myModal').modal('toggle');
            toggle_loader(false);
        },
        error: function (result) {
            toggle_loader(false);
            toastr['error'](result.message, 'Error');
        }
    })
}

function get_states(thiss, country, url) {
    console.log(url)
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            'country': country
        },
        beforeSend: function () {
            //toggle_loader(true);
        },

        success: function (result) {
            thiss.find('option').remove();
            jQuery.each(result, function (state, index) {
                thiss.append(new Option(index, state));
            });
            // toggle_loader(false);
        },
        error: function (e) {
            // toggle_loader(false);
        }
    })
}

/*
$(document).ready(function () {
  $(".customer_type").change(function() {
    $(".customer_type").prop('checked', false);
    $(this).prop('checked', true);
  });
})*/

/*$(document).ready(function () {

    function dp_scroll_text() {
        $(".dp-animate-hide").appendTo(".dp-scroll-text").removeClass("dp-animate-hide");
        $(".dp-scroll-text p:first-child").removeClass("dp-run-script dp-animate-1").addClass("dp-animate-hide");
        var images = $("p.dp-run-script.dp-animate-4").next().data('products');
        $("p.dp-run-script.dp-animate-4").next().addClass("dp-run-script dp-animate-4");
        $(".dp-run-script").removeClass("dp-animate-1 dp-animate-2 dp-animate-3 dp-animate-4");
        $('#vslider1').attr("src", images[0]);
        $('#vslider2').attr("src", images[1]);
        $('#vslider3').attr("src", images[2]);
        $('#vslider4').attr("src", images[3]);
        $('#vslider5').attr("src", images[4]);
        $('#vslider6').attr("src", images[5]);


        $.each($('.dp-run-script'), function (index, runscript) {
            index++;
            $(runscript).addClass('dp-animate-' + index);
        });
    }

    setInterval(function () {
        dp_scroll_text();
    }, 5000);


});*/

/*var mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}*/

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}

$(document).ready(function () {



    dotcount = 1;

    jQuery('.owl-dot').each(function () {
        jQuery(this).addClass('dotnumber' + dotcount);
        jQuery(this).addClass('xzoom-gallery');
        jQuery(this).attr('data-info', dotcount);
        jQuery(this).attr('data-info', dotcount);
        jQuery(this).attr('data-info', dotcount);
        dotcount = dotcount + 1;
    });

    slidecount = 1;

    jQuery('.owl-item').not('.cloned').each(function () {
        jQuery(this).addClass('slidenumber' + slidecount);
        slidecount = slidecount + 1;
    });

    setTimeout(function () {

        jQuery('.owl-dot').each(function () {
            grab = jQuery(this).data('info');
            slidegrab = jQuery('.slidenumber' + grab + ' img').attr('src');
            jQuery(this).css("background-image", "url(" + slidegrab + ")");
        });
    }, 2000);


    amount = $('.owl-dot').length;
    gotowidth = 100 / amount;
    jQuery('.owl-dot').css("height", gotowidth + "%");

});

