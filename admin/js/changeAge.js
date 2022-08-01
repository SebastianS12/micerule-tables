/**
* Puts the opposite Value (Ad/U8) in the Input Field for
* the opposite age
*/
jQuery(document).ready(function($){
  $("select").on('change',function(){

    switch(this.id){
      case "ageBIS1":
      if($(this).val()=="U8"){
        $("#tdBIS").html('<input type="hidden" id="ageBIS2" name="micerule_table_data[age][]"value="Ad">Ad');
      }else{
        $("#tdBIS").html('<input type="hidden" id="ageBIS2"  name="micerule_table_data[age][]"value="U8">U8');
      }
      break;

      case "ageBSelf1":
      if($(this).val()=="U8"){
        $("#tdBSelf").html('<input type="hidden" name="micerule_table_data[age][]"value="Ad">Ad');
      }else{
        $("#tdBSelf").html('<input type="hidden" id="ageBIS2"  name="micerule_table_data[age][]"value="U8">U8');
      }
      break;

      case "ageBM1":
      if($(this).val()=="U8"){
        $("#tdBM").html('<input type="hidden" name="micerule_table_data[age][]"value="Ad">Ad');
      }else{
        $("#tdBM").html('<input type="hidden" id="ageBIS2"  name="micerule_table_data[age][]"value="U8">U8');
      }
      break;

      case "ageBT1":
      if($(this).val()=="U8"){
        $("#tdBT").html('<input type="hidden"  name="micerule_table_data[age][]"value="Ad">Ad');
      }else{
        $("#tdBT").html('<input type="hidden" id="ageBIS2"  name="micerule_table_data[age][]"value="U8">U8');
      }
      break;

      case "ageBS1":
      if($(this).val()=="U8"){
        $("#tdBS").html('<input type="hidden"  name="micerule_table_data[age][]"value="Ad">Ad');
      }else{
        $("#tdBS").html('<input type="hidden" id="ageBIS2"  name="micerule_table_data[age][]"value="U8">U8');
      }
      break;

      case "ageBAOV1":
      if($(this).val()=="U8"){
        $("#tdBAOV").html('<input type="hidden"  name="micerule_table_data[age][]"value="Ad">Ad');
      }else{
        $("#tdBAOV").html('<input type="hidden" id="ageBIS2"  name="micerule_table_data[age][]"value="U8">U8');
      }
      break;
    }
  });
});
