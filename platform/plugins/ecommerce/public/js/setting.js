/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*******************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/setting.js ***!
  \*******************************************************************/
$(document).ready(function () {
  $(document).on('keyup', '#store_order_prefix', function (event) {
    if ($(event.currentTarget).val()) {
      $('.sample-order-code-prefix').text($(event.currentTarget).val() + '-');
    } else {
      $('.sample-order-code-prefix').text('');
    }
  });
  $(document).on('keyup', '#store_order_suffix', function (event) {
    if ($(event.currentTarget).val()) {
      $('.sample-order-code-suffix').text('-' + $(event.currentTarget).val());
    } else {
      $('.sample-order-code-suffix').text('');
    }
  });
});
/******/ })()
;