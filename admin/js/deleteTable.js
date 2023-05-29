/**
* Displays a dialog when Delete Table Button is pressed
*
* When confirmed, calls 'deleteTable', which deletes the table database entry
*
*/
jQuery(document).ready(function($){
  $(".deleteButton").on('click',function(){
    var id= $(this).val();
    $( "#dialogText" ).dialog({
      buttons: {
        'Cancel': function() {
          $(this).dialog('close');
        },
        'Confirm': function() {
          jQuery.ajax({
            type: 'POST',
            url: my_ajax_obj.ajax_url,
            data: {
              _ajax_nonce: my_ajax_obj.nonce,
              action: 'deleteTable',
              id: id,
            },
            success: function (data) {
              location.reload();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
              alert(errorThrown);
            }
          });

        }
      }
    });
  });
});
