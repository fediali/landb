/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/store-locator.js ***!
  \*************************************************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var StoreLocatorManagement = /*#__PURE__*/function () {
  function StoreLocatorManagement() {
    _classCallCheck(this, StoreLocatorManagement);
  }

  _createClass(StoreLocatorManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.btn-trigger-show-store-locator', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);
        var $modalBody;

        if ($current.data('type') === 'update') {
          $modalBody = $('#update-store-locator-modal .modal-body');
        } else {
          $modalBody = $('#add-store-locator-modal .modal-body');
        }

        $.ajax({
          url: $current.data('load-form'),
          type: 'GET',
          beforeSend: function beforeSend() {
            $current.addClass('button-loading');
            $modalBody.html('');
          },
          success: function success(res) {
            if (res.error) {
              Botble.showError(res.message);
            } else {
              $modalBody.html(res.data);
              Botble.initResources();
              $modalBody.closest('.modal.fade').modal('show');
            }

            $current.removeClass('button-loading');
          },
          complete: function complete() {
            $current.removeClass('button-loading');
          },
          error: function error(data) {
            $current.removeClass('button-loading');
            Botble.handleError(data);
          }
        });
      });

      var createOrUpdateStoreLocator = function createOrUpdateStoreLocator(_self) {
        _self.addClass('button-loading');

        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.closest('.modal-content').find('form').prop('action'),
          data: _self.closest('.modal-content').find('form').serialize(),
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('.store-locator-wrap').load(window.location.href + ' .store-locator-wrap > *');

              _self.removeClass('button-loading');

              _self.closest('.modal.fade').modal('hide');
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
      };

      $(document).on('click', '#add-store-locator-button', function (event) {
        event.preventDefault();
        createOrUpdateStoreLocator($(event.currentTarget));
      });
      $(document).on('click', '#update-store-locator-button', function (event) {
        event.preventDefault();
        createOrUpdateStoreLocator($(event.currentTarget));
      });
      $(document).on('click', '.btn-trigger-delete-store-locator', function (event) {
        event.preventDefault();
        $('#delete-store-locator-button').data('target', $(event.currentTarget).data('target'));
        $('#delete-store-locator-modal').modal('show');
      });
      $(document).on('click', '#delete-store-locator-button', function (event) {
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
              $('.store-locator-wrap').load(window.location.href + ' .store-locator-wrap > *');

              _self.removeClass('button-loading');

              _self.closest('.modal.fade').modal('hide');
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
      $(document).on('click', '#change-primary-store-locator-button', function (event) {
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
              $('.store-locator-wrap').load(window.location.href + ' .store-locator-wrap > *');

              _self.removeClass('button-loading');

              _self.closest('.modal.fade').modal('hide');
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

  return StoreLocatorManagement;
}();

$(document).ready(function () {
  new StoreLocatorManagement().init();
});
/******/ })()
;