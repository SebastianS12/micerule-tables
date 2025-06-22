/**
* Puts the opposite Value (Ad/U8) in the Input Field for
* the opposite age
*/
jQuery(document).ready(function($){
  $(".age-select").on('change',function(){
      oaSelectId = this.id+"-oa";
      $("[id='"+oaSelectId+"']").val(getOppositeAge($(this).val()));
      console.log($(this).val());
      console.log($("[id='"+oaSelectId+"']").val());
  });

  $(".fancier-select").select2();
  $(".variety-select").select2();
  $(".judge-select").select2();
  $(".judge-partnership-select").select2();
});

function getOppositeAge(age){
  return (age === "Ad") ? "U8" : "Ad";
}
