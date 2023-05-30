jQuery(document).ready(function($){
  assignLocationSettingsListeners();
});

function assignLocationSettingsListeners(){
  $(".optionalSettings").on('change', function(){
    updateOptionalSettings($(this));
  });
}

function updateOptionalSettings(settingElement){
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
      allowJunior: $("#allow-Junior").prop('checked'),
      allowAuction: $("#allow-Auction").prop('checked'),
      firstPrize : $("#prizeMoney-firstPlace").val(),
      secondPrize : $("#prizeMoney-secondPlace").val(),
      thirdPrize : $("#prizeMoney-thirdPlace").val(),
    },
    success: function (data) {
      if(settingElement.hasClass("optionalClasses")){
        addOptionalClass(settingElement);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}

function addOptionalClass(element){
  var optionalClassName = element.prop("id").split("allow-")[1];
  if(optionalClassName != null){
    if(element.prop('checked')){
      addClass("optional", optionalClassName);
    }else{
      var position = $("#"+optionalClassName+"-tr-location").find(".class-delete").prop("id").split("&-&")[0];
      deleteClass("optional", position, optionalClassName);
    }
  }
}
