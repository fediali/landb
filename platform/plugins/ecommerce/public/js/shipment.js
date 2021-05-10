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
});
/******/ })()
;