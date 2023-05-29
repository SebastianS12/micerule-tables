/**
* Opens Popover with Input Fields to add breed
*
*/
jQuery(document).ready(function($){


  var breeds = ["Selfs","Marked","Satins","AOVs","Tans"];

  function getUploads(callback,context){
    //get uploads with ajax


    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'getUploads',
      },
      success: function (data) {
        callback(data,context);
      },

      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }

    });

  }

  function formatState (state) {
    if (!state.id) {
      return state.text;
    }
    var $state = $(
      '<span><img src="' + state.element.value+'" class="img-flag" height="40px" width="40px" /> ' + state.text + ' </span>'
    );
    return $state;
  };


  //Icon Preview
  $("#overviewTable").on('change','#fileUploadAdd',function(){
    $("#iconAdd").css("display","block");
    var icon =$(this).parents().eq(6).find(".icon-bg-add"); //icon that's being edited
    console.log(icon);
    var reader = new FileReader();
    reader.onload = function(e){
      icon.find("img").attr('src',e.target.result);
    }
    icon.css("display","block");
    $("#labelUpload").text($(this)[0].files[0].name);
    console.log($(this).parents());
    reader.readAsDataURL($(this)[0].files[0]);
  });

  //Icon Preview Select
  $("#overviewTable").on('change','#editSelectAdd',function(){
    $("#iconAdd").css("display","block");
    var icon =$(this).parents().eq(6).find(".icon-bg-add"); //icon that's being edited
    console.log($(this).parents().eq(5));
    icon.find("img").attr('src',$(this).val());
    icon.css("display","block");
  });






  $("#addBreedButton").on('click',function(){

    //close previous add row
    $(".icon-bg-add").popover('hide');
    $(".icon-bg-add").closest("tr").remove();

    //popoverHtml
    var html = "<div class='form-group'>";
    html += "<div class='breed-row'>";
    html += "<input type='text' class='namePopover' value=''>";

    var options = "";
    breeds.forEach(function(value){
      options += "<option value='"+value+"'>"+value+"</option>";
    });
    html += "<select class='editCategory'>"+options+"</select>";
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
    html += "<div class='uploader-area-add' style='display:none'>";
    html += "<select id='editSelectAdd'><option value=''>Select existing</option></select>";
    html += "<div class='drop-area-holder'>";

    html += "<input type='file' class='fileUploadAdd'  id='fileUploadAdd'>";
    html += "<div style='display:flex; flex-direction:column; justify-content:center; align-items:center;'><img src='/wp-content/themes/Divi-child/Assets/Icons/cloud-upload.svg' width='73' height='53' /><span style='pointer-events:none;'>Drop files here</span><span id='or'>or</span>";
    html += "<label for='fileUploadAdd' id='labelUpload'>Select File</label>";



    html += "</div>";
    html += "</div>";
    html += "</div>";


    html += "</div>";

    html += "<div style='margin-top:24px'>";
    html += "<button class='submitPopoverAdd'>Add Breed ";
    html += "<div id='spinnerDiv' style='display:none'><img src='/wp-content/plugins/micerule-tables/public/partials/spinner.svg' /></div>";
    html += "</button>";
    html += "</div>";






    $("#overviewTable").prepend("<tr><td><div class='icon-bg-add' width='50' height='50'><img src='/wp-content/themes/Divi-child/Assets/Icons/icon-placeholder.svg' id='iconAdd' width='50' height='50'></div></td></tr>");

    $(document).unbind('click',outsideClickAdd);

    $('.icon-bg-add').popover({
      html:true,
      sanitize: false,
      content:function(){
        return html;
      }


    });
    $(".icon-bg-add").popover('show');
  });



  $("#overviewTable").on('inserted.bs.popover','.icon-bg-add', function () {
    console.log("insert");
    var context = $(this);

    getUploads(function(result){
      $("#editSelectAdd").html("<select id='editSelect'><option value=''>Select existing</option>"+result+"</select>");
      $("#editSelectAdd").select2({
        templateResult: formatState
      });
    });
    $(".popover").css("z-index",1);

    $(".editColour").wpColorPicker({
      hide:false,
      change: function (event, ui) {
        $("#iconAdd").css("display","block");
        context.css("background-color",ui.color.toString());},
      });
    });



    $("#overviewTable").on("click", ".submitPopoverAdd", function(){

      var name = $(".namePopover").val();

      var colour = $(".editColour").val();

      var category = $(".editCategory").val();

      var selectEdit = $('#editSelectAdd').select2('data')[0].id;



      //Ajax call for Add
      //allowed formats for upload
      var fileExtension = ['png', 'svg'];

      if(name != "" && colour != ""  && category != "" &&($("#fileUploadAdd").val()!= "" || $("#editSelectAdd").select2('data')[0].id !="")){
        if($.inArray($("#fileUploadAdd").val().split('.').pop().toLowerCase(),fileExtension)==-1 && $("#editSelectAdd").select2('data')[0].id==""){
          alert("Only svg and png allowed!");
        }else{
          var data = new FormData();
          data.append('action','breedAdd');
          data.append('_ajax_nonce', my_ajax_obj.nonce);
          //check File Input
          if($("#fileUploadAdd").prop('files').length != 0){
            var file =$("#fileUploadAdd").prop('files')[0];
            data.append('file',file);
          }else{
            data.append('path',selectEdit);
          }

          data.append('name',name);
          data.append('colour',colour);
          data.append('category',category);
          jQuery.ajax({
            type: 'POST',
            url: my_ajax_obj.ajax_url,
            data: data,
            contentType: false,
            processData: false,

            beforeSend: function() {
              $('#spinnerDiv').css("display","block");
            },

            success: function (data) {
              location.reload();
              console.log(data);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
              alert(errorThrown);
            }
          });
        }


      }else{
        alert("Please fill out all fields!");
      }

    });


    //event handler for clicking outside the AddBreed popover
    var outsideClickAdd = function(event){

      if(!$(event.target).closest(".popover").length && !$(event.target).parent().hasClass("icon-bg-add") && !$(event.target).closest(".select2-search").length){

        $(".icon-bg-add").popover('hide');
        $(".icon-bg-add").closest("tr").remove();


      }
    };

    //outside Click
    $("#overviewTable").on('hidden.bs.popover','.icon-bg-add', function () {


      $(document).unbind('click',outsideClickAdd);
      console.log("unbind");

    });

    $("#overviewTable").on('shown.bs.popover','.icon-bg-add', function () {
      console.log("bind");
      $(document).bind("click",outsideClickAdd);

    });

    //-----------------------outsideClick end--------------------------

    //-----------------------Upload Area-------------------------------
    $("#overviewTable").on('drag dragstart dragend dragover dragenter dragleave drop',".uploader-area-add", function(e) {
      e.preventDefault();
      e.stopPropagation();
    })

    //upload field dragover
    $("#overviewTable").on("dragover dragcenter", ".uploader-area-add", function(){

      $(".uploader-area-add").addClass('is-dragover');
    });

    //upload field dragleave
    $("#overviewTable").on("dragleave dragend drop", ".uploader-area-add", function(){

      $(".uploader-area-add").removeClass('is-dragover');
    });

    $("#overviewTable").on('drop',".uploader-area-add", function(e) {
      var droppedFiles = e.originalEvent.dataTransfer.files;

      $("#fileUploadAdd").prop('files',droppedFiles);
      $("#fileUploadAdd").trigger("change");

      $("#or").css("opacity",0);

    });

    //-------------------------------Upload Area End------------------------------




  });
