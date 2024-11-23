jQuery(document).ready(function($){
  assignMoveClassListener();
});

function assignMoveClassListener(){
  $("#locationSectionTables").on('click', ".moveClassButton", function(){
    const firstClassID = $(this).data("classId");
    const secondClassID = ($(this).data('moveAction') == 'moveDown') ? $(this).parents("tr").next().data("classId") : $(this).parents("tr").prev().data("classId");;

    jQuery.ajax({
      type: 'POST',
      url: getRoute("swapClasses"),
      beforeSend: function ( xhr ) {
        xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
      },
      data: {
        locationID: $("#locationID").val(),
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
