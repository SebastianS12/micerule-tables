function registerClasses(){
  var classRegistrations = [];
  var optionalClassRegistrations = [];

  $(".registrationInput").each(function(){
    var inputVal = $(this).find("input").val();
    if(inputVal == ""){
      inputVal = 0;
    }

    var classIndex = $(this).data("classIndex");

    const registrationData = {classIndex : classIndex, registrationCount : inputVal,};
    classRegistrations.push(registrationData);
  });

  $(".registrationInput-optionalClass").each(function(){
    var inputVal = $(this).find("input").val();
    if(inputVal == ""){
      inputVal = 0;
    }
    var className = this.id.split("&-&")[0];
    var age = this.id.split("&-&")[1];

    const registrationData = {className : className, age: age, registrationCount : inputVal,};
    // optionalClassRegistrations.push(registrationData);
    classRegistrations.push(registrationData);
  });

  
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'registerClasses',
      classRegistrations: classRegistrations,
      optionalClassRegistrations: optionalClassRegistrations,
      userName: $("#userSelectRegistration").val(),
      locationID: $("#locationID").val(),
    },
    success: function (data) {
      $("#spinner-div").hide();
      displayRegisterModalHtml(data);
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
      console.log("Fail");
    }
  });
}

function displayRegisterModalHtml(html){
  $("#registerModal").html(html);
  $("#registerModal").modal();
}
