function setCustomClassVariety(entryID, varietyName, tab){
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'setCustomClassVariety',
      entryID: entryID,
      varietyName: varietyName,
    },
    success: function (data) {
      console.log(data);
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
