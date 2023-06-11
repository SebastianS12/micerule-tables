<?php

/*
* creates html for a single event table, based on id
*
*/
function micerule_shortcode_table($atts){

  global $wpdb;
  $micerule_settings = shortcode_atts(array(
    'id' => ''
  ), $atts);

  //start html
  $html = "<div class='micerule_table_MotherShipContainer'>";
  $html .= "<p><a href ='".get_permalink($micerule_settings['id'])."'>Calendar Page</a></p>";

  if(is_user_logged_in()){
    $html .= getJudgeHtml($micerule_settings['id']);
  }

  $html .= "<table id = 'micerule_table_".$micerule_settings['id']."'  class='eventTable'>";
  $html .= "<thead><tr>";
  $html .= "<th class = 'eventHeader2'> Award</th>";
  $html .= "<th class = 'eventHeader'> Name</th>";
  $html .= "<th class = 'eventHeader'> Breed</th>";
  $html .= "<th class = 'eventHeader'> Age</th>";
  $html .= "<th class = 'eventHeader'> Points</th>";
  $html .= "</tr></thead><tbody>";

  $resultTableData = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$micerule_settings['id']." ORDER BY id ASC", ARRAY_A);
  foreach($resultTableData as $sectionResultData){
    $html .= "<tr>";
    //TODO: There should be a better way to handle this -> add field to db?, helper class?
    $displayedAwards = array("BIS" => "Best in Show", "BOA" => "Best Opposite Age in Show", "BISec" => "Best ".$sectionResultData['section'], "BOSec" => "Best Opposite Age ".$sectionResultData['section']);
    $html .= "<td class='eventCell2'>".$displayedAwards[$sectionResultData['award']]."</td>";
    $html .= getFancierCellHtml($sectionResultData['fancier_name']);
    $html .= getVarietyCellHtml($sectionResultData['variety_name']);
    $html .= "<td class='eventCell'>".$sectionResultData['age']."</td>";
    $html .= "<td class='eventCell'>".$sectionResultData['points']."</td>";
    $html .= "</tr>";
  }

  $optionalResultTableData = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."micerule_event_results_optional WHERE event_post_id = ".$micerule_settings['id'], ARRAY_A);
  foreach($optionalResultTableData as $optionalClassResult){
    $html .= "<tr>";
    $html .= "<td class='eventCell2'>Best ".strtoupper($optionalClassResult['class_name'])."</td>";
    $html .= getFancierCellHtml($optionalClassResult['fancier_name']);
    $html .= getVarietyCellHtml($optionalClassResult['variety_name']);
    $html .= "<td class='eventCell'>AA</td>";
    $html .= "<td class='eventCell'>0</td>";
    $html .= "</tr>";
  }
  $html .= "</tbody>";
  $html .= "</table>";
  $html .= "</div>";

  return $html;
}

add_shortcode('micerule_tables', 'micerule_shortcode_table');

function getJudgeHtml($eventPostID){
  global $wpdb;

  $html = "<div class='judges'>";
  $html .= "<p>Judges</p>";
  $judgesData = $wpdb->get_results("SELECT judge_no, judge_name FROM ".$wpdb->prefix."micerule_event_judges WHERE event_post_id = ".$eventPostID, ARRAY_A);
  foreach($judgesData as $judgeData){
    $judgeSectionData = $wpdb->get_results("SELECT section FROM ".$wpdb->prefix."micerule_event_judges_sections WHERE event_post_id = ".$eventPostID." AND judge_no = ".$judgeData['judge_no'], ARRAY_A);
    $html .= "<p>".$judgeData['judge_name'].":  ";
    foreach($judgeSectionData as $sectionData){
      $html .= strtoupper($sectionData['section']).", ";
    }
    $html = rtrim($html, ', '); //remove comma from last loop iteration
    $html .= "</p>";
  }
  $html .= "<br>";
  $html.= "</div>";

  return $html;
}

function getFancierCellHtml($fancierName){
  $html = "";
  if(is_user_logged_in()){
    $html .= "<td class='eventCell'>".$fancierName."</td>";
  }else{
    $html .= "<td class = 'resultCellBlur'>";
    $html .= "<div class ='blurDiv' style='width:".random_int(70,175)."px; background-image: url(".plugin_dir_url(__DIR__)."../public/partials/blur.png);height:20px ;display:inline-block; background-position: ".random_int(0,500)."px 0'></div>";
    $html .= "</td>";
  }

  return $html;
}

function getVarietyCellHtml($varietyName){
  global $wpdb;
  $breedName = "(No record)";
  $iconURL = get_home_url()."/wp-content/themes/Divi-child/Assets/spacer.gif";
  $iconColour = "#FFF";
  $breedData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_breeds WHERE name = '".$varietyName."'", ARRAY_A);
  if(isset($breedData)){
    $breedName = $breedData['name'];
    $iconColour = $breedData['colour'];
    $iconURL = $breedData['icon_url'];
  }
  
  return "<td class='eventCell'><div class='variety-icon' style='background:url(".$iconURL.");background-repeat:no-repeat;background-color:".$iconColour."'></div>".$breedName."</td>";
}
?>
