jQuery(document).ready(function($){

  $("#unstandardised-row-toggle").on('click', function(){
    $("#unstandardisedRow").toggle();
    if($("#unstandardisedRow").css("display") == "none"){
      $("#unstandardisedRow").find(".fancier-select").val("");
      $("#unstandardisedRow").find(".variety-select").val("");
    }
  });

  $("#junior-row-toggle").on('click', function(){
    $("#juniorRow").toggle();
    if($("#juniorRow").css("display") == "none"){
      $("#juniorRow").find(".fancier-select").val("");
      $("#juniorRow").find(".variety-select").val("");
    }
  });
});
