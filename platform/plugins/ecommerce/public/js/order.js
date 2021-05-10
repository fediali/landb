/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/order.js ***!
  \*****************************************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var OrderAdminManagement = /*#__PURE__*/function () {
  function OrderAdminManagement() {
    _classCallCheck(this, OrderAdminManagement);
  }

  _createClass(OrderAdminManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.btn-confirm-order', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.closest('form').prop('action'),
          data: _self.closest('form').serialize(),
          success: function success(res) {
            if (!res.error) {
              $('#main-order-content').load(window.location.href + ' #main-order-content > *');

              _self.closest('div').remove();

              Botble.showSuccess(res.message);
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-trigger-resend-order-confirmation-modal', function (event) {
        event.preventDefault();
        $('#confirm-resend-confirmation-email-button').data('action', $(event.currentTarget).data('action'));
        $('#resend-order-confirmation-email-modal').modal('show');
      });
      $(document).on('click', '#confirm-resend-confirmation-email-button', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.data('action'),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');

            $('#resend-order-confirmation-email-modal').modal('hide');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-trigger-shipment', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        var $form_body = $('.shipment-create-wrap');
        $form_body.toggleClass('hidden');

        if (!$form_body.hasClass('shipment-data-loaded')) {
          Botble.blockUI({
            target: $form_body,
            iconOnly: true,
            overlayColor: 'none'
          });
          $.ajax({
            url: _self.data('target'),
            type: 'GET',
            success: function success(res) {
              if (res.error) {
                Botble.showError(res.message);
              } else {
                $form_body.html(res.data);
                $form_body.addClass('shipment-data-loaded');
                Botble.initResources();
              }

              Botble.unblockUI($form_body);
            },
            error: function error(data) {
              Botble.handleError(data);
              Botble.unblockUI($form_body);
            }
          });
        }
      });
      $(document).on('change', '#store_id', function (event) {
        var $form_body = $('.shipment-create-wrap');
        Botble.blockUI({
          target: $form_body,
          iconOnly: true,
          overlayColor: 'none'
        });
        $('#select-shipping-provider').load($('.btn-trigger-shipment').data('target') + '?view=true&store_id=' + $(event.currentTarget).val() + ' #select-shipping-provider > *', function () {
          Botble.unblockUI($form_body);
          Botble.initResources();
        });
      });
      $(document).on('change', '.shipment-form-weight', function (event) {
        var $form_body = $('.shipment-create-wrap');
        Botble.blockUI({
          target: $form_body,
          iconOnly: true,
          overlayColor: 'none'
        });
        $('#select-shipping-provider').load($('.btn-trigger-shipment').data('target') + '?view=true&store_id=' + $('#store_id').val() + '&weight=' + $(event.currentTarget).val() + ' #select-shipping-provider > *', function () {
          Botble.unblockUI($form_body);
          Botble.initResources();
        });
      });
      $(document).on('click', '.table-shipping-select-options .clickable-row', function (event) {
        var _self = $(event.currentTarget);

        $('.input-hidden-shipping-method').val(_self.data('key'));
        $('.input-hidden-shipping-option').val(_self.data('option'));
        $('.input-show-shipping-method').val(_self.find('span.ws-nm').text());
      });
      $(document).on('click', '.btn-create-shipment', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.closest('form').prop('action'),
          data: _self.closest('form').serialize(),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('#main-order-content').load(window.location.href + ' #main-order-content > *');
              $('.btn-trigger-shipment').remove();
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-cancel-shipment', function (event) {
        event.preventDefault();
        $('#confirm-cancel-shipment-button').data('action', $(event.currentTarget).data('action'));
        $('#cancel-shipment-modal').modal('show');
      });
      $(document).on('click', '#confirm-cancel-shipment-button', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.data('action'),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('.carrier-status').addClass('carrier-status-' + res.data.status).text(res.data.status_text);
              $('#cancel-shipment-modal').modal('hide');
              $('#order-history-wrapper').load(window.location.href + ' #order-history-wrapper > *');
              $('.shipment-actions-wrapper').remove();
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-close-shipment-panel', function (event) {
        event.preventDefault();
        $('.shipment-create-wrap').addClass('hidden');
      });
      $(document).on('click', '.btn-trigger-update-shipping-address', function (event) {
        event.preventDefault();
        $('#update-shipping-address-modal').modal('show');
      });
      $(document).on('click', '#confirm-update-shipping-address-button', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.closest('.modal-content').find('form').prop('action'),
          data: _self.closest('.modal-content').find('form').serialize(),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('#update-shipping-address-modal').modal('hide');
              $('.shipment-address-box-1').html(res.data.line);
              $('.text-infor-subdued.shipping-address-info').html(res.data.detail);
              var $form_body = $('.shipment-create-wrap');
              Botble.blockUI({
                target: $form_body,
                iconOnly: true,
                overlayColor: 'none'
              });
              $('#select-shipping-provider').load($('.btn-trigger-shipment').data('target') + '?view=true #select-shipping-provider > *', function () {
                Botble.unblockUI($form_body);
                Botble.initResources();
              });
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-update-order', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.closest('form').prop('action'),
          data: _self.closest('form').serialize(),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-trigger-cancel-order', function (event) {
        event.preventDefault();
        $('#confirm-cancel-order-button').data('target', $(event.currentTarget).data('target'));
        $('#cancel-order-modal').modal('show');
      });
      $(document).on('click', '#confirm-cancel-order-button', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.data('target'),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('#main-order-content').load(window.location.href + ' #main-order-content > *');
              $('#cancel-order-modal').modal('hide');
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.btn-trigger-confirm-payment', function (event) {
        event.preventDefault();
        $('#confirm-payment-order-button').data('target', $(event.currentTarget).data('target'));
        $('#confirm-payment-modal').modal('show');
      });
      $(document).on('click', '#confirm-payment-order-button', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.data('target'),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('.page-content').load(window.location.href + ' .page-content > *');
              $('#confirm-payment-modal').modal('hide');
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
      $(document).on('click', '.show-timeline-dropdown', function (event) {
        event.preventDefault();
        $($(event.currentTarget).data('target')).slideToggle();
        $(event.currentTarget).closest('.comment-log-item').toggleClass('bg-white');
      });
      $(document).on('keyup', '.input-sync-item', function (event) {
        var number = $(event.currentTarget).val();

        if (!number || isNaN(number)) {
          number = 0;
        }

        $(event.currentTarget).closest('.page-content').find($(event.currentTarget).data('target')).text(Botble.numberFormat(parseFloat(number), 2));
      });
      $(document).on('click', '.btn-trigger-refund', function (event) {
        event.preventDefault();
        $('#confirm-refund-modal').modal('show');
      });
      $(document).on('change', '.j-refund-quantity', function () {
        var total_restock_items = 0;
        $.each($('.j-refund-quantity'), function (index, el) {
          var number = $(el).val();

          if (!number || isNaN(number)) {
            number = 0;
          }

          total_restock_items += parseFloat(number);
        });
        $('.total-restock-items').text(total_restock_items);
      });
      $(document).on('click', '#confirm-refund-payment-button', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.closest('.modal-dialog').find('form').prop('action'),
          data: _self.closest('.modal-dialog').find('form').serialize(),
          success: function success(res) {
            if (!res.error) {
              $('.page-content').load(window.location.href + ' .page-content > *');
              Botble.showSuccess(res.message);

              _self.closest('.modal').modal('hide');
            } else {
              Botble.showError(res.message);
            }

            _self.removeClass('button-loading');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
    }
  }]);

  return OrderAdminManagement;
}();

$(document).ready(function () {
  new OrderAdminManagement().init();
});
/******/ })()
;