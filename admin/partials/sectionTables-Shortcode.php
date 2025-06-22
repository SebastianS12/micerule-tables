<?php

function sectionTables(){
  global $post;

  $locationID = LocationHelper::getIDFromLocationPostID($post->ID);
  return ShowOptionsView::getSectionTablesHtml($locationID);
}

add_shortcode('sectionTables','sectionTables');
