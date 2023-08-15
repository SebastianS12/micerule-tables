<?php

function sectionTables($atts){
  global $post;

  $sectionData_atts = shortcode_atts(array(
    'id' => ''
  ), $atts);

  return ShowOptionsView::getSectionTablesHtml($sectionData_atts['id']);
}

add_shortcode('sectionTables','sectionTables');
