jQuery(document).ready(function($){
  assignEntrySummaryListeners();
});


function assignEntrySummaryListeners(){
  $(".setAllAbsent").on('change', function(){
    setAllAbsent($(this));
  });
}


function setAllAbsent(clickedBox){
  var penNumbers = [];

  clickedBox.parents("div.fancier-entry-summary").find("td.js-pen-no").each(function(){
    penNumbers.push($(this).text());
  });

  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'setAllAbsent',
      penNumbers: penNumbers,
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
