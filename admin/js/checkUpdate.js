/**
* Updates Season Table and sets or unsets the Table to displayed on Leaderboards, when
* Show on Leaderboard box is checked or unchecked 
*
* 'updateTable' goes through all Events in time span of the season
*  and accumulates the points again
*
*
*/
jQuery(document).ready(function($){
  $(".checkUpdate").on('change',function(){
    var id= $(this).val();
    if($("#Check"+id).prop('checked')){
      var checked = 1;
    }else{
      var checked = 0;
      console.log($(this).val());
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
        console.log(data);

      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
});
