/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*******************************************************!*\
  !*** ./platform/themes/martfury/assets/js/backend.js ***!
  \*******************************************************/
(function ($) {
  'use strict';

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  window.botbleCookieNewsletter = function () {
    var COOKIE_VALUE = 1;
    var COOKIE_NAME = 'botble_cookie_newsletter';
    var COOKIE_DOMAIN = $('div[data-session-domain]').data('session-domain');
    var COOKIE_MODAL = $('#subscribe');
    var COOKIE_MODAL_TIME = COOKIE_MODAL.data('time');

    function newsletterWithCookies(expirationInDays) {
      setCookie(COOKIE_NAME, COOKIE_VALUE, expirationInDays);
    }

    function cookieExists(name) {
      return document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1;
    }

    function hideCookieDialog() {
      if (!cookieExists(COOKIE_NAME) && $('#dont_show_again').is(':checked')) {
        newsletterWithCookies(3);
      } else {
        newsletterWithCookies(1 / 24);
      }
    }

    function setCookie(name, value, expirationInDays) {
      var date = new Date();
      date.setTime(date.getTime() + expirationInDays * 24 * 60 * 60 * 1000);
      document.cookie = name + '=' + value + ';expires=' + date.toUTCString() + ';domain=' + COOKIE_DOMAIN + ';path=/';
    }

    if (!cookieExists(COOKIE_NAME)) {
      setTimeout(function () {
        if (COOKIE_MODAL.length > 0) {
          COOKIE_MODAL.addClass('active');
          $('body').css('overflow', 'hidden');
        }
      }, COOKIE_MODAL_TIME);
    }

    return {
      newsletterWithCookies: newsletterWithCookies,
      hideCookieDialog: hideCookieDialog
    };
  }();

  var showError = function showError(message) {
    window.showAlert('alert-danger', message);
  };

  var showSuccess = function showSuccess(message) {
    window.showAlert('alert-success', message);
  };

  var handleError = function handleError(data) {
    if (typeof data.errors !== 'undefined' && data.errors.length) {
      handleValidationError(data.errors);
    } else if (typeof data.responseJSON !== 'undefined') {
      if (typeof data.responseJSON.errors !== 'undefined') {
        if (data.status === 422) {
          handleValidationError(data.responseJSON.errors);
        }
      } else if (typeof data.responseJSON.message !== 'undefined') {
        showError(data.responseJSON.message);
      } else {
        $.each(data.responseJSON, function (index, el) {
          $.each(el, function (key, item) {
            showError(item);
          });
        });
      }
    } else {
      showError(data.statusText);
    }
  };

  var handleValidationError = function handleValidationError(errors) {
    var message = '';
    $.each(errors, function (index, item) {
      if (message !== '') {
        message += '<br />';
      }

      message += item;
    });
    showError(message);
  };

  window.showAlert = function (messageType, message) {
    if (messageType && message !== '') {
      var alertId = Math.floor(Math.random() * 1000);
      var html = "<div class=\"alert ".concat(messageType, " alert-dismissible\" id=\"").concat(alertId, "\">\n                            <span class=\"close icon-cross2\" data-dismiss=\"alert\" aria-label=\"close\"></span>\n                            <i class=\"icon-") + (messageType === 'alert-success' ? 'checkmark-circle' : 'cross-circle') + " message-icon\"></i>\n                            ".concat(message, "\n                        </div>");
      $('#alert-container').append(html).ready(function () {
        window.setTimeout(function () {
          $("#alert-container #".concat(alertId)).remove();
        }, 6000);
      });
    }
  };

  $(document).ready(function () {
    window.onBeforeChangeSwatches = function () {
      $('.add-to-cart-form button[type=submit]').prop('disabled', true).addClass('btn-disabled');
    };

    window.onChangeSwatchesSuccess = function (res) {
      $('.add-to-cart-form .error-message').hide();
      $('.add-to-cart-form .success-message').hide();

      if (res.error) {
        $('.add-to-cart-form button[type=submit]').prop('disabled', true).addClass('btn-disabled');
        $('.number-items-available').html('<span class="text-danger">(' + res.message + ')</span>').show();
        $('.hidden-product-id').val('');
      } else {
        $('.add-to-cart-form').find('.error-message').hide();
        $('.ps-product__price span').text(res.data.display_sale_price);

        if (res.data.sale_price !== res.data.price) {
          $('.ps-product__price del').text(res.data.display_price).show();
        } else {
          $('.ps-product__price del').hide();
        }

        $('.hidden-product-id').val(res.data.id);
        $('.add-to-cart-form button[type=submit]').prop('disabled', false).removeClass('btn-disabled');
        $('.number-items-available').html('<span class="text-success">(' + res.message + ')</span>').show();
        var slider = $(document).find('.ps-product--quickview .ps-product__images');

        if (slider.length) {
          slider.slick('unslick');
          var imageHtml = '';
          res.data.image_with_sizes.origin.forEach(function (item) {
            imageHtml += '<div class="item"><img src="' + item + '" alt="image"/></div>';
          });
          slider.html(imageHtml);
          slider.slick({
            slidesToShow: slider.data('item'),
            slidesToScroll: 1,
            infinite: false,
            arrows: slider.data('arrow'),
            focusOnSelect: true,
            prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
            nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>"
          });
        }

        var product = $('.ps-product--detail');

        if (product.length > 0) {
          var primary = product.find('.ps-product__gallery');
          var second = product.find('.ps-product__variants');
          var vertical = product.find('.ps-product__thumbnail').data('vertical');

          if (primary.length) {
            primary.slick('unslick');
            var _imageHtml = '';
            res.data.image_with_sizes.origin.forEach(function (item) {
              _imageHtml += '<div class="item"><a href="' + item + '"><img src="' + item + '" alt="image"/></a></div>';
            });
            primary.html(_imageHtml);
            primary.slick({
              slidesToShow: 1,
              slidesToScroll: 1,
              asNavFor: '.ps-product__variants',
              fade: true,
              dots: false,
              infinite: false,
              arrows: primary.data('arrow'),
              prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
              nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>"
            });
          }

          if (second.length) {
            second.slick('unslick');
            var thumbHtml = '';
            res.data.image_with_sizes.thumb.forEach(function (item) {
              thumbHtml += '<div class="item"><img src="' + item + '" alt="image"/></div>';
            });
            second.html(thumbHtml);
            second.slick({
              slidesToShow: second.data('item'),
              slidesToScroll: 1,
              infinite: false,
              arrows: second.data('arrow'),
              focusOnSelect: true,
              prevArrow: "<a href='#'><i class='fa fa-angle-up'></i></a>",
              nextArrow: "<a href='#'><i class='fa fa-angle-down'></i></a>",
              asNavFor: '.ps-product__gallery',
              vertical: vertical,
              responsive: [{
                breakpoint: 1200,
                settings: {
                  arrows: second.data('arrow'),
                  slidesToShow: 4,
                  vertical: false,
                  prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
                  nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>"
                }
              }, {
                breakpoint: 992,
                settings: {
                  arrows: second.data('arrow'),
                  slidesToShow: 4,
                  vertical: false,
                  prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
                  nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>"
                }
              }, {
                breakpoint: 480,
                settings: {
                  slidesToShow: 3,
                  vertical: false,
                  prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
                  nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>"
                }
              }]
            });
          }
        }

        $(window).trigger('resize');

        if (product.length > 0) {
          product.find('.ps-product__gallery').data('lightGallery').destroy(true);
          product.find('.ps-product__gallery').lightGallery({
            selector: '.item a',
            thumbnail: true,
            share: false,
            fullScreen: false,
            autoplay: false,
            autoplayControls: false,
            actualSize: false
          });
        }
      }
    };

    $('.ps-panel--sidebar').show();
    $('.ps-popup').show();
    $('.menu--product-categories .menu__content').show();
    $('.ps-popup__close').on('click', function (e) {
      e.preventDefault();
      $(this).closest('.ps-popup').removeClass('active');
      $('body').css('overflow', 'auto');
      window.botbleCookieNewsletter.hideCookieDialog();
    });
    $('#subscribe').on('click', function (event) {
      if (!$(event.target).closest('.ps-popup__content').length) {
        $(this).removeClass('active');
        $('body').css('overflow-y', 'auto');
        window.botbleCookieNewsletter.hideCookieDialog();
      }
    });
    $(document).on('click', '.newsletter-form button[type=submit]', function (event) {
      event.preventDefault();
      event.stopPropagation();

      var _self = $(this);

      _self.addClass('button-loading');

      $.ajax({
        type: 'POST',
        cache: false,
        url: _self.closest('form').prop('action'),
        data: new FormData(_self.closest('form')[0]),
        contentType: false,
        processData: false,
        success: function success(res) {
          _self.removeClass('button-loading');

          if (typeof refreshRecaptcha !== 'undefined') {
            refreshRecaptcha();
          }

          if (!res.error) {
            window.botbleCookieNewsletter.newsletterWithCookies(30);

            _self.closest('form').find('input[type=email]').val('');

            showSuccess(res.message);
            setTimeout(function () {
              _self.closest('.modal-body').find('button[data-dismiss="modal"]').trigger('click');
            }, 2000);
          } else {
            showError(res.message);
          }
        },
        error: function error(res) {
          if (typeof refreshRecaptcha !== 'undefined') {
            refreshRecaptcha();
          }

          _self.removeClass('button-loading');

          handleError(res);
        }
      });
    });
    $(document).on('click', '.ps-form--download-app button[type=submit]', function (event) {
      event.preventDefault();

      var _self = $(event.currentTarget);

      _self.addClass('button-loading');

      $.ajax({
        url: _self.closest('form').prop('action'),
        data: _self.closest('form').serialize(),
        type: 'POST',
        success: function success(res) {
          if (res.error) {
            _self.removeClass('button-loading');

            window.showAlert('alert-danger', res.message);
            return false;
          }

          window.showAlert('alert-success', res.message);

          _self.removeClass('button-loading');
        },
        error: function error(res) {
          _self.removeClass('button-loading');

          handleError(res, _self.closest('form'));
        }
      });
    });
    $(document).on('change', '.switch-currency', function () {
      $(this).closest('form').submit();
    });
    $(document).on('change', '.product-filter-item', function () {
      $(this).closest('form').submit();
    });
    $(document).on('click', '.js-add-to-wishlist-button', function (event) {
      event.preventDefault();

      var _self = $(this);

      _self.addClass('button-loading');

      $.ajax({
        url: _self.prop('href'),
        method: 'POST',
        success: function success(res) {
          if (res.error) {
            _self.removeClass('button-loading');

            window.showAlert('alert-danger', res.message);
            return false;
          }

          window.showAlert('alert-success', res.message);
          $('.btn-wishlist span i').text(res.data.count);

          _self.removeClass('button-loading').removeClass('js-add-to-wishlist-button').addClass('js-remove-from-wishlist-button active');
        },
        error: function error(res) {
          _self.removeClass('button-loading');

          window.showAlert('alert-danger', res.message);
        }
      });
    });
    $(document).on('click', '.js-remove-from-wishlist-button', function (event) {
      event.preventDefault();

      var _self = $(this);

      _self.addClass('button-loading');

      $.ajax({
        url: _self.prop('href'),
        method: 'DELETE',
        success: function success(res) {
          if (res.error) {
            _self.removeClass('button-loading');

            window.showAlert('alert-danger', res.message);
            return false;
          }

          window.showAlert('alert-success', res.message);
          $('.btn-wishlist span i').text(res.data.count);

          _self.closest('tr').remove();

          _self.removeClass('button-loading').removeClass('js-remove-from-wishlist-button active').addClass('js-add-to-wishlist-button');
        },
        error: function error(res) {
          _self.removeClass('button-loading');

          window.showAlert('alert-danger', res.message);
        }
      });
    });
    $(document).on('click', '.js-add-to-compare-button', function (event) {
      event.preventDefault();

      var _self = $(this);

      _self.addClass('button-loading');

      $.ajax({
        url: _self.prop('href'),
        method: 'POST',
        success: function success(res) {
          if (res.error) {
            _self.removeClass('button-loading');

            window.showAlert('alert-danger', res.message);
            return false;
          }

          window.showAlert('alert-success', res.message);
          $('.btn-compare span i').text(res.data.count);

          _self.removeClass('button-loading').removeClass('js-add-to-compare-button').addClass('js-remove-from-compare-button active');
        },
        error: function error(res) {
          _self.removeClass('button-loading');

          window.showAlert('alert-danger', res.message);
        }
      });
    });
    $(document).on('click', '.js-remove-from-compare-button', function (event) {
      event.preventDefault();

      var _self = $(this);

      var buttonText = _self.text();

      _self.text(buttonText + '...');

      $.ajax({
        url: _self.prop('href'),
        method: 'DELETE',
        success: function success(res) {
          if (res.error) {
            _self.text(buttonText);

            window.showAlert('alert-danger', res.message);
            return false;
          }

          $('.ps-table--compare').load(window.location.href + ' .ps-table--compare > *', function () {
            window.showAlert('alert-success', res.message);
            $('.btn-compare span i').text(res.data.count);

            _self.text(buttonText);
          });
        },
        error: function error(res) {
          _self.removeClass('button-loading');

          window.showAlert('alert-danger', res.message);
        }
      });
    });
    $(document).on('click', '.add-to-cart-button', function (event) {
      event.preventDefault();

      var _self = $(this);

      _self.prop('disabled', true).addClass('button-loading');

      $.ajax({
        url: _self.prop('href'),
        method: 'POST',
        data: {
          id: _self.data('id')
        },
        dataType: 'json',
        success: function success(res) {
          _self.prop('disabled', false).removeClass('button-loading').addClass('active');

          if (res.error) {
            window.showAlert('alert-danger', res.message);
            return false;
          }

          window.showAlert('alert-success', res.message);

          if (_self.prop('name') === 'checkout' && res.data.next_url !== undefined) {
            window.location.href = res.data.next_url;
          } else {
            $.ajax({
              url: window.siteUrl + '/ajax/cart',
              method: 'GET',
              success: function success(response) {
                if (!response.error) {
                  $('.ps-cart--mobile').html(response.data.html);
                  $('.btn-shopping-cart span i').text(response.data.count);
                }
              }
            });
          }
        },
        error: function error(res) {
          _self.prop('disabled', false).removeClass('button-loading');

          window.showAlert('alert-danger', res.message);
        }
      });
    });
    $(document).on('click', '.remove-cart-item', function (event) {
      event.preventDefault();

      var _self = $(this);

      _self.closest('.ps-product--cart-mobile').addClass('content-loading');

      $.ajax({
        url: _self.prop('href'),
        method: 'GET',
        success: function success(res) {
          _self.closest('.ps-product--cart-mobile').removeClass('content-loading');

          if (res.error) {
            window.showAlert('alert-danger', res.message);
            return false;
          }

          $.ajax({
            url: window.siteUrl + '/ajax/cart',
            method: 'GET',
            success: function success(response) {
              if (!response.error) {
                $('.ps-cart--mobile').html(response.data.html);
                $('.btn-shopping-cart span i').text(response.data.count);
                window.showAlert('alert-success', res.message);
              }
            }
          });
        },
        error: function error(res) {
          _self.closest('.ps-product--cart-mobile').removeClass('content-loading');

          window.showAlert('alert-danger', res.message);
        }
      });
    });
    $(document).on('click', '.add-to-cart-form button[type=submit]', function (event) {
      event.preventDefault();
      event.stopPropagation();

      var _self = $(this);

      if (!$('.hidden-product-id').val()) {
        _self.prop('disabled', true).addClass('btn-disabled');

        return;
      }

      _self.prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

      _self.closest('form').find('.error-message').hide();

      _self.closest('form').find('.success-message').hide();

      $.ajax({
        type: 'POST',
        cache: false,
        url: _self.closest('form').prop('action'),
        data: new FormData(_self.closest('form')[0]),
        contentType: false,
        processData: false,
        success: function success(res) {
          _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');

          if (res.error) {
            _self.removeClass('button-loading');

            window.showAlert('alert-danger', res.message);
            return false;
          }

          window.showAlert('alert-success', res.message);

          if (_self.prop('name') === 'checkout' && res.data.next_url !== undefined) {
            window.location.href = res.data.next_url;
          } else {
            $.ajax({
              url: window.siteUrl + '/ajax/cart',
              method: 'GET',
              success: function success(response) {
                if (!response.error) {
                  $('.ps-cart--mobile').html(response.data.html);
                  $('.btn-shopping-cart span i').text(response.data.count);
                }
              }
            });
          }
        },
        error: function error(res) {
          _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');

          handleError(res, _self.closest('form'));
        }
      });
    });
    $(document).on('change', '.submit-form-on-change', function () {
      $(this).closest('form').submit();
    });
    $(document).on('click', '.form-review-product button[type=submit]', function (event) {
      var _this = this;

      event.preventDefault();
      event.stopPropagation();
      $(this).prop('disabled', true).addClass('btn-disabled').addClass('button-loading');
      $.ajax({
        type: 'POST',
        cache: false,
        url: $(this).closest('form').prop('action'),
        data: new FormData($(this).closest('form')[0]),
        contentType: false,
        processData: false,
        success: function success(res) {
          $(_this).closest('form').find('.success-message').html('').hide();
          $(_this).closest('form').find('.error-message').html('').hide();

          if (!res.error) {
            $(_this).closest('form').find('select').val(0);
            $(_this).closest('form').find('textarea').val('');
            $(_this).closest('form').find('.success-message').html(res.message).show();
            $.ajax({
              url: window.siteUrl + '/ajax/reviews/' + $(_this).closest('form').find('input[name=product_id]').val(),
              method: 'GET',
              success: function success(response) {
                if (!response.error) {
                  $('#list-reviews').html(response.data.html);
                  $(document).find('select.rating').each(function () {
                    var readOnly = $(this).attr('data-read-only') === 'true';
                    $(this).barrating({
                      theme: 'fontawesome-stars',
                      readonly: readOnly,
                      emptyValue: '0'
                    });
                  });
                }
              }
            });
            setTimeout(function () {
              $(this).closest('form').find('.success-message').html('').hide();
            }, 5000);
          } else {
            $(_this).closest('form').find('.error-message').html(res.message).show();
            setTimeout(function () {
              $(this).closest('form').find('.error-message').html('').hide();
            }, 5000);
          }

          $(_this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
        },
        error: function error(res) {
          $(_this).prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
          handleError(res, $(_this).closest('form'));
        }
      });
    });
    $('.form-coupon-wrapper .coupon-code').keypress(function (event) {
      if (event.keyCode === 13) {
        $('.apply-coupon-code').trigger('click');
        event.preventDefault();
        event.stopPropagation();
        return false;
      }
    });
    $(document).on('click', '.btn-apply-coupon-code', function (event) {
      event.preventDefault();

      var _self = $(event.currentTarget);

      _self.prop('disabled', true).addClass('btn-disabled').addClass('button-loading');

      $.ajax({
        url: _self.data('url'),
        type: 'POST',
        data: {
          coupon_code: _self.closest('.form-coupon-wrapper').find('.coupon-code').val()
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function success(res) {
          if (!res.error) {
            $('.ps-section--shopping').load(window.location.href + '?applied_coupon=1 .ps-section--shopping > *', function () {
              _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');

              window.showAlert('alert-success', res.message);
            });
          } else {
            window.showAlert('alert-danger', res.message);

            _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
          }
        },
        error: function error(data) {
          if (typeof data.responseJSON !== 'undefined') {
            if (data.responseJSON.errors !== 'undefined') {
              $.each(data.responseJSON.errors, function (index, el) {
                $.each(el, function (key, item) {
                  window.showAlert('alert-danger', item);
                });
              });
            } else if (typeof data.responseJSON.message !== 'undefined') {
              window.showAlert('alert-danger', data.responseJSON.message);
            }
          } else {
            window.showAlert('alert-danger', data.status.text);
          }

          _self.prop('disabled', false).removeClass('btn-disabled').removeClass('button-loading');
        }
      });
    });
    $(document).on('click', '.btn-remove-coupon-code', function (event) {
      event.preventDefault();

      var _self = $(event.currentTarget);

      var buttonText = _self.text();

      _self.text(_self.data('processing-text'));

      $.ajax({
        url: _self.data('url'),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function success(res) {
          if (!res.error) {
            $('.ps-section--shopping').load(window.location.href + ' .ps-section--shopping > *', function () {
              _self.text(buttonText);
            });
          } else {
            window.showAlert('alert-danger', res.message);

            _self.text(buttonText);
          }
        },
        error: function error(data) {
          if (typeof data.responseJSON !== 'undefined') {
            if (data.responseJSON.errors !== 'undefined') {
              $.each(data.responseJSON.errors, function (index, el) {
                $.each(el, function (key, item) {
                  window.showAlert('alert-danger', item);
                });
              });
            } else if (typeof data.responseJSON.message !== 'undefined') {
              window.showAlert('alert-danger', data.responseJSON.message);
            }
          } else {
            window.showAlert('alert-danger', data.status.text);
          }

          _self.text(buttonText);
        }
      });
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
      var n = !isFinite(+number) ? 0 : +number,
          prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
          sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep,
          dec = typeof dec_point === 'undefined' ? '.' : dec_point,
          toFixedFix = function toFixedFix(n, prec) {
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        var k = Math.pow(10, prec);
        return Math.round(n * k) / k;
      },
          s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');

      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }

      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }

      return s.join(dec);
    }

    function filterSlider() {
      $('.nonlinear').each(function (index, element) {
        var $element = $(element);
        var min = $element.data('min');
        var max = $element.data('max');
        var $wrapper = $(element).closest('.nonlinear-wrapper');
        noUiSlider.create(element, {
          connect: true,
          behaviour: 'tap',
          start: [$wrapper.find('.product-filter-item-price-0').val(), $wrapper.find('.product-filter-item-price-1').val()],
          step: max / 10,
          range: {
            min: min,
            '10%': max * 0.1,
            '20%': max * 0.2,
            '30%': max * 0.3,
            '40%': max * 0.4,
            '50%': max * 0.5,
            '60%': max * 0.6,
            '70%': max * 0.7,
            '80%': max * 0.8,
            '90%': max * 0.9,
            max: max
          }
        });
        var nodes = [$('.ps-slider__min'), $('.ps-slider__max')];
        var currentExchangeRate = parseFloat($('div[data-current-exchange-rate]').data('current-exchange-rate'));
        element.noUiSlider.on('update', function (values, handle) {
          nodes[handle].html(number_format(Math.round(values[handle] * currentExchangeRate)));
        });
        element.noUiSlider.on('end', function (values, handle) {
          $wrapper.find('.product-filter-item-price-' + handle).val(Math.round(values[handle] * currentExchangeRate));
          $wrapper.find('.product-filter-item').closest('form').submit();
        });
      });
    }

    filterSlider();
    $(document).on('click', '.js-quick-view-button', function (event) {
      event.preventDefault();

      var _self = $(event.currentTarget);

      _self.addClass('button-loading');

      $.ajax({
        url: _self.prop('href'),
        type: 'GET',
        success: function success(res) {
          if (!res.error) {
            $('#product-quickview .ps-product--quickview').html(res.data);
            $('.ps-product--quickview .ps-product__images').slick({
              slidesToShow: 1,
              slidesToScroll: 1,
              fade: true,
              dots: false,
              arrows: true,
              infinite: false,
              prevArrow: "<a href='#'><i class='fa fa-angle-left'></i></a>",
              nextArrow: "<a href='#'><i class='fa fa-angle-right'></i></a>"
            });
            $('#product-quickview').modal('show');
          }

          _self.removeClass('button-loading');
        },
        error: function error() {
          _self.removeClass('button-loading');
        }
      });
    });
    $('.product__qty .up').on('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      var currentVal = parseInt($(this).closest('.product__qty').find('.qty-input').val(), 10);
      $(this).closest('.product__qty').find('.qty-input').val(currentVal + 1).prop('placeholder', currentVal + 1).trigger('change');
    });
    $('.product__qty .down').on('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      var currentVal = parseInt($(this).closest('.product__qty').find('.qty-input').val(), 10);

      if (currentVal > 0) {
        $(this).closest('.product__qty').find('.qty-input').val(currentVal - 1).prop('placeholder', currentVal - 1).trigger('change');
      }
    });
    $(document).on('change', '.product-category-select', function () {
      $('.product-cat-label').text($.trim($(this).find('option:selected').text()));
    });
    $('.product-cat-label').text($.trim($('.product-category-select option:selected').text()));
    var locationTimeout = null;
    $('#input-search').on('keydown', function () {
      $('.ps-panel--search-result').html('').removeClass('active');
    }).on('keyup', function () {
      var k = $(this).val();

      if (k) {
        $('.ps-form--quick-search .spinner-icon').show();
        clearTimeout(locationTimeout);
        locationTimeout = setTimeout(function () {
          var form = $('.ps-form--quick-search');
          $.ajax({
            type: 'GET',
            url: form.data('ajax-url'),
            data: form.serialize(),
            success: function success(res) {
              if (!res.error && res.data !== '') {
                $('.ps-panel--search-result').html(res.data).addClass('active');
              } else {
                $('.ps-panel--search-result').html('').removeClass('active');
              }

              $('.ps-form--quick-search .spinner-icon').hide();
            },
            error: function error() {
              $('.ps-form--quick-search .spinner-icon').hide();
            }
          });
        }, 500);
      }
    });
    $('.rating_wrap > a ').on('click', function (e) {
      e.preventDefault();
      var target = $(this).attr('href');
      $('.ps-tab-list li').removeClass('active');
      $('.ps-tab-list li > a[href="' + target + '"]').closest('li').addClass('active');
      $(target).addClass('active');
      $(target).siblings('.ps-tab').removeClass('active');
      $(target).closest('.ps-tab-root').find('li').removeClass('active');
      $(target).closest('.ps-tab-root').find('li a[href="' + target + '"]').closest('li').addClass('active');
      $('html, body').animate({
        scrollTop: $(target).offset().top - $('.header--product .navigation').height() - 165 + 'px'
      }, 800);
    });
  });
})(jQuery);
/******/ })()
;