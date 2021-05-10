/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/utilities.js ***!
  \*********************************************************************/
$(document).ready(function () {
  if ($.fn.datepicker) {
    $('#date_of_birth').datepicker({
      format: 'yyyy-mm-dd',
      orientation: 'bottom'
    });
  }

  $('#avatar').on('change', function (event) {
    var input = event.currentTarget;

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('.userpic-avatar').attr('src', e.target.result);
      };

      reader.readAsDataURL(input.files[0]);
    }
  });
});
/******/ })()
;