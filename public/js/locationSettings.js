jQuery(document).ready(function($){
  assignLocationSettingsListeners();
});


let debounceTimeout;
function assignLocationSettingsListeners(){
  $(".optionalSettings").on('input', function(){
    clearTimeout(debounceTimeout);
    
    debounceTimeout = setTimeout(() => {
      updateOptionalSettings($(this));
    }, 3000);
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
      console.log(data);
      if(settingElement.hasClass("optionalClasses")){
        addOrDeleteOptionalClass(settingElement);
      }else{
        assignLocationSettingsListeners();
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
      assignLocationSettingsListeners();
    }
  });
}

function addOrDeleteOptionalClass(element){
  var optionalClassName = element.prop("id").split("allow-")[1];
  if(optionalClassName != null){
    if(element.prop('checked')){
      addClass("optional", optionalClassName);
    }else{
      const classID = $("#"+optionalClassName+"-tr-location").data("classId");
      deleteClass(classID, "optional");
    }
  }
}
