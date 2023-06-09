/**
* Displays/Hides Select for Partner(User), when Partnership box is checked
*
*/
jQuery(document).ready(function($){
  $(".pCheck").on('change', function(){
    if($(this).prop('checked')){
      $(this).parent().find(".partnership-select").css("display", "inline");
    }else{
      $(this).parent().find(".partnership-select").css("display", "none");
      $(this).parent().find().val(".partnership-select");
    }
  });
});
