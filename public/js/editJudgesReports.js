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
    /*
    var penNumber = this.id.split("&-&")[0];
    var selectValue = $(this).val();
    console.log(penNumber + selectValue);

    setCustomClassVariety(penNumber, selectValue, ".judgesReport.content");
    */
  });
}

function submitReport(buttonElement){
  var commentID = buttonElement.parent().data('comment-id');
  var indexID = buttonElement.parent().data('index-id');
  var classComment = buttonElement.parent().find(".jr-class-report").val();
  var placementReportData = [];
  buttonElement.prev().find(".placement-report").each(function(){
    var id = $(this).data('report-id');
    var placementID = $(this).data('placement-id');
    var buckChecked = $(this).find(".buck").eq(0).prop('checked');
    var doeChecked = $(this).find(".doe").eq(0).prop('checked');
    var comment = $(this).find(".jr-report").val();
    placementReportData.push({id: id, placementID : placementID, buckChecked : buckChecked, doeChecked : doeChecked, comment : comment});
  });

  
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'submitReport',
      commentID: commentID,
      indexID: indexID,
      classComment: classComment,
      placementReportData : JSON.stringify(placementReportData),
      submitType: "classReport",
    },
    success: function (data) {
      console.log(data);
      $("#spinner-div").hide();
      buttonElement.text("Submitted")
      setTimeout(function() {
        buttonElement.text("Submit Changes");
      }, 3000);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}

function submitGeneralComment(buttonElement){
  var commentID = buttonElement.parent().data('comment-id');
  var judgeID = buttonElement.parent().data('judge-id');
  var comment = buttonElement.prev().find("textarea").eq(0).val();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'submitReport',
      commentID: commentID,
      judgeID: judgeID,
      comment: comment,
      submitType: "generalComment"
    },
    success: function (data) {
      console.log(data);
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
