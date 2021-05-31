$('.toggle-menu').click(function() {
    $(this).toggleClass('active');
    $('#menu').toggleClass('open');
});


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

// animation js

gsap.registerPlugin(ScrollTrigger);
// REVEAL //
gsap.utils.toArray(".revealUp").forEach(function(elem) {
    ScrollTrigger.create({
        trigger: elem,
        start: "top 80%",
        end: "bottom 20%",
        markers: false,
        onEnter: function() {
            gsap.fromTo(
                elem, { y: 50, autoAlpha: 0 }, {
                    duration: 1,
                    y: 0,
                    autoAlpha: 1,
                    ease: "back",
                    overwrite: "auto"
                }
            );
        },
        onLeave: function() {
            gsap.fromTo(elem, { autoAlpha: 1 }, { autoAlpha: 0, overwrite: "auto" });
        },
        onEnterBack: function() {
            gsap.fromTo(
                elem, { y: -50, autoAlpha: 0 }, {
                    duration: 1,
                    y: 0,
                    autoAlpha: 1,
                    ease: "back",
                    overwrite: "auto"
                }
            );
        },
        onLeaveBack: function() {
            gsap.fromTo(elem, { autoAlpha: 1 }, { autoAlpha: 0, overwrite: "auto" });
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
        .from(elem, { y: 50, duration: 1, ease: 'none' })
});


$(document).ready(function () {
  $(document).on('submit', 'form.add_to_cart_form', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
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
        $('#cart-icon-'+id).css('color','green');
        $('#user-cart-count').html(parseFloat($('#user-cart-count').text()) + parseFloat(qty));
      },
      error: function (result) {
        toggle_loader(false);
        if(result.status == 401){
          toastr['error']('You need to login first' , 'Unauthorized!');
        }else{
          console.log(result.responseJSON.message)
          toastr['error'](result.responseJSON.message , 'Error');
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
        $('#wishlist-icon-'+id).css('color','red');
      },
      error: function (result) {
        toggle_loader(false);
        if(result.status == 401){
          toastr['error']('You need to login first' , 'Unauthorized!');
        }else{
          toastr['error'](result.message , 'Error');
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
      if(val == 0){
        $('.cartitem-'+id).remove();
      }
      toastr['success'](result.message, 'Success');
      var newTotal = val * price;
      var subTotal = parseFloat($('#total-cart-price').text());
      var grandTotal = parseFloat($('#grand-total-cart-price').text());
      $('#cart-item-total-'+id).html(parseFloat(newTotal).toFixed(1));
      $('#total-cart-price').html((action === 'inc') ? parseFloat(subTotal)+parseFloat(price) : subTotal-price);
      $('#grand-total-cart-price').html((action === 'inc') ? parseFloat(grandTotal)+parseFloat(price) : grandTotal-price);
      $('#user-cart-count').html((action === 'inc') ? parseFloat($('#user-cart-count').text()) + 1 : parseFloat($('#user-cart-count').text()) - 1);
      /*toggle_loader(false);*/
    },
    error: function (result) {
        toastr['error'](result.responseJSON.message , 'Error');
      status = 1;
    }
  });
  toggle_loader(false);
  return status;
}

function toggle_product_detail(id) {
  e.preventDefault();
  $.ajax({
    type: 'GET',
    url: '/product/detail/'+id,
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
      var image = '<img src="images/lucky&blessed_logo_sign_Black 1.png" />';
      if (product.images !== null) {
        product.images.forEach(function (value, index) {
          images += '<a href="javascript:void(0)" class="selected" data-full="storage/'+ value +'"><img src="storage/'+ value +'" /></a>';
        });
        $('#product-detail-images').html(images);
        $('#product-detail-image').html('<img src="storage/'+ product.images[0] +'" />');
      }else{
        $('#product-detail-images').html('<a href="javascript:void(0)" class="selected" data-full="images/lucky&blessed_logo_sign_Black 1.png">+image+</a>');
        $('#product-detail-image').html(image);
      }
      if (product.category !== null) {
        if (product.category.category_sizes !== null) {
          product.category.category_sizes.forEach(function (size, index) {
            sizes += size.name + ',';
            $("#product-detail-sizes-drop").append(new Option(size.name, size.id));
          });
        }
      }
      $('#product-detail-name').html(product.name);
      $('#product-detail-price').html(product.price);
      $('#product-detail-desc').html(product.description);
      $('#product-detail-sizes').html(sizes);
      $('#product-detail-category').html((product.category !== null) ? product.category.name : '');
      $('#product-detail-sku').html(product.sku);
      $('#product-detail-form').attr('data-id', product.id);
      $('#product-detail-button').attr('data-id', product.id);
      $('#myModal').modal('toggle');
      toggle_loader(false);
    },
    error: function (result) {
      toggle_loader(false);
      toastr['error'](result.message , 'Error');
    }
  })
}

function get_states(thiss, country, url){
  $.ajax({
    type: 'GET',
    url: url,
    data: {
      'country' : country
    },
    beforeSend: function () {
      toggle_loader(true);
    },

    success: function (result) {
      thiss.find('option').remove();
      jQuery.each(result, function (state, index) {
        thiss.append(new Option(index, state));
      });
      toggle_loader(false);
    },
    error: function (e) {
      toggle_loader(false);
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
