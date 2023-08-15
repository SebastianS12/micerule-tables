jQuery(document).ready(function($){
  assignMoveClassListener();
});

function assignMoveClassListener(){
  $("#locationSectionTables").on('click', ".moveClassButton", function(){
    var idString = this.id;
    var section = idString.split("&-&")[0];
    var firstClassName = idString.split("&-&")[1];
    var secondClassName = $(this).parents("tr").prev().find(".classNameCell").text();
    if(idString.split("&-&")[2] == 'moveDown')
      secondClassName = $(this).parents("tr").next().find(".classNameCell").text();

    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'moveClass',
        id: $("#locationID").val(),
        section: section.toLowerCase(),
        firstClassName: firstClassName,
        secondClassName: secondClassName,
      },
      success: function (data) {
        $("#locationSectionTables").replaceWith(data);
        assignLocationSettingsListeners();
        assignAddClassListener();
        assignDeleteClassListener();
        assignMoveClassListener();
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
}
