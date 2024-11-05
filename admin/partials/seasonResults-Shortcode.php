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

  $html = var_export(SeasonResults::getSeasonResults(1505080800, 1538172000), true);

  //return HTML
  return $html;

}

add_shortcode('season_resultTable','season_resultTable');

?>
