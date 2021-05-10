/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************************************!*\
  !*** ./platform/plugins/translation/resources/assets/js/theme-translations.js ***!
  \********************************************************************************/
$(document).ready(function () {
  $(document).on('click', '.button-save-theme-translations', function (event) {
    event.preventDefault();

    var _self = $(event.currentTarget);

    _self.addClass('button-loading');

    var $form = _self.closest('form');

    $.ajax({
      url: $form.prop('action'),
      type: 'POST',
      data: $form.serialize(),
      success: function success(data) {
        _self.removeClass('button-loading');

        if (data.error) {
          Botble.showError(data.message);
        } else {
          Botble.showSuccess(data.message);
          $form.removeClass('dirty');
        }
      },
      error: function error(data) {
        _self.removeClass('button-loading');

        Botble.handleError(data);
      }
    });
  });
});
/******/ })()
;