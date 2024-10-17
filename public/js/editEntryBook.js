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
    let entryID = $(this).parents(".editEntry-td").data("entryId");

    openDeleteModal(entryID);
  });

  $(".placementCheck").on('change', function(){
    var prize = $(this).data("prize");
    var placement = $(this).data("placement");
    var indexID = $(this).parent().data("index-id");
    var entryID = $(this).parent().data("entry-id");

    editPlacement(prize, placement, indexID, entryID);
  });

  $(".BISCheck").on('change', function(){
    var prizeID = $(this).data("prize-id");
    var challengeIndexID = $(this).data("index-id");
    var oaChallengeIndexID = $(this).data("oa-index-id");

    editBIS(prizeID, challengeIndexID, oaChallengeIndexID);
  });

  $(".absentCheck").on('change', function(){
    let entryID = $(this).parents(".absent-td").data("entryId");

    editAbsent(entryID);
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


function openDeleteModal(entryID){
  var modalHtml = "<h2>Delete Entry</h2>";
  modalHtml += "<p>Are you sure you want to delete this entry?</p>";
  modalHtml += "<div class = 'button-row'><a class= 'button' id = 'confirmDeleteEntry'>Delete</a>";
  modalHtml += "<a class='button' id = 'cancelDeleteEntry'>Cancel</a></div>";

  $("#editEntryModal").html(modalHtml);
  $("#editEntryModal").modal();

  $("#confirmDeleteEntry").on('click', function(){
    deleteEntry(entryID);
  });

  $("#cancelDeleteEntry").on('click', function(){
    $.modal.close();
  });
}

function deleteEntry(entryID){
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'deleteEntry',
      entryID: entryID,
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

function editPlacement(prize, placement, indexID, entryID){
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'editPlacement',
      prize: prize,
      placement: placement,
      indexID: indexID,
      entryID: entryID,
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


function editBIS(prizeID, challengeIndexID, oaChallengeIndexID){
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'editBIS',
      prizeID: prizeID,
      challengeIndexID: challengeIndexID,
      oaChallengeIndexID: oaChallengeIndexID,
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

function editAbsent(entryID){
  console.log(entryID);
  $("#spinner-div").show();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'editAbsent',
      entryID: entryID,
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
