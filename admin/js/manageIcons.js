/**
* Functionality for Manage Icon Tab,
*
* Delete and Bulk Upload
*
*/
jQuery(document).ready(function ($) {
  //allowed formats for upload
  var fileExtension = ['png', 'svg'];
  //array for file Uploads
  var files = new Array();
  
  $("#deleteIcon").on('click', function () {
    deleteIcon();
  });
  //Multiple Files Uploads
  //Upload-area
  $("#upload-area-multiple").on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
  })

  //upload field dragover
  $("#upload-area-multiple").on("dragover dragcenter", function () {
    $("#upload-area-multiple").addClass('is-dragover');
  });

  //upload field dragleave
  $("#upload-area-multiple").on("dragleave dragend drop", function () {
    $("#upload-area-multiple").removeClass('is-dragover');
  });

  $("#upload-area-multiple").on('drop', function (e) {
    var droppedFiles = e.originalEvent.dataTransfer.files;
    if ($.inArray(droppedFiles[0].name.split('.').pop().toLowerCase(), fileExtension) == -1) {
      alert("Only svg and png allowed!");
      return;
    }

    $.each(droppedFiles, function (i) {
      files.push($(this)[0]);
    });

    $("#or").css("opacity", 0);
    $("#fileUploadMultiple").trigger("change");
  });

  $("#fileUploadMultiple").on("change", function () {
    if (files.length != 0) {
      if (files.length == 1) {
        $("#uploadSpan").text(files[0].name)
      } else {
        $("#uploadSpan").text(files.length + " files selected");
      }
    } else {
      if ($("#fileUploadMultiple").prop('files').length == 1) {
        $("#uploadSpan").text($("#fileUploadMultiple").prop('files')[0].name);
      } else {
        $("#uploadSpan").text($("#fileUploadMultiple").prop('files').length + " files selected");
      }
    }
  });

  //upload files through ajax
  $(".submitUploadsMultiple").on("click", function () {
    uploadFiles();
  });

  /**
   * deletes icon selected from select2
   */
  function deleteIcon(){
    var iconPath = $("#deleteUpload").select2('data')[0].id;
    if (iconPath != "") {
      jQuery.ajax({
        type: 'POST',
        url: my_ajax_obj.ajax_url,
        data: {
          _ajax_nonce: my_ajax_obj.nonce,
          action: 'deleteIcon',
          path: iconPath
        },
        success: function (data) {
          location.reload();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          alert(errorThrown);
        }
      });
    } else {
      alert("Select an Icon first!");
    }
  }

  /**
   * uploads files in files array to res folder through ajax
   */
  function uploadFiles(){
    var data = new FormData();
    data.append('action', 'uploadFiles');
    data.append('_ajax_nonce', my_ajax_obj.nonce);
    if (files.length == 0) { //if empty, files got submitted through file select
      if ($("#fileUploadMultiple").prop('files').length == 0) {//file select empty too, alert warning
        alert("No files selected!");
      } else {
        $.each($("#fileUploadMultiple").prop('files'), function (i) {//check for file extensions
          if ($.inArray($(this)[0].name.split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only svg and png allowed!");
            return;
          } else {//Append files from browse Files Input
            data.append(i, $(this)[0]);
          }
        });
      }
    } else {//files were added through drag and drop field
      $.each(files, function (i) {//append files to Formdata from drag and drop field
        data.append(i, $(this)[0]);
      });
    }

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
