//Datepicker for Create Season Table
jQuery(document).ready(function($){
  $(".datepicker").datepicker();

  //datepicker for deadline select
  $("#deadlineDatePicker").flatpickr({
    enableTime : true,
    onClose : function(){
      console.log($("#deadlineDatePicker").val());
    },
  });
});
