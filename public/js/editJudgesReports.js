jQuery(document).ready(function($){
  assignJudgesReportsListeners();
});

function assignJudgesReportsListeners(){
  $(".submitReport").on('click', function(){
    submitReport($(this));
  });

  $(".submitGeneralComment").on('click', function(){
    submitGeneralComment($(this));
  });

  $(".classSelect-judgesReports").on('change', function(){
    var penNumber = this.id.split("&-&")[0];
    var selectValue = $(this).val();

    setCustomClassVariety(penNumber, selectValue, ".judgesReport.content");
  });
}

function submitReport(buttonElement){
  var className = buttonElement.prev().find(".jr-classData-li").find(".jr-classData-className").text();
  var age = buttonElement.prev().find(".jr-classData-li").find(".jr-classData-age").text();
  var classComments = buttonElement.parent().find(".jr-class-report").val();
  var placementReportData = [];

  buttonElement.prev().find(".jr-placement-tr").each(function(){
    var placement = this.id.split("&-&")[1];
    var buckChecked = $(this).find(".buck").eq(0).prop('checked');
    var doeChecked = $(this).find(".doe").eq(0).prop('checked');
    var reportText = $(this).find(".jr-report").val();

    placementReportData.push({placement : placement, buckChecked : buckChecked, doeChecked : doeChecked, reportText : reportText});
  })

  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'submitReport',
      className: className,
      age: age,
      classComments: classComments,
      placementReportData : JSON.stringify(placementReportData),
      submitType: "classReport",
    },
    success: function (data) {
      $("#spinner-div").hide();
      if(data != -1){
        buttonElement.text("Submitted")
        setTimeout(function() {
          buttonElement.text("Submit Changes");
        }, 3000);
      }else{
        alert("Something went wrong!");
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}

function submitGeneralComment(buttonElement){
  var judgeName = buttonElement.parentsUntil("tr").find(".jr-judge-name").text();
  var text = buttonElement.prev().find("textarea").eq(0).val();

  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'submitReport',
      judgeName: judgeName,
      text: text,
      submitType: "generalComment"
    },
    success: function (data) {
      $("#spinner-div").hide();
      buttonElement.text("Submitted")
      setTimeout(function() {
        buttonElement.text("Submit Changes");
      }, 3000);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}

function updateJudgesReportsHtml(judgesReportsHtml){
  $(".judgesReport.content").replaceWith(judgesReportsHtml);
  assignJudgesReportsListeners();
}
