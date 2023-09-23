/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************!*\
  !*** ./resources/js/dashboard.js ***!
  \***********************************/
$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  }); //count delivery success

  $.ajax({
    type: "get",
    url: "/admin/count-delivery-success",
    success: function success(e) {
      $('#delivery').html(e.data);
    }
  }); //count customer

  $.ajax({
    type: "get",
    url: "/admin/count-customer",
    success: function success(e) {
      $('#customer').html(e.data);
    }
  }); //count order

  $.ajax({
    type: "get",
    url: "/admin/count-order",
    success: function success(e) {
      $('#order').html(e.data);
    }
  });
});
/******/ })()
;