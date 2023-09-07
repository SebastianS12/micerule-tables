jQuery(document).ready(function($){
  assignEntrySummaryListeners();
});


function assignEntrySummaryListeners(){
  $(".setAllAbsent").on('change', function(){
    setAllAbsent($(this));
  });
}


function setAllAbsent(clickedBox){
  var userName = clickedBox.parents("div.fancier-entry-summary").find("p.fancier-name").text();

  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'setAllAbsent',
      userName: userName,
      checkValue: clickedBox.prop('checked'),
    },
    success: function (data) {
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}


function updateEntrySummaryHtml(entrySummaryHtml){
  $(".entrySummary.content").replaceWith(entrySummaryHtml);
  assignEntrySummaryListeners();
}
