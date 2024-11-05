function updateAdminTabs(/*adminTabsHtml, activeTabClass*/){
  console.log("Update Admin Tabs");
  //adminTabsHtml = jQuery.parseJSON(adminTabsHtmlJson);
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'getAdminTabsHtml',
    },
    success: function (data) {
      $(".adminTabs").replaceWith(data);
      assignTabListeners();
      assignEntrySummaryListeners();
      assignEntryBookListeners();
      assignPrizeCardsListeners();
      assignJudgesReportsListeners();

      selectTab();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}

function updateJudgingSheetsHtml(judgingSheetsHtml){
  $(".judgingSheets.content").replaceWith(judgingSheetsHtml);
}

function updateAbsenteesHtml(absenteesHtml){
  $(".absentees.content").replaceWith(absenteesHtml);
}
