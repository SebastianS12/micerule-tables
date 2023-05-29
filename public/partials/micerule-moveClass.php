<?php
global $post;
global $wpdb;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$locationID = $_POST['id'];
$sectionName = $_POST['section'];
$position = $_POST['position'];
$direction = $_POST['direction'];

$eventClasses = EventClasses::create($locationID);
if($sectionName != "optional")
  $eventClasses->moveClass($sectionName, $position, $direction);
else
  $eventClasses->moveOptionalClass($position, $direction);
$eventClasses->updatePostMeta($locationID);

$locationSecretaries = EventProperties::getLocationSecretaries($locationID);
$locationOptionalSettings = EventOptionalSettings::create($locationID);
$locationSectionTables = new LocationSectionTables($locationID);
echo($locationSectionTables->getSectionTablesHtml($eventClasses, $locationSecretaries, $locationOptionalSettings));

wp_die();
