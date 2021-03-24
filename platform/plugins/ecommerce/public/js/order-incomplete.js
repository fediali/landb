/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/order-incomplete.js ***!
  \****************************************************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var OrderIncompleteManagement = /*#__PURE__*/function () {
  function OrderIncompleteManagement() {
    _classCallCheck(this, OrderIncompleteManagement);
  }

  _createClass(OrderIncompleteManagement, [{
    key: "init",
    value: function init() {
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
      $(document).on('click', '.btn-trigger-send-order-recover-modal', function (event) {
        event.preventDefault();
        $('#confirm-send-recover-email-button').data('action', $(event.currentTarget).data('action'));
        $('#send-order-recover-email-modal').modal('show');
      });
      $(document).on('click', '#confirm-send-recover-email-button', function (event) {
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

            $('#send-order-recover-email-modal').modal('hide');
          },
          error: function error(res) {
            Botble.handleError(res);

            _self.removeClass('button-loading');
          }
        });
      });
    }
  }]);

  return OrderIncompleteManagement;
}();

$(document).ready(function () {
  new OrderIncompleteManagement().init();
});
/******/ })()
;