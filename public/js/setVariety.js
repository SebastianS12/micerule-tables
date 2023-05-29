function setCustomClassVariety(penNumber, selectValue, tab){
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'setCustomClassVariety',
      penNumber: penNumber,
      selectValue: selectValue,
    },
    success: function (data) {
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
