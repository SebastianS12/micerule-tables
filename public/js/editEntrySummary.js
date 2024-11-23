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
    type: 'PUT',
    url: getRoute("setAllAbsent"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      userName: userName,
      absent: clickedBox.prop('checked'), // Ensures boolean is preserved
    }),
    success: function (data) {
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest.responseText);
      console.log(errorThrown);
    }
  });
}


function updateEntrySummaryHtml(entrySummaryHtml){
  $(".entrySummary.content").replaceWith(entrySummaryHtml);
  assignEntrySummaryListeners();
}
