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
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'deleteClass',
      id: $("#locationID").val(),
      classID: classID,
      section: section,
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
