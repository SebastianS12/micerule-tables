jQuery(document).ready(function($){
  assignMoveClassListener();
});

function assignMoveClassListener(){
  $("#locationSectionTables").on('click', ".moveClassButton", function(){
    const firstClassID = $(this).data("classId");
    const secondClassID = ($(this).data('moveAction') == 'moveDown') ? $(this).parents("tr").next().data("classId") : $(this).parents("tr").prev().data("classId");;

    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'moveClass',
        id: $("#locationID").val(),
        firstClassID: firstClassID,
        secondClassID: secondClassID,
      },
      success: function (data) {
        $("#locationSectionTables").replaceWith(data);
        assignLocationSettingsListeners();
        assignAddClassListener();
        assignDeleteClassListener();
        assignMoveClassListener();
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log(errorThrown);
      }
    });
  });
}
