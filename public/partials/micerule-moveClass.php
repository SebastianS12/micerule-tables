<?php
global $post;
global $wpdb;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$locationID = $_POST['id'];
$firstClassID = $_POST['firstClassID'];
$secondClassID = $_POST['secondClassID'];

ShowClassesController::swapClasses($locationID, $firstClassID, $secondClassID);
echo(ShowOptionsView::getSectionTablesHtml($locationID));
wp_die();
