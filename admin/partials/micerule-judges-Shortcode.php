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

  //start html
  $html = '<div class="micerule_judges" style="text-align: center">';

  //header
  $html .= "<p>--Judges--</p>";

  $judgesRepository = new JudgesRepository($id);
  $judgesSectionRepository = new JudgesSectionsRepository($id);
  $judgeCollection = $judgesRepository->getAll()->with([JudgeSectionModel::class], ["id"], ["judge_id"], [$judgesSectionRepository]);
  foreach($judgeCollection as $judgeModel){
    $html .= "<span>".$judgeModel->judge_name.":  ";
      foreach($judgeModel->sections() as $judgeSectionModel){
        $section = Section::from($judgeSectionModel->section);
        $html .= $section->getDisplayStringPlural().", ";
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
