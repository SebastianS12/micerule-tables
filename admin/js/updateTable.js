/**
* Updates Season Table when Update Table Button is pressed
*
* 'updateTable' goes through all Events in time span of the season
*  and accumulates the points again
*
*
*/
jQuery(document).ready(function($){
  $(".updateButton").on('click',function(){
    var id= $(this).val();
    if($("#Check"+id).prop('checked')){
      var checked = 1;
    }else{
      var checked = 0;
    }
    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'updateTable',
        id: id,
        checked: checked,
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
