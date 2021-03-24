/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!**************************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/payment-method.js ***!
  \**************************************************************************/


function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var PaymentMethodManagement = /*#__PURE__*/function () {
  function PaymentMethodManagement() {
    _classCallCheck(this, PaymentMethodManagement);
  }

  _createClass(PaymentMethodManagement, [{
    key: "init",
    value: function init() {
      $('.toggle-payment-item').off('click').on('click', function (event) {
        $(event.currentTarget).closest('tbody').find('.payment-content-item').toggleClass('hidden');
      });
      $('.disable-payment-item').off('click').on('click', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        $('#confirm-disable-payment-method-modal').modal('show');
        $('#confirm-disable-payment-method-button').on('click', function (event) {
          event.preventDefault();
          $(event.currentTarget).addClass('button-loading');
          $.ajax({
            type: 'POST',
            cache: false,
            url: $('div[data-disable-payment-url]').data('disable-payment-url'),
            data: {
              type: _self.closest('form').find('.payment_type').val()
            },
            success: function success(res) {
              if (!res.error) {
                _self.closest('tbody').find('.payment-name-label-group').addClass('hidden');

                _self.closest('tbody').find('.edit-payment-item-btn-trigger').addClass('hidden');

                _self.closest('tbody').find('.save-payment-item-btn-trigger').removeClass('hidden');

                _self.closest('tbody').find('.btn-text-trigger-update').addClass('hidden');

                _self.closest('tbody').find('.btn-text-trigger-save').removeClass('hidden');

                _self.addClass('hidden');

                $('#confirm-disable-payment-method-modal').modal('hide');
                Botble.showSuccess(res.message);
              } else {
                Botble.showError(res.message);
              }

              $('#confirm-disable-payment-method-button').removeClass('button-loading');
            },
            error: function error(res) {
              Botble.handleError(res);
              $('#confirm-disable-payment-method-button').removeClass('button-loading');
            }
          });
        });
      });
      $('.save-payment-item').off('click').on('click', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: $('div[data-update-payment-url]').data('update-payment-url'),
          data: _self.closest('form').serialize(),
          success: function success(res) {
            if (!res.error) {
              _self.closest('tbody').find('.payment-name-label-group').removeClass('hidden');

              _self.closest('tbody').find('.method-name-label').text(_self.closest('form').find('input[name=name]').val());

              _self.closest('tbody').find('.disable-payment-item').removeClass('hidden');

              _self.closest('tbody').find('.edit-payment-item-btn-trigger').removeClass('hidden');

              _self.closest('tbody').find('.save-payment-item-btn-trigger').addClass('hidden');

              _self.closest('tbody').find('.btn-text-trigger-update').removeClass('hidden');

              _self.closest('tbody').find('.btn-text-trigger-save').addClass('hidden');

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
    }
  }]);

  return PaymentMethodManagement;
}();

$(document).ready(function () {
  new PaymentMethodManagement().init();
});
/******/ })()
;