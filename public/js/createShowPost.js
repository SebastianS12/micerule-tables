jQuery(document).ready(function($){
  $("#create-show-post").on('click', function(){
    console.log("Create");

    jQuery.ajax({
      type: 'POST',
      url: getRoute("showPost"),
      beforeSend: function ( xhr ) {
        xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
      },
      contentType: 'application/json',
      success: function (data) {
        if(data != ""){
          $("#create-show-post").after("<a href = '" + data + "'>Show Report Draft</a>")
        }else{
          alert("Something went wrong");
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
});
