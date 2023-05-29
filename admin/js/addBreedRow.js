jQuery(document).ready(function($){
  $(".addBreed").on('click',function(){
    switch(this.id){
      case "Selfs":
        console.log("Selfs");
        $('#tableSelfs tr:last').after('<tr><td name="micerule_breeds_table_data[selfs][breeds][]"><input type="text" name="micerule_breeds_table_data[selfs][breeds][]"></input></td><td name="micerule_breeds_table_data[selfs][ads][]"><input type="number" name="micerule_breeds_table_data[selfs][ads][]"></input></td><td name="micerule_breeds_table_data[selfs][u8][]"><input type="number" name="micerule_breeds_table_data[selfs][u8][]"></input></td></tr>');
        break;

      case "Tans":
       console.log("Tans");
       $('#tableTans tr:last').after('<tr><td name="micerule_breeds_table_data[tans][breeds][]"><input type="text" name="micerule_breeds_table_data[tans][breeds][]"></input></td><td name="micerule_breeds_table_data[tans][ads][]"><input type="number" name="micerule_breeds_table_data[tans][ads][]"></input></td><td name="micerule_breeds_table_data[tans][u8][]"><input type="number" name="micerule_breeds_table_data[tans][u8][]"></input></td></tr>');
       break;

      case "Satins":
        console.log("Satins");
        $('#tableSatins tr:last').after('<tr><td name="micerule_breeds_table_data[satins][breeds][]"><input type="text" name="micerule_breeds_table_data[satins][breeds][]"></input></td><td name="micerule_breeds_table_data[satins][ads][]"><input type="number" name="micerule_breeds_table_data[satins][ads][]"></input></td><td name="micerule_breeds_table_data[satins][u8][]"><input type="number" name="micerule_breeds_table_data[satins][u8][]"></input></td></tr>');
        break;

      case "Marked":
        console.log("Marked");
        $('#tableMarked tr:last').after('<tr><td name="micerule_breeds_table_data[marked][breeds][]"><input type="text" name="micerule_breeds_table_data[marked][breeds][]"></input></td><td name="micerule_breeds_table_data[marked][ads][]"><input type="number" name="micerule_breeds_table_data[marked][ads][]"></input></td><td name="micerule_breeds_table_data[marked][u8][]"><input type="number" name="micerule_breeds_table_data[marked][u8][]"></input></td></tr>');
        break;

      case "AOVs":
        console.log("AOVs");
        $('#tableAOVs tr:last').after('<tr><td name="micerule_breeds_table_data[aovs][breeds][]"><input type="text" name="micerule_breeds_table_data[aovs][breeds][]"></input></td><td name="micerule_breeds_table_data[aovs][ads][]"><input type="number" name="micerule_breeds_table_data[aovs][ads][]"></input></td><td name="micerule_breeds_table_data[aovs][u8][]"><input type="number" name="micerule_breeds_table_data[aovs][u8][]"></input></td></tr>');
        break;
    }
  });
});
