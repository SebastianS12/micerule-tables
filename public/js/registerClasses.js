function registerClasses(){
  var classRegistrations = [];

  $(".registrationInput").each(function(){
    var inputVal = $(this).find("input").val();
    if(inputVal == ""){
      inputVal = 0;
    }

    var classIndex = $(this).data("classIndex");

    const registrationData = {classIndex : classIndex, registrationCount : inputVal,};
    classRegistrations.push(registrationData);
  });

  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: getRoute("registrations"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      eventPostID: miceruleApi.eventPostID,
      classRegistrations: classRegistrations,
      userName: $("#userSelectRegistration").val(),
    }),
    success: function (data) {
      $("#spinner-div").hide();
      displayRegisterModalHtml(data);
      updateAdminTabs();
    },
    error: function(xhr, status, error) {
      console.error('AJAX Error:', error);
    }
  });
}

function displayRegisterModalHtml(html){
  $("#registerModal").html(html);
  $("#registerModal").modal();
}
