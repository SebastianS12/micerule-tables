function setCustomClassVariety(entryID, varietyName, tab){
  jQuery.ajax({
    type: 'PUT',
    url: getRoute("editVarietyName"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      eventPostID: miceruleApi.eventPostID,
      entryID: entryID,
      varietyName: varietyName,
    }),
    success: function (data) {
      console.log(data);
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
