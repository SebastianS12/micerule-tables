jQuery(document).ready(function($){
  $("#create-show-post").on('click', function(){
    console.log("Create");

    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'createShowPost',
      },
      success: function (data) {
        alert(data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
});
