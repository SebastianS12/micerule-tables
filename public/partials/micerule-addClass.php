<?php
  global $post;

  $locationID = $_POST['id'];
  $sectionName = $_POST['section'];
  $className = $_POST['className'];

ShowOptionsController::addShowClass($locationID, $className, $sectionName);
echo(ShowOptionsView::getSectionTablesHtml($locationID));
wp_die();
