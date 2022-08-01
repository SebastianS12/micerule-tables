/**
* Displays/Hides Select for Partner(User), when Partnership box is checked
*
*/
jQuery(document).ready(function($){

  if($("#partnership1").prop('checked')){
    $("#selectPShip1").css("display","inline");
  }else{
    $("#selectPShip1").css("display","none");
  }

  if($("#partnership2").prop('checked')){
    $("#selectPShip2").css("display","inline");
  }else{
    $("#selectPShip2").css("display","none");
  }

  if($("#partnership3").prop('checked')){
    $("#selectPShip3").css("display","inline");
  }else{
    $("#selectPShip3").css("display","none");
  }


  $(".pCheck").on('change',function(){

    switch(this.id){
      case "partnership1":
      if($("#partnership1").prop('checked')){
        $("#selectPShip1").css("display","inline");
      }else{
        $("#selectPShip1").css("display","none");
      }
      break;

      case "partnership2":
      if($("#partnership2").prop('checked')){
        $("#selectPShip2").css("display","inline");
      }else{
        $("#selectPShip2").css("display","none");
      }
      break;

      case "partnership3":
      if($("#partnership3").prop('checked')){
        $("#selectPShip3").css("display","inline");
      }else{
        $("#selectPShip3").css("display","none");
      }
      break;
    }
  });
});
