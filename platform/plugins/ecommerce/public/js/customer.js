/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/customer.js ***!
  \********************************************************************/
$(document).ready(function () {
  $(document).on('click', '#is_change_password', function (event) {
    if ($(event.currentTarget).is(':checked')) {
      $('input[type=password]').closest('.form-group').removeClass('hidden').fadeIn();
    } else {
      $('input[type=password]').closest('.form-group').addClass('hidden').fadeOut();
    }
  });
});
/******/ })()
;