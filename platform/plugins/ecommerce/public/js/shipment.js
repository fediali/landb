/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/shipment.js ***!
  \********************************************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var ShipmentManagement = /*#__PURE__*/function () {
  function ShipmentManagement() {
    _classCallCheck(this, ShipmentManagement);
  }

  _createClass(ShipmentManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.shipment-actions .applist-menu a', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        $('#confirm-change-shipment-status-button').data('target', _self.data('target')).data('status', _self.data('value'));
        var $modal = $('#confirm-change-status-modal');
        $modal.find('.shipment-status-label').text(_self.text().toLowerCase());
        $modal.modal('show');
      });
      $(document).on('click', '#confirm-change-shipment-status-button', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.data('target'),
          data: {
            status: _self.data('status')
          },
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('.max-width-1200').load(window.location.href + ' .max-width-1200 > *', function () {
                $('#confirm-change-status-modal').modal('hide');

                _self.removeClass('button-loading');
              });
            } else {
              Botble.showError(res.message);

              _self.removeClass('button-loading');
            }
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
    }
  }]);

  return ShipmentManagement;
}();

$(document).ready(function () {
  new ShipmentManagement().init();
  $(document).scannerDetection({
    timeBeforeScanTest: 200,
    // wait for the next character for upto 200ms
    avgTimeByChar: 40,
    // it's not a barcode if a character takes longer than 100ms
    preventDefault: false,
    endChar: [13],
    onComplete: function onComplete(barcode, qty) {
      validScan = true;
      $('#scannerInput').val(barcode);
    },
    onError: function onError(string, qty) {
      console.log('Something went wrong. Try again!');
    }
  });
  $(document).ready(function () {
    var input = document.getElementById("scannerInput"); // Execute a function when the user releases a key on the keyboard

    input.addEventListener("keyup", function (event) {
      // Number 13 is the "Enter" key on the keyboard
      if (event.keyCode === 13) {
        // Cancel the default action, if needed
        event.preventDefault();
        var $t = $(event.currentTarget);
        $t.addClass('loading');
        $('#product-error').hide();
        $('#scannerInput').removeClass('is-invalid');
        get_product_by_barcode(this.value, $t);
      }
    });
    var form = document.getElementsByTagName("form");
    form[0].addEventListener("keydown", function (event) {
      if (event.keyCode === 13) {
        event.preventDefault();
      }
    });
  });

  function get_product_by_barcode(barcode, loader) {
    $.ajax({
      type: "GET",
      url: "{{ url('/admin/orders/verify-product-shipment-barcode/'.$orderProduct->order_id.'/') }}" + barcode,
      success: function success(result) {
        if (result.status == 'success') {
          loader.removeClass('loading');
          window.location.reload();
        }
      },
      error: function error(result) {
        $('#product-error').html('Product Not found!');
        $('#product-error').show();
        $('#scannerInput').addClass('is-invalid');
        loader.removeClass('loading');
      }
    });
  }
});
/******/ })()
;