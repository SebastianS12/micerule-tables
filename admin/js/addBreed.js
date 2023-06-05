var sections = ["Selfs", "Marked", "Satins", "AOVs", "Tans"];
/**
* Opens Popover with Input Fields to add breed
*
*/
jQuery(document).ready(function ($) {

  $("#addBreedButton").on('click', function () {
    openAddBreedPopOver();
  });

  $("#overviewTable").on("click", ".submitPopoverAdd", function () {
    var name = $(".namePopover").val();
    var colour = $(".editColour").val();
    var section = $(".editSection").val();
    var iconSelectValue = $('#editSelectAdd').select2('data')[0].id;

    addBreedDBEntry(name, colour, section, iconSelectValue);
  });

  /**
   * helper function for creating html for the add breed popover
   * @returns popover html
   */
  function getPopOverHtml() {
    var html = "<div class='form-group'>";
    html += "<div class='breed-row'>";
    html += "<input type='text' class='namePopover' value=''>";

    var options = "";
    sections.forEach(function (value) {
      options += "<option value='" + value + "'>" + value + "</option>";
    });
    html += "<select class='editSection'>" + options + "</select>";
    html += "</div>";
    html += "<div class='popover-tabs'>";
    html += "<div id='colourTab' class='active'><span>Colour</span></div>";
    html += "<div style='width:2px; height: 100%; background:black;'></div>";
    html += "<div id='uploadTab' class='inactive'><span>Icon</span></div>";
    html += "</div>";
    html += "<div id='swap-pane'>";
    html += "<div id='colourEditTab'>";
    html += "<input type='text' class='editColour' value=''>";
    html += "</div>";
    html += "<div class='uploader-area' style='display:none'>";
    html += "<select id='editSelect'><option value=''>Select existing</option></select>";
    html += "<div class='drop-area-holder'>";
    html += "<input type='file' class='fileUpload'  id='fileUpload'>";
    html += "<div style='display:flex; flex-direction:column; justify-content:center; align-items:center;'><img src='/wp-content/themes/Divi-child/Assets/Icons/cloud-upload.svg' width='73' height='53' /><span style='pointer-events:none;'>Drop files here</span><span id='or'>or</span>";
    html += "<label for='fileUpload' id='labelUpload'>Select File</label>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    html += "<div style='margin-top:24px'>";
    html += "<button class='submitPopoverAdd'>Add Breed ";
    html += "<div id='spinnerDiv' style='display:none'><img src='/wp-content/plugins/micerule-tables/public/partials/spinner.svg' /></div>";
    html += "</button>";
    html += "</div>";

    return html;
  }


  function openAddBreedPopOver() {
    //close previous preview row if existing
    $(".icon-bg-add").popover('hide');
    $(".icon-bg-add").closest("tr").remove();

    //add preview row
    $("#overviewTable").prepend("<tr><td><div class='icon-bg-add' width='50' height='50'><img src='/wp-content/themes/Divi-child/Assets/Icons/icon-placeholder.svg' id='iconAdd' width='50' height='50'></div></td></tr>");
    $(".icon-bg-add").addClass("icon-edit");
    $('.icon-bg-add').popover({
      html: true,
      sanitize: false,
      content: function () {
        return getPopOverHtml();
      }
    });

    $(".icon-bg-add").popover('show');
  }

  /**
   * adds breed to micerule_breeds table by calling ajax function breedAdd
   * @param {string} name 
   * @param {string} colour - hex string of selected colour
   * @param {string} section 
   * @param {string} iconSelectValue - Path of selected Icon
   */
  function addBreedDBEntry(name, colour, section, iconSelectValue) {
    //allowed formats for upload
    var fileExtension = ['png', 'svg'];

    if (name != "" && colour != "" && section != "" && ($("#fileUpload").val() != "" || $("#editSelect").select2('data')[0].id != "")) {
      if ($.inArray($("#fileUpload").val().split('.').pop().toLowerCase(), fileExtension) == -1 && $("#editSelect").select2('data')[0].id == "") {
        alert("Only svg and png allowed!");
      } else {
        var data = new FormData();
        data.append('action', 'addBreed');
        data.append('_ajax_nonce', my_ajax_obj.nonce);
        //check File Input
        if ($("#fileUpload").prop('files').length != 0) {
          var file = $("#fileUpload").prop('files')[0];
          data.append('file', file);
        } else {
          data.append('iconURL', iconSelectValue);
        }

        data.append('name', name);
        data.append('colour', colour);
        data.append('section', section);
        jQuery.ajax({
          type: 'POST',
          url: my_ajax_obj.ajax_url,
          data: data,
          contentType: false,
          processData: false,
          beforeSend: function () {
            $('#spinnerDiv').css("display", "block");
          },
          success: function (data) {
            location.reload();
          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
          }
        });
      }
    } else {
      alert("Please fill out all fields!");
    }
  }

});
