//upload file code to manageIcons
//everything popover related (unbind, outside click etc) here
//add, update, delete to manageBreeds.js

jQuery(document).ready(function ($) {
    var colour = "";
    var svgPath = "";

    //Icon Preview
    $("#overviewTable").on('change', '#fileUpload', function () {
        previewUploadedIcon();
    });

    //Icon Preview Select
    $("#overviewTable").on('change', '#editSelect', function () {
        previewSelectedIcon($(this).val());
    });

    //toggle Icon and Colour Tab
    $("#overviewTable").on("click", "#colourTab", function () {
        showIconTab();
    });

    $("#overviewTable").on("click", "#uploadTab", function () {
        showColourTab();
    });

    //load select2
    $('#deleteUpload').select2({
        templateResult: formatState
    });

    /**
   * customizes the select2 dropdown appearance by adding the icon as img
   */
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span><img src="' + state.element.value + '" class="img-flag" height="40px" width="40px" /> ' + state.text + ' </span>'
        );
        return $state;
    };

    /**
     * adds uploaded icon to the icon preview
     * only triggered when icon is uploaded, not selected
     */
    function previewUploadedIcon() {
        $("#iconAdd").css("display", "block");
        var iconPreviewElement = $("#overviewTable").find(".icon-edit");
        //var icon =$(this).parents().eq(6).find(".icon-bg-add"); //icon that's being edited
        var reader = new FileReader();
        reader.onload = function (e) {
            iconPreviewElement.find("img").attr('src', e.target.result);
        }
        iconPreviewElement.css("display", "block");
        $("#labelUpload").text($(this)[0].files[0].name);
        reader.readAsDataURL($(this)[0].files[0]);
    }

    /**
     * adds selected, already uploaded icon to the icon preview
     * only triggered when icon is selected, not uploaded
     */
    function previewSelectedIcon(iconPath) {
        $("#iconAdd").css("display", "block");
        var iconPreviewElement = $("#overviewTable").find(".icon-edit");
        iconPreviewElement.find("img").attr('src', iconPath);
        iconPreviewElement.css("display", "block");
    }

    /**
    * adds file select options and ColorPicker when popover is inserted into DOM
    */
    $("#overviewTable").on('inserted.bs.popover', '.icon-edit', function () {
        var previewIconElement = $(this);
        addUploadOptionsToPopOver();
        addColorPickerToPopOver(previewIconElement);
    });

    /**
     * adds upload paths from already uploaded icons as options to popover icon select after popover is inserted into DOM
     */
    function addUploadOptionsToPopOver() {
        addExistingUploadOptions(function (result) {
            $("#editSelect").html("<select id='editSelect'><option value=''>Select existing</option>" + result + "</select>");
            $("#editSelect").select2({
                templateResult: formatState
            });
        });
        $(".popover").css("z-index", 1);
    }

    /**
     * attaches color picker to popover after popover is inserted into DOM
     * @param {*jQuery Object} previewIconElement 
     */
    function addColorPickerToPopOver(previewIconElement) {
        $(".editColour").wpColorPicker({
            hide: false,
            change: function (event, ui) {
                //$("#iconAdd").css("display", "block");
                previewIconElement.css("background-color", ui.color.toString());
            },
        });
    }

    /**
     * retrieves an array of paths of uploaded icons and adds them as options to a select
     * @param {*} callback callback function that adds the upload paths as options to the select
     */
    function addExistingUploadOptions(callback) {
        //get uploads with ajax
        jQuery.ajax({
            type: 'POST',
            url: my_ajax_obj.ajax_url,
            data: {
                _ajax_nonce: my_ajax_obj.nonce,
                action: 'getUploads',
            },
            success: function (data) {
                callback(data);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }

    function showIconTab() {
        $("#colourEditTab").css("display", "block");
        $(".editColour").css("display", "block");
        $(".uploader-area").css("display", "none");
        $("#colourTab").addClass('active').removeClass('inactive');
        $("#uploadTab").addClass('inactive').removeClass('active');

        //if Add Breed
        $(".uploader-area-add").css("display", "none");
    }

    function showColourTab() {
        $("#colourEditTab").css("display", "none");
        $(".uploader-area").css("display", "block");
        $("#uploadTab").addClass('active').removeClass('inactive');
        $("#colourTab").addClass('inactive').removeClass('active');

        //if Add Breed
        $(".uploader-area-add").css("display", "block");
    }



    //outside Click
    $("#overviewTable").on('hidden.bs.popover', '.icon-edit', function () {
        $(this).removeClass("icon-edit");
    });

    $("#overviewTable").on('shown.bs.popover', '.icon-edit', function () {
        $(".wp-picker-input-wrap").removeClass("hidden");
        //hide popover and restore icon and colour
        $(document).one("click", function (event) {
            if (!jQuery(event.target).closest(".popover").length && !jQuery(event.target).parent().hasClass("icon-edit") && !jQuery(event.target).closest(".select2-search").length) {
                jQuery(".icon-edit").popover('hide');
                jQuery(".icon-edit").find("img").attr('src', svgPath);
                jQuery(".icon-edit").css("background-color", colour);
        
                jQuery(".icon-bg-add").closest("tr").remove();
            }
        });
        svgPath = $(this).find("img").attr('src');
        colour = $(this).css("background-color");
    });

    //-----------------------Upload Area-------------------------------
    $("#overviewTable").on('drag dragstart dragend dragover dragenter dragleave drop',".uploader-area", function(e) {
        e.preventDefault();
        e.stopPropagation();
      })
  
      //upload field dragover
      $("#overviewTable").on("dragover dragcenter", ".uploader-area", function(){
        $(".uploader-area").addClass('is-dragover');
      });
  
      //upload field dragleave
      $("#overviewTable").on("dragleave dragend drop", ".uploader-area", function(){
        $(".uploader-area").removeClass('is-dragover');
      });
  
      $("#overviewTable").on('drop',".uploader-area", function(e) {
        var droppedFiles = e.originalEvent.dataTransfer.files;
        $("#fileUpload").prop('files',droppedFiles);
        $("#fileUpload").trigger("change");
        $("#or").css("opacity",0);
      });
      //-------------------------------Upload Area End------------------------------
});