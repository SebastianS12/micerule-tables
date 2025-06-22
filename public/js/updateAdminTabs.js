function updateAdminTabs(){
  console.log("Update Admin Tabs");
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      type: 'GET',
      url: getRoute("adminTabs"),
      beforeSend: function ( xhr ) {
        xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
      },
      data: {
        eventPostID: miceruleApi.eventPostID,
      },
      success: function (data) {
        console.log(data);
        $(".adminTabs").replaceWith(data);
        assignTabListeners();
        assignEntrySummaryListeners();
        assignEntryBookListeners();
        assignPrizeCardsListeners();
        assignJudgesReportsListeners();
  
        selectTab();
        resolve();
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log(errorThrown);
        reject(errorThrown);
      }
    });
  });
}

function updateJudgingSheetsHtml(judgingSheetsHtml){
  $(".judgingSheets.content").replaceWith(judgingSheetsHtml);
}

function updateAbsenteesHtml(absenteesHtml){
  $(".absentees.content").replaceWith(absenteesHtml);
}
