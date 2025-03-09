jQuery(document).ready(function($){
  assignLocationSettingsListeners();
});


function assignLocationSettingsListeners(){
  $("#update-show-options-btn").on('click', function(){
      updateOptionalSettings();
  });
}

function updateOptionalSettings(){
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
      location.reload();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest.responseText);
      console.log(errorThrown);
      alert("Something went wrong");
    }
  });
}
