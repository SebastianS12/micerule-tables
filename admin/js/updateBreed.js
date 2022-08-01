//values for Popover Breeds Select
var breeds = ["Selfs","Marked","Satins","AOVs","Tans"];

/**
* opens Popover with input fields to update breed
*
*/
jQuery(document).ready(function($){

  var name ="";
  var colourPicker = "";
  var category = "";
  var svgPath = "";


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

  //adds icons to select
  function formatState (state) {
    if (!state.id) {
      return state.text;
    }
    var $state = $(
      '<span><img src="' + state.element.value+'" class="img-flag" height="40px" width="40px" /> ' + state.text + ' </span>'
    );
    return $state;
  };



  //Icon preview
  $("#overviewTable").on('change','#fileUpload',function(){
    var icon =$(this).parents().eq(6).find(".icon-bg"); //icon that's being edited
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
  $("#overviewTable").on('change','#editSelect',function(){

    var icon =$(this).parents().eq(5).find(".icon-bg"); //icon that's being edited
    console.log($(this).parents().eq(5));
    icon.find("img").attr('src',$(this).val());
    icon.css("display","block");
  });


  function popoverHtml(callback,context){
    //build HTML
    var html = "<div class='form-group'>";
    name = context.closest('tr').find('td').eq(1).html();
    html += "<div class='breed-row'>";
    html += "<input type='text' class='namePopover' value='"+name+"'>";

    category = context.closest('tr').find('td').eq(2).html();
    var options = "";
    breeds.forEach(function(value){
      options += "<option value='"+value+"'"+((value == category)? ' selected="selected"' : '')+">"+value+"</option>";
    });
    html += "<select class='editCategory'>"+options+"</select>";
    svgPath= context.closest('tr').find('td').eq(0).find("img").attr("src");
    html += "</div>";
    html += "<div class='popover-tabs'>";
    html += "<div id='colourTab' class='active'><span>Colour</span></div>";
    html += "<div style='width:2px; height: 100%; background:black;'></div>";
    html += "<div id='uploadTab' class='inactive'><span>Icon</span></div>";
    html += "</div>";
    html += "<div id='swap-pane'>";
    html += "<div id='colourEditTab'>";
    colourPicker = context.closest('tr').find('td').eq(3).css('background-color');
    html += "<input type='text' class='editColour' value='"+rgbToHex(colourPicker.split("(")[1].split(")")[0])+"'>";
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
  $(".icon-bg").on('click',function(){


    popoverHtml(function(html){
      editHtml = html;
      const bounds= document.querySelector(".overview");

      $(".icon-bg").popover({
        boundary: bounds ,
        html:true,
        sanitize: false,
        content:function(){
          console.log(editHtml);
          console.log(bounds);
          return editHtml;
        },

      });





    },$(this));

    $(this).popover('show');




  });



  $('.icon-bg').on('shown.bs.popover', function () {

    //colourPicker input field
    $(".wp-picker-input-wrap").removeClass("hidden");

    $(document).bind("click",outsideClick );

  });



  //adds select2 and colorpicker after html is loaded
  $('.icon-bg').on('inserted.bs.popover', function () {
    console.log("insert");
    var context = $(this);

    getUploads(function(result){
      $("#editSelect").html("<select id='editSelect'><option value=''>Select existing</option>"+result+"</select>");
      $("#editSelect").select2({
        templateResult: formatState
      });
    });
    $(".popover").css("z-index",1);

    $(".editColour").wpColorPicker({
      hide:false,
      change: function (event, ui) {
        context.css("background-color",ui.color.toString());},
      });
    });



    //upload area
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


    /**
    * calls 'updateBreed' to update Breed when Submit Changes is clicked
    *
    */
    $("#overviewTable").on("click", ".submitPopover", function(){

      var name_New = $(".namePopover").val();

      var colour = $(".editColour").val();

      var category_New = $(".editCategory").val();

      var selectEdit = $('#editSelect').select2('data')[0].id;



      //Ajax call for Edit
      //allowed formats for upload
      var fileExtension = ['png', 'svg'];

      if(name_New == ""  ||  category_New == ""){
        alert("Please fill out all fields!");
      }else if($("#fileUpload").prop('files').length != 0 && $.inArray($("#fileUpload").val().split('.').pop().toLowerCase(),fileExtension)==-1){
        alert("Only svg and png allowed!");
      }else{

        var data = new FormData();
        data.append('action','updateBreed');
        data.append('_ajax_nonce', my_ajax_obj.nonce);
        if($("#fileUpload").prop('files').length != 0){
          var file =$("#fileUpload").prop('files')[0];
          data.append('file',file);
        }else{
          if(selectEdit !=""){
            data.append('path',$('#editSelect').select2('data')[0].id);
          }else{
            data.append('path',svgPath);
          }
        }
        data.append('name',name);
        data.append('name_New',name_New);
        data.append('colour',colour);
        data.append('category',category);
        data.append('category_New',category_New);
        jQuery.ajax({
          type: 'POST',
          url: my_ajax_obj.ajax_url,
          data: data,
          contentType: false,
          processData: false,
          success: function (data) {
            location.reload();
            console.log(data);

          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("test");
          }
        });
      }//end check for empty fields and file type

    });


    /**
    * calls 'deleteBreed' to update Breed when Delete Variety is clicked
    *
    */
    $("#overviewTable").on("click", ".deletePopover", function(){

      $( "#dialogText" ).dialog({
        buttons: {
          'Cancel': function() {
            console.log(category);
            $(this).dialog('close');
          },
          'Confirm': function() {
            console.log(name);
            jQuery.ajax({
              type: 'POST',
              url: my_ajax_obj.ajax_url,
              data: {
                _ajax_nonce: my_ajax_obj.nonce,
                action: 'deleteBreed',
                name: name,
                category: category,
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
        }
      });

    });




    //reset Icon, called when popover hide
    $('.icon-bg').on('hidden.bs.popover', function () {


      $(document).unbind('click',outsideClick);
      console.log("unbind");

    });


    //event handler for clicking outside the popover
    var outsideClick = function(event){
      console.log($(event.target).closest(".popover"));
      if(!$(event.target).closest(".popover").length && !$(event.target).parent().hasClass("icon-bg") && !$(event.target).closest(".select2-search").length){
        console.log("outside");

        $(".icon-bg").each(function(){
          if(typeof($(this).attr("aria-describedby"))!== 'undefined'){
            $(this).popover('hide');
            $(this).find("img").attr('src',svgPath);
            $(this).css("background-color",colourPicker);
            console.log($(this).attr("aria-describedby"));
          }
        });
      }
    };

    //toggle Icon and Colour Tab
    $("#overviewTable").on("click","#colourTab",function(){
      console.log($(".wp-picker-input-wrap"));
      $("#colourEditTab").css("display","block");
      $(".editColour").css("display","block");
      $(".uploader-area").css("display","none");
      $("#colourTab").addClass('active').removeClass('inactive');
      $("#uploadTab").addClass('inactive').removeClass('active');


      //if Add Breed
      $(".uploader-area-add").css("display","none");
    });

    $("#overviewTable").on("click","#uploadTab",function(){
      $("#colourEditTab").css("display","none");
      $(".uploader-area").css("display","block");
      $("#uploadTab").addClass('active').removeClass('inactive');
      $("#colourTab").addClass('inactive').removeClass('active');

      //if Add Breed
      $(".uploader-area-add").css("display","block");
    });

  });



  function rgbToHex(rgb){
    return  "#" + ((1 << 24) + (parseInt(rgb.split(",")[0]) << 16) + (parseInt(rgb.split(",")[1]) << 8) + parseInt(rgb.split(",")[2])).toString(16).slice(1);
  }
