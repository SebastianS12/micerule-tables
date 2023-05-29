jQuery(document).ready(function($){
  assignMoveClassListener();
});

function assignMoveClassListener(){
  $("#locationSectionTables").on('click', ".moveClassButton", function(){
    var idString = this.id;
    var section = idString.split("&-&")[0];
    var className = idString.split("&-&")[1];
    var position = idString.split("&-&")[2];
    var direction = (idString.split("&-&")[3] == 'moveUp') ? -1 : 1;

    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'moveClass',
        id: $("#locationID").val(),
        section: section.toLowerCase(),
        className: className,
        position: position,
        direction: direction,
      },
      success: function (data) {
        //adjustMoveButtons(className, position, direction, section);
        //swapRowIDs(className, direction, position);
        //swapRows($.escapeSelector(className), direction);
        //updateRowPositions();
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

function adjustMoveButtons(className, position, direction, section){
  //escape slash if className contains one
  className = $.escapeSelector(className);

  if(position == 0){
    $($("#"+className+"-tr-location").next().find(".moveClassButton")[0]).removeClass('active');
    $($("#"+className+"-tr-location").find(".moveClassButton")[0]).addClass('active');
  }

  if(position == 1 && direction == -1){
    $($("#"+className+"-tr-location").prev().find(".moveClassButton")[0]).addClass('active');
    $($("#"+className+"-tr-location").find(".moveClassButton")[0]).removeClass('active');
  }

  if($("#table"+section+"-location").find(".classRow-location").length - 2 == position){
    $($("#"+className+"-tr-location").prev().find(".moveClassButton")[1]).removeClass('active');
    $($("#"+className+"-tr-location").find(".moveClassButton")[1]).addClass('active');
  }

  if($("#table"+section+"-location").find(".classRow-location").length - 3 == position && direction == 1){
    $($("#"+className+"-tr-location").next().find(".moveClassButton")[1]).addClass('active');
    $($("#"+className+"-tr-location").find(".moveClassButton")[1]).removeClass('active');
  }
}

function getNewPositionIDString(id, direction){
  splitID = id.split("&-&");
  newPosition = parseInt(splitID[2]) + parseInt(direction);

  return splitID[0]+"&-&"+splitID[1]+"&-&"+newPosition+"&-&"+splitID[3];
}

function swapRowIDs(className, direction, position){
  //escape slash if className contains one
  className = $.escapeSelector(className);

  //$($("#"+className+"-tr").find(".registerBreedButton")[0]).attr("id", getIDString($("#"+className+"-tr").find(".registerBreedButton")[0].id, direction));
  $($("#"+className+"-tr-location").find(".moveClassButton")[0]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").find(".moveClassButton")[0].id, direction));
  $($("#"+className+"-tr-location").find(".moveClassButton")[1]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").find(".moveClassButton")[1].id, direction));
  $($("#"+className+"-tr-location").find(".deleteClassButton")[0]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").find(".deleteClassButton")[0].id, direction));

  if(direction == -1){
    $($("#"+className+"-tr-location").prev().find(".moveClassButton")[0]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").prev().find(".moveClassButton")[0].id, 1));
    $($("#"+className+"-tr-location").prev().find(".moveClassButton")[1]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").prev().find(".moveClassButton")[1].id, 1));
    $($("#"+className+"-tr-location").prev().find(".deleteClassButton")[0]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").prev().find(".deleteClassButton")[0].id, 1));
  }else{
    $($("#"+className+"-tr-location").next().find(".moveClassButton")[0]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").next().find(".moveClassButton")[0].id, -1));
    $($("#"+className+"-tr-location").next().find(".moveClassButton")[1]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").next().find(".moveClassButton")[1].id, -1));
    $($("#"+className+"-tr-location").next().find(".deleteClassButton")[0]).attr("id", getNewPositionIDString($("#"+className+"-tr-location").next().find(".deleteClassButton")[0].id, 1));
  }
}

function updateRowPositions(){
  var position = 1;
  $(".classRow-location").each(function(){
    $(this).find(".positionCell").text(position + "/" + (position + 1));
    position += 2;
  });
}

function swapRows(className, direction){
  if(direction == -1){
    jQuery("#"+className+"-tr-location").prev().before(jQuery("#"+className+"-tr-location"));
  }else{
    jQuery("#"+className+"-tr-location").next().after(jQuery("#"+className+"-tr-location"));   //swap row with row below
  }
}
