jQuery(document).ready(function($){
  assignAddClassListener();
});

function assignAddClassListener(){
  $(".addBreedButton").on('click',function(){
    var section = this.id.split("AddButton")[0];

    if($(".confirmClass").length == 0)
      getSelectClassRowHtml(section);
    // convertPostMeta();
  });
}

function getSelectHTML(selectOptionsHTML){
  var selectHTML = "<select class=' classSelect' name='Class Name'>";
  selectHTML += selectOptionsHTML;
  selectHTML += "</select>";

  return selectHTML;
}

function getSelectClassRowHtml(section){
  jQuery.ajax({
    type: 'GET',
    url: getRoute("getClassSelectOptionsHtml"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    data: {
      sectionName: section.toLowerCase(),
      locationID: $("#locationID").val(),
    },
    success: function (selectOptionsHTML) {
      var selectHTML = getSelectHTML(selectOptionsHTML);
      var tableID = '#table' + section + "-location" ;
      var position = $(tableID).find(".classRow-location").length;
      addSelectClassRowHtml(section, position, tableID, selectHTML);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest.responseText);
      console.log(errorThrown);
    }
  });
}

function addClass(section, className){
  jQuery.ajax({
    type: 'POST',
    url: getRoute("addClass"),
    data: {
      _wpnonce: miceruleApi.nonce,
      locationID: $("#locationID").val(),
      section: section.toLowerCase(),
      className: className,
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
}

function addSelectClassRowHtml(section, position, tableID, selectHTML){
  classSelectHtml = '<td class = "add-class-select" colspan = "2">'+selectHTML+'</td>';
  confirmButtonHtml = "<td class = 'confirmClass-td'><button type = 'button' class = 'confirmClass'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/confirmAdd.svg'></button></td>";
  cancelButtonHtml = "<td class = 'cancelClass-td'><button type = 'button' class = 'cancelClass'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/cancelAdd.svg'></button></td>";

  $(tableID + ' tr:last').before('<tr>' + classSelectHtml + confirmButtonHtml + cancelButtonHtml +'</tr>');
  $('.classSelect').select2({
    tags:true,
  });

  $(".confirmClass").on("click", function(){
    var className = $(this).closest('tr').find('.classSelect').val();
    if(className != "")
      addClass(section, className);
      //addClass(section, className, $(this).closest('tr'), tableID);
  });

  $(".cancelClass").on("click", function(){
    $(this).closest('tr').remove();
  });
}

function convertPostMeta(){
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'addClass',
    },
    success: function (data) {
      console.log(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}
