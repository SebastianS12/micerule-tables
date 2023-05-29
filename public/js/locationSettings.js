jQuery(document).ready(function($){
    assignLocationSettingsListeners();
});

function assignLocationSettingsListeners(){
  $(".optionalSettings").on('change', function(){
    updateOptionalSettings();

    if($(this).hasClass("optionalClasses")){
      console.log("class");
      var optionalClassName = this.id.split("allow-")[1];
      if(optionalClassName != null){
        console.log($(this).prop('checked'));
        if($(this).prop('checked')){
          addClass("optional", optionalClassName);
        }else{
          var position = $("#"+optionalClassName+"-tr-location").find(".class-delete").prop("id").split("&-&")[0];
          deleteClass("optional", position, optionalClassName);
        }
      }
    }
  });

  $(".optionalSettings.optionalClasses").on('change', function(){

  });
}


function updateOptionalSettings(){
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'eventOptionalSettings',
      id: $("#locationID").val(),
      allowOnlineRegistrations: $("#enableOnlineRegistrations").prop('checked'),
      registrationFee: $("#registrationFeeInput").val(),
      allowUnstandardised: $("#allow-Unstandardised").prop('checked'),
      allowJuvenile: $("#allow-Juvenile").prop('checked'),
      allowAuction: $("#allow-Auction").prop('checked'),
      firstPrize : $("#prizeMoney-firstPlace").val(),
      secondPrize : $("#prizeMoney-secondPlace").val(),
      thirdPrize : $("#prizeMoney-thirdPlace").val(),
    },
    success: function (data) {
      console.log(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
