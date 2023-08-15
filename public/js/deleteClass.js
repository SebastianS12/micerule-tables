jQuery(document).ready(function($){
  assignDeleteClassListener();
});

function assignDeleteClassListener(){
  $("#locationSectionTables").on('click', ".deleteClassButton", function(){
    var className = this.id.split("&-&")[0];
    deleteClass(className);
  });
}

function deleteClass(className){
  console.log(className);
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'deleteClass',
      id: $("#locationID").val(),
      className: className,
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
      alert(errorThrown);
    }
  });

}
