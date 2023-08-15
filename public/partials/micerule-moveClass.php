<?php
global $post;
global $wpdb;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$locationID = $_POST['id'];
$sectionName = $_POST['section'];
$firstClassName = $_POST['firstClassName'];
$secondClassName = $_POST['secondClassName'];

if($sectionName != "optional")
  ShowOptionsController::swapSectionClasses($locationID, $firstClassName, $secondClassName);
else
  ShowOptionsController::swapOptionalClasses($locationID, $firstClassName, $secondClassName);

echo(ShowOptionsView::getSectionTablesHtml($locationID));
wp_die();
