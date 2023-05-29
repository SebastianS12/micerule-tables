const sectionNames = ['SELFS', 'TANS', 'MARKED', 'SATINS', 'AOVS'];

jQuery(document).ready(function($){

  //var currentAdRegistrations = [];

  $(".registerClassesButton").on('click',function(){

    var classRegistrations = [];
    var optionalClassRegistrations = [];

    $(".registrationInput").each(function(){
      var inputVal = $(this).find("input").val();
      if(inputVal == ""){
        inputVal = 0;
      }

      var className = this.id.split("&-&")[0];
      var classIndex = this.id.split("&-&")[1];
      var age = this.id.split("&-&")[2];

      const registrationData = {className : className, classIndex: classIndex, age: age, registrationCount : inputVal,};
      classRegistrations.push(registrationData);
    });

    $(".registrationInput-optionalClass").each(function(){
      var inputVal = $(this).find("input").val();
      if(inputVal == ""){
        inputVal = 0;
      }
      var className = this.id.split("&-&")[0];
      var classIndex = this.id.split("&-&")[1];
      var age = this.id.split("&-&")[2];

      const registrationData = {className : className, classIndex: classIndex, age: age, registrationCount : inputVal,};
      optionalClassRegistrations.push(registrationData);
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
        //displayRegisterModalHtml(adRegistrations, u8Registrations, $("#userSelectRegistration").val());
        //updateRegistrationOverview(jQuery.parseJSON(data));
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
        console.log("Fail");
      }
    });

  //displayRegisterModalHtml(adRegistrations, u8Registrations, $("#userSelectRegistration").val());
});

});

function displayRegisterModalHtml(html){
  $("#registerModal").html(html);
  $("#registerModal").modal();
}
