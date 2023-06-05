/**
* opens Popover with input fields to update breed
*
*/
jQuery(document).ready(function ($) {
  function popoverHtml(callback, iconElement) {
    var html = "<div class='form-group'>";
    var name = iconElement.closest('tr').find('breed-name').text();
    html += "<div class='breed-row'>";
    html += "<input type='text' class='namePopover' value='" + name + "'>";

    var section = iconElement.closest('tr').find('.breed-section').text();
    var options = "";
    sections.forEach(function (value) {
      options += "<option value='" + value + "'" + ((value == section) ? ' selected="selected"' : '') + ">" + value + "</option>";
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
    colourPicker = iconElement.css('background-color');
    html += "<input type='text' class='editColour' value='" + rgbToHex(colourPicker.split("(")[1].split(")")[0]) + "'>";
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

    html += "<div class='button-row'>";
    html += "<button class='deletePopover'>Delete Variety</button>";
    html += "<button class='submitPopover'>Submit Changes</button>";

    html += "</div>";
    html += "</div>";

    callback(html);
  }

  // Opens Popover, when button is clicked
  $(".icon-bg").on('click', function () {
    $(this).addClass("icon-edit");
    popoverHtml(function (html) {
      editHtml = html;
      const bounds = document.querySelector(".overview");

      $(".icon-bg").popover({
        boundary: bounds,
        html: true,
        sanitize: false,
        content: function () {
          return editHtml;
        },
      });
    }, $(this));

    $(this).popover('show');
  });

  /**
  * calls 'updateBreed' to update Breed when Submit Changes is clicked
  *
  */
  $("#overviewTable").on("click", ".submitPopover", function () {
    var id = $(".icon-edit").attr("data-id");
    var name = $(".namePopover").val();
    var colour = $(".editColour").val();
    var section = $(".editCategory").val();
    var selectEdit = $('#editSelect').select2('data')[0].id;
    var iconURL = $(".icon-edit").find("img").attr("src");

    //Ajax call for Edit
    //allowed formats for upload
    var fileExtension = ['png', 'svg'];

    if (name == "" || section == "") {
      alert("Please fill out all fields!");
    } else if ($("#fileUpload").prop('files').length != 0 && $.inArray($("#fileUpload").val().split('.').pop().toLowerCase(), fileExtension) == -1) {
      alert("Only svg and png allowed!");
    } else {
      var data = new FormData();
      data.append('action', 'updateBreed');
      data.append('_ajax_nonce', my_ajax_obj.nonce);
      if ($("#fileUpload").prop('files').length != 0) {
        var file = $("#fileUpload").prop('files')[0];
        data.append('file', file);
      } else {
        if (selectEdit != "") {
          data.append('iconURL', $('#editSelect').select2('data')[0].id);
        } else {
          data.append('iconURL', iconURL);
        }
      }
      data.append('id', id);
      data.append('name', name);
      data.append('colour', colour);
      data.append('section', section);
      jQuery.ajax({
        type: 'POST',
        url: my_ajax_obj.ajax_url,
        data: data,
        contentType: false,
        processData: false,
        success: function (data) {
          location.reload();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          alert(errorThrown);
        }
      });
    }
  });

  /**
  * calls 'deleteBreed' to update Breed when Delete Variety is clicked
  *
  */
  $("#overviewTable").on("click", ".deletePopover", function () {
    var id = $(".icon-edit").attr("data-id");
    $("#dialogText").dialog({
      buttons: {
        'Cancel': function () {
          $(this).dialog('close');
        },
        'Confirm': function () {
          jQuery.ajax({
            type: 'POST',
            url: my_ajax_obj.ajax_url,
            data: {
              _ajax_nonce: my_ajax_obj.nonce,
              action: 'deleteBreed',
              id: id,
            },
            success: function (data) {
              location.reload();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
              alert(errorThrown);
            }
          });
        }
      }
    });
  });
  
});

function rgbToHex(rgb) {
  return "#" + ((1 << 24) + (parseInt(rgb.split(",")[0]) << 16) + (parseInt(rgb.split(",")[1]) << 8) + parseInt(rgb.split(",")[2])).toString(16).slice(1);
}
