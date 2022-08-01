/**
* Displays/Hides Season Table on Season Results
*
*/
jQuery(document).ready(function($){
  $(".toggleButton").on('click',function(){
    if($("#table"+$(this).val()).css("height")=="0px"){
      $("#table"+$(this).val()).css("height","480px");
    }else{
      $("#table"+$(this).val()).css("height","0px");
    }
  });
});
