jQuery(document).ready(function($){
  assignDeleteClassListener();
});

function assignDeleteClassListener(){
  $("#locationSectionTables").on('click', ".deleteClassButton", function(){

    var section = this.id.split("&-&")[0];
    var className = this.id.split("&-&")[1];
    var position = this.id.split("&-&")[2];

    deleteClass(section, position, className);
  });
}

function deleteClass(section, position, className){
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'deleteClass',
      id: $("#locationID").val(),
      section: section.toLowerCase(),
      className: className,
      position: position,
    },
    success: function (data) {
      console.log(data);
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

function updateRowIds(className, tableID){
  $("#"+className+"-tr-location").nextAll(".classRow-location").each(function(){
    if($(this).find(".deleteClassButton").length){
      var splitID = $(this).find(".deleteClassButton").attr("id").split("&-&");
      var newPosition = parseInt(splitID[2]) - 1;

      $($(this).find(".moveClassButton")[0]).attr("id", getIDStringNewPosition($($(this).find(".moveClassButton")[0]).attr("id"), newPosition));
      $($(this).find(".moveClassButton")[0]).attr("class", "moveClassButton "+ ((newPosition > 0) ? 'active' :''));
      $($(this).find(".moveClassButton")[1]).attr("id", getIDStringNewPosition($($(this).find(".moveClassButton")[1]).attr("id"), newPosition));
      $($(this).find(".moveClassButton")[1]).attr("class", "moveClassButton "+ ((newPosition < $(tableID).find(".classRow").length - 3) ? 'active' :''));
      $(this).find(".deleteClassButton").attr("id", getIDStringNewPosition($(this).find(".deleteClassButton").attr("id"), newPosition));
    }
  });
}

function updateRowPositions(){
  var position = 1;
  $(".classRow-location").each(function(){
    $(this).find(".positionCell").text(position + "/" + (position + 1));
    position += 2;
  });
}

function deleteTableRow(className, section){
  var tableID = '#table' + section + "-location";
  updateRowIds($.escapeSelector(className), tableID);
  $("#"+className+"-tr-location").remove();
  updateRowPositions();
}

function getIDStringNewPosition(id, newPosition){
  var splitID = id.split("&-&");

  return splitID[0]+"&-&"+splitID[1]+"&-&"+newPosition+"&-&"+splitID[3];
}

function getIDStringNewClassName(id, className){
  splitID = id.split("&-&");

  return splitID[0]+"&-&"+className+"&-&"+splitID[2]+"&-&"+splitID[3];
}
