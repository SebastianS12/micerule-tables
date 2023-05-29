<?php

/*
* creates html for season table based on given id 
*
*/
function season_resultTable($atts){
  global $wpdb;

  $micerule_settings = shortcode_atts(array(
    'id'=> ''
  ), $atts);

 //get Season Results(Names and Points) from Database for given ID
  $table = $wpdb->prefix;
  $json=$wpdb->get_results("SELECT season_results FROM ".$wpdb->prefix."micerule_result_tables WHERE mrtable_id =".$micerule_settings['id']);
  $data = json_decode($json[0]->season_results);

  //start HTML
  $html= "<div class = 'season_resultTable'>";
  //start table
  $html .= "<table id = 'micerule_season_resultTable'>";

  //Create header rows
  $html .= "<thead><tr>";
  $html .= "<th class = 'season_resultHeader'>Name</th>";
  $html .= "<th class = 'season_resultHeader'>Points</th>";
  $html .= "</tr></thead><tbody>";

  //table contents
  foreach($data as $key=> $value){
    $html .= "<tr>";
    $html .= "<td class = 'season_resultCell'>".$key."</td>";
    $html .= "<td class = 'season_resultCell'>".$value."</td>";
    $html .= "</tr>";
  }

  //close HTML
  $html .= "</tbody>";
  //end table
  $html .= "</table>";
  //end html
  $html .= "</div>";

  //return HTML
  return $html;

}

add_shortcode('season_resultTable','season_resultTable');

?>
