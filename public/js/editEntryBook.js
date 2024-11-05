jQuery(document).ready(function($){
  assignEntryBookListeners();
});


function assignEntryBookListeners(){
  $(".moveEntry").on('click', function(){
    var entryID = $(this).parents(".editEntry-td").data("entryId");
    console.log(entryID);

    openMoveModal(entryID);
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


async function openMoveModal(entryID){
  additionalHtml = "<a class = 'button' id = 'confirmMoveModal'>Move</a>";
  await openEditModal("Move Entry to:", additionalHtml);

  $("#confirmMoveModal").on('click', function(){
    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'moveEntry',
        newClassIndexID: $("#classSelect").find('option').filter(":selected").val(),
        entryID: entryID,
      },
      success: function (data) {
        console.log(data);
        $.modal.close();
        $("#editEntryModal").remove();
        updateAdminTabs();
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
}


async function openAddModal(){
  var additionalHtml = "<select id = 'userSelect'>";
  $("#userSelectRegistration option").each(function(){
    additionalHtml += "<option value = '"+$(this).text()+"'>" + $(this).text() + "</option>";
  });
  additionalHtml += "</select>";
  additionalHtml += "<button type = 'button' id = 'confirmAddModal'>Confirm</button>";
  await openEditModal("Add Entry to:", additionalHtml);

  $("#confirmAddModal").on('click', function(){
    console.log($("#classSelect").find('option').filter(":selected").val());
    console.log($("#userSelect").find('option').filter(":selected").val());
    $("#spinner-div").show();
    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'addEntry',
        classIndexID: $("#classSelect").find('option').filter(":selected").val(),
        userName: $("#userSelect").find('option').filter(":selected").val(),
      },
      success: function (data) {
        $("#spinner-div").hide();
        $.modal.close();
        $("#editEntryModal").remove();
        updateAdminTabs();
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  });
}


async function openEditModal(title, additionalHtml){
  const selectOptions = await getEditSelectOptions();
  var html = "<div class = 'modal-content'><h2>"+title+"</h2>";

  html += "<select id = 'sectionSelect'>";
  $.each(selectOptions, function(sectionName){
    html += "<option value = '"+sectionName+"'>"+sectionName+"</option>";
  });
  html += "</select>";

  html += "<select id = 'classSelect'>";
  const classes = Object.values(selectOptions)[0];
  $.each(classes, function(_, selectOption){
    html += "<option value = '"+ selectOption.index_id + "'>"+ selectOption.classIndex +" - "+selectOption.className+" - "+selectOption.age+"</option>";
  });
  html += "</select>";

  html += additionalHtml;

  $("#editEntryModal").html(html);
  $("#editEntryModal").modal();

  $("#sectionSelect").on('change', function(){
    $("#classSelect").empty();
    const section = ($(this).val());
    $.each(selectOptions[section], function(_, selectOption){
      $("#classSelect").append($("<option></option>").attr("value", selectOption.index_id).text(selectOption.classIndex +" - "+selectOption.className+" - "+selectOption.age));
    });
  });
}

function getEditSelectOptions(){
  return new Promise((resolve, reject) => {
    jQuery.ajax({
        type: 'GET',
        url: my_ajax_obj.ajax_url,
        data: {
            _ajax_nonce: my_ajax_obj.nonce,
            action: 'getSelectOptions',
        },
        success: function (data) {
            resolve(data); // Pass data when the call succeeds
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            reject(errorThrown); // Handle errors by rejecting the Promise
        }
    });
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
