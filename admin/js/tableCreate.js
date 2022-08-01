/**
* Calls 'tableCreate' to Create new SeasonTable when Create Table Button clicked
*
* dateFrom: starting date of Season
* dateTo: ending date of Season
*
*/
jQuery(document).ready(function($){
  $("#tableCreate").on('click',function(){
    var dateFrom= $("#datepicker-1").datepicker("getDate")/1000;
    var dateTo= $("#datepicker-2").datepicker("getDate")/1000;
    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'tableCreate',
        dateFrom: dateFrom,
        dateTo: dateTo,
      },
      success: function (data) {

        location.reload();
        console.log(data);

      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
});
