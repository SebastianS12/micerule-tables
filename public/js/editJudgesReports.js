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
    var entryID = $(this).data('entry-id');
    console.log(entryID);
    if($(this).val() !== '' && entryID != undefined && entryID != ''){
      setCustomClassVariety(entryID, $(this).val())
    }
  });
}

function submitReport(buttonElement){
  var commentID = buttonElement.parent().data('comment-id') || null;
  var indexID = buttonElement.parent().data('index-id');
  var classComment = buttonElement.parent().find(".jr-class-report").val();
  var placementReportData = [];
  buttonElement.prev().find(".placement-report").each(function(){
    var id = $(this).data('report-id') || null;
    var placementID = $(this).data('placement-id');
    var buckChecked = $(this).find(".buck").eq(0).prop('checked');
    var doeChecked = $(this).find(".doe").eq(0).prop('checked');
    var comment = $(this).find(".jr-report").val();
    placementReportData.push({id: id, placementID : placementID, buckChecked : buckChecked, doeChecked : doeChecked, comment : comment});
  });

  
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: getRoute("classReport"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      eventPostID: miceruleApi.eventPostID,
      commentID: commentID,
      indexID: indexID,
      comment: classComment,
      placementReports : placementReportData,
    }),
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
  var commentID = buttonElement.parent().data('comment-id') || null;
  var judgeID = buttonElement.parent().data('judge-id');
  var comment = buttonElement.prev().find("textarea").eq(0).val();
  jQuery.ajax({
    type: 'POST',
    url: getRoute("generalComment"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      eventPostID: miceruleApi.eventPostID,
      commentID: commentID,
      judgeID: judgeID,
      comment: comment,
    }),
    success: function (data) {
      console.log(data);
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

function updateJudgesReportsHtml(judgesReportsHtml){
  $(".judgesReport.content").replaceWith(judgesReportsHtml);
  assignJudgesReportsListeners();
}
