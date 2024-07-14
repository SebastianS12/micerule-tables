jQuery(document).ready(function($){
  assignEntryBookListeners();
});


function assignEntryBookListeners(){
  $(".moveEntry").on('click', function(){
    var penNumber = this.id.split("&-&")[0];

    openMoveModal(penNumber);
  });

  $(".addEntry").on('click', function(){
    openAddModal();
  });

  $(".deleteEntry").on('click', function(){
    var penNumber = this.id.split("&-&")[0];

    openDeleteModal(penNumber);
  });


  $(".placementCheck").on('change', function(){
    var prize = this.id.split("&-&")[0];
    var placement = this.id.split("&-&")[1];
    var entryID = this.id.split("&-&")[2];
    var checkValue = $(this).prop('checked');

    editPlacement(prize, placement, entryID, checkValue)
  });

  $(".BISCheck").on('change', function(){
    var prize = this.id.split("&-&")[0];
    var age = this.id.split("&-&")[1];
    var section = this.id.split("&-&")[2];
    var checkValue = $(this).prop('checked'); //negate because prop is already new value

    editBIS(prize, age, section, checkValue);
  });

  $(".absentCheck").on('change', function(){
    var penNumber = this.id.split("&-&")[0];
    var checkValue = $(this).prop('checked');

    editAbsent(penNumber, checkValue);
  });

  $(".classSelect-entryBook").on('change', function(){
    var entryID = this.id.split("&-&")[0];
    var varietyName = $(this).val();

    setCustomClassVariety(entryID, varietyName, ".entryBook.content");
  });

  $(".unstandardised-input").on('keyup', debounce((eventObject) => {
    var entryID = eventObject.currentTarget.id.split("&-&")[0];
    var varietyName = $(eventObject.currentTarget).val();

    setCustomClassVariety(entryID, varietyName);
  }, 1000));
}

function debounce(callback, wait) {
  let timeout;
  return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(function () { callback.apply(this, args); }, wait);
  };
}


function openMoveModal(penNumber){
  additionalHtml = "<a class = 'button' id = 'confirmMoveModal'>Move</a>";
  openEditModal("Move Entry to:", additionalHtml);

  $("#confirmMoveModal").on('click', function(){
    console.log($("#ageSelect").find('option').filter(":selected").val());
    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'moveEntry',
        newSection: $("#sectionSelect").find('option').filter(":selected").val().toLowerCase(),
        newClassName: $("#classSelect").find('option').filter(":selected").val(),
        newAge: $("#ageSelect").find('option').filter(":selected").val(),
        penNumber: penNumber,
      },
      success: function (data) {
        $.modal.close();
        $("#editEntryModal").remove();
        updateAdminTabs();
        console.log(data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
}


function openAddModal(){
  var additionalHtml = "<select id = 'userSelect'>";
  $("#userSelectRegistration option").each(function(){
    additionalHtml += "<option value = '"+$(this).text()+"'>" + $(this).text() + "</option>";
  });
  additionalHtml += "</select>";
  additionalHtml += "<button type = 'button' id = 'confirmAddModal'>Confirm</button>";
  openEditModal("Add Entry to:", additionalHtml);

  $("#confirmAddModal").on('click', function(){
    $("#spinner-div").show();
    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'addEntry',
        section: $("#sectionSelect").find('option').filter(":selected").val().toLowerCase(),
        className: $("#classSelect").find('option').filter(":selected").val(),
        age: $("#ageSelect").find('option').filter(":selected").val(),
        userName: $("#userSelect").find('option').filter(":selected").val(),
      },
      success: function (data) {
        $("#spinner-div").hide();
        $.modal.close();
        $("#editEntryModal").remove();
        updateAdminTabs();
        console.log(data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
}


function openEditModal(title, additionalHtml){
  //TODO:: file for constants
  const sectionNames = ["SELFS", "TANS", "MARKED", "SATINS", "AOVS", "OPTIONAL"];
  var html = "<div class = 'modal-content'><h2>"+title+"</h2>";

  html += "<select id = 'sectionSelect'>";
  sectionNames.forEach(function(sectionName){
    html += "<option value = '"+sectionName+"'>"+sectionName+"</option>"
  })
  html += "<option value = 'optional'>Optional</option>";
  html += "</select>";

  html += "<select id = 'classSelect'>";
  $("#" + sectionNames[0].toLowerCase() + "-registrationTable").find(".classNameCell:visible").not(".challenge").each(function(){
    html += "<option value = '"+$(this).text() + "'>"+$(this).text()+"</option>";
  })
  html += "</select>";

  html += "<select id = 'ageSelect'><option value = 'Ad'>Ad</option><option value = 'U8'>U8</option><option value = 'AA' disabled = 'disabled'>AA</option></select></div>";

  html += additionalHtml;
  //html += "<input type = 'button' id = 'confirmEditModal'>Move</input>";
  $("#editEntryModal").html(html);
  $("#editEntryModal").modal();

  $("#sectionSelect").on('change', function(){
    $("#classSelect").empty();
    $("#"+$.escapeSelector($(this).val().toLowerCase() + "-registrationTable")).find(".classNameCell:visible").not(".challenge").each(function(){
      var className = $(this).parent().attr("id").split("-tr")[0];
      if(className.toLowerCase() != "junior"){
        $("#classSelect").append($("<option></option>").attr("value", className).text($(this).text()));
      }
    })

    //lock age select for optional classes
    if($(this).val() === 'optional'){
      $("#ageSelect option[value='AA']").prop('disabled', false);
      $("#ageSelect option[value='AA']").prop('selected', true);
      $('#ageSelect :not(:selected)').prop('disabled',true);
    }else{
      $("#ageSelect option[value='Ad']").prop('selected', true);
      $("#ageSelect option[value='AA']").prop('disabled', true);
      $("#ageSelect :not(option[value='AA']").prop('disabled', false);
    }
  });


}


function openDeleteModal(penNumber){
  var modalHtml = "<h2>Delete Entry</h2>";
  modalHtml += "<p>Are you sure you want to delete this entry?</p>";
  modalHtml += "<div class = 'button-row'><a class= 'button' id = 'confirmDeleteEntry'>Delete</a>";
  modalHtml += "<a class='button' id = 'cancelDeleteEntry'>Cancel</a></div>";

  $("#editEntryModal").html(modalHtml);
  $("#editEntryModal").modal();

  $("#confirmDeleteEntry").on('click', function(){
    deleteEntry(penNumber);
  });

  $("#cancelDeleteEntry").on('click', function(){
    $.modal.close();
  });
}

function deleteEntry(penNumber){
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'deleteEntry',
      penNumber: penNumber,
    },
    success: function (data) {
      $("#spinner-div").hide();
      updateAdminTabs();
      $.modal.close();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}

function editPlacement(prize, placement, entryID, checkValue){
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'editPlacement',
      prize: prize,
      placement: placement,
      entryID: entryID,
      checkValue: checkValue,
    },
    success: function (data) {
      $("#spinner-div").hide();
      updateAdminTabs();
      console.log(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}


function editBIS(prize, age, section, checkValue){
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'editBIS',
      prize: prize,
      section: section.toLowerCase(),
      age: age,
      checkValue: checkValue,
    },
    success: function (data) {
      $("#spinner-div").hide();
      updateAdminTabs();
      console.log(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}

function editAbsent(penNumber, checkValue){
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'editAbsent',
      penNumber: penNumber,
      checkValue: checkValue,
    },
    success: function (data) {
      $("#spinner-div").hide();
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}


function updateEntryBookHtml(entryBookHtml){
  $(".entryBook.content").replaceWith(entryBookHtml);
  assignEntryBookListeners();
}
