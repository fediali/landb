/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/report.js ***!
  \******************************************************************/
$(document).ready(function () {
  BDashboard.loadWidget($('#revenue-report').find('.widget-content'), route('ecommerce.report.revenue'));
  $(document).on('click', '#revenue-report .btn-group .dropdown-menu a', function (event) {
    event.preventDefault();
    BDashboard.loadWidget($('#revenue-report').find('.widget-content'), $(event.currentTarget).prop('href'));
  });
  BDashboard.loadWidget($('#widget_ecommerce_report_general').find('.widget-content'), route('ecommerce.report.dashboard-widget.general'));
});
/******/ })()
;