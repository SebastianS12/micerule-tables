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
      console.log(data);
      $(".adminTabs").replaceWith(data);
      assignTabListeners();
      assignEntrySummaryListeners();
      assignEntryBookListeners();
      assignPrizeCardsListeners();
      assignJudgesReportsListeners();

      selectTab();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });


  //updateLabelHtml(adminTabsHtml["Label"]);
  //updateEntrySummaryHtml(adminTabsHtml["Entry Summary"]);
  //updateJudgingSheetsHtml(adminTabsHtml["Judging Sheets"]);
  //updateEntryBookHtml(adminTabsHtml["Entry Book"]);
  //updateAbsenteesHtml(adminTabsHtml["Absentees"]);
  //updatePrizeCardsHtml(adminTabsHtml["Prize Cards"]);
  //updateJudgesReportsHtml(adminTabsHtml["Judges Reports"]);

  //jQuery(activeTabClass).show();
}

function updateJudgingSheetsHtml(judgingSheetsHtml){
  $(".judgingSheets.content").replaceWith(judgingSheetsHtml);
}

function updateAbsenteesHtml(absenteesHtml){
  $(".absentees.content").replaceWith(absenteesHtml);
}
