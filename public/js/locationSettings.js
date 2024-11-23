jQuery(document).ready(function($){
  assignLocationSettingsListeners();
});


let debounceTimeout;
function assignLocationSettingsListeners(){
  $(".optionalSettings").on('input', function(){
    clearTimeout(debounceTimeout);
    
    debounceTimeout = setTimeout(() => {
      updateOptionalSettings($(this));
    }, 1000);
  });
}

function updateOptionalSettings(settingElement){
  jQuery.ajax({
    type: 'POST',
    url: getRoute("locationSettings"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      id: $(".showsec-options").data("optionId") || null,
      locationID: $("#locationID").val(),
      allowOnlineRegistrations: $("#enableOnlineRegistrations").prop('checked'),
      registrationFee: $("#registrationFeeInput").val(),
      allowUnstandardised: $("#allow-Unstandardised").prop('checked'),
      allowJunior: $("#allow-Junior").prop('checked'),
      allowAuction: $("#allow-Auction").prop('checked'),
      firstPrize : $("#prizeMoney-firstPlace").val(),
      secondPrize : $("#prizeMoney-secondPlace").val(),
      thirdPrize : $("#prizeMoney-thirdPlace").val(),
      pmBiSec : $("#prizeMoney-biSec").val(),
      pmBoSec : $("#prizeMoney-boSec").val(),
      pmBIS: $("#prizeMoney-bis").val(),
      pmBOA: $("#prizeMoney-boa").val(),
      auctionFee: $("#auctionFeeInput").val() || 0.0,
    }),
    success: function (data) {
      console.log(data);
      if(settingElement.hasClass("optionalClasses")){
        addOrDeleteOptionalClass(settingElement);
      }else{
        assignLocationSettingsListeners();
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest.responseText);
      console.log(errorThrown);
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
