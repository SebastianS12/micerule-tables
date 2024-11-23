jQuery(document).ready(function($){
  assignDeleteClassListener();
});

function assignDeleteClassListener(){
  $("#locationSectionTables").on('click', ".deleteClassButton", function(){
    var classID = $(this).data("classId");
    const section = $(this).data("section");
    deleteClass(classID, section);
  });
}

function deleteClass(classID, section){
  jQuery.ajax({
    type: 'DELETE',
    url: getRoute("deleteClass"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    data: {
      locationID: $("#locationID").val(),
      classID: classID,
      sectionName: section,
    },
    success: function (data) {
      /*
      if(data == 0)
        alert("Class cannot be deleted because there are mice registered!");
      if(data == 1)
        deleteTableRow(className, section);
        */
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
}
