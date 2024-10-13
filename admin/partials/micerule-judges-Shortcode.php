<?php

/*
* creates html for judge display of event with given id 
*
*/
function micerule_shortcode_judges($atts){

  global $wpdb;
  $micerule_settings = shortcode_atts(array(
    'id' => ''
  ), $atts);



  $id = filter_var($micerule_settings['id'], FILTER_SANITIZE_NUMBER_INT);

  //get postmeta based on event ID
  $table_post_meta = get_post_meta($id, 'micerule_data_settings', true);


  //start html
  $html = '<div class="micerule_judges" style="text-align: center">';

  //header
  $html .= "<p>--Judges--</p>";

  foreach(EventJudgesHelper::getEventJudgeNames($id) as $judgeName){
    $html .= "<span>".$judgeName.":  ";
      foreach(EventJudgesHelper::getJudgeSections($id, $judgeName) as $judgeSection){
          $html .= $judgeSection.", ";
      }
      $html = rtrim($html, ', ');

      $html .= "</span>";
      $html .= "<br>";
  }

  //end html
  $html .= '</div>';
  return $html;

}


add_shortcode('micerule_judges', 'micerule_shortcode_judges');
?>
