<?php
global $post;
global $wpdb;

$locationID = $_POST['id'];
$sectionName = $_POST['section'];
$className = $_POST['className'];
$position = $_POST['position'];

$eventClasses = EventClasses::create($locationID);

//get event post ids
$event_postIDs = $wpdb->get_results("SELECT post_id FROM ".$wpdb->prefix."em_events WHERE location_id = '".$locationID."' AND UNIX_TIMESTAMP(event_end_date) > '".time()."'",ARRAY_A);

/*
//check if there are registered mice for class to delete
$eventClassRegistrationData = array();
foreach($event_postIDs as $event_postID){
$event_id = $event_postID['post_id'];
$eventClassRegistrationData[$event_id] = get_post_meta($event_id, 'micerule_data_event_class_registrations', true);

if(count($eventClassRegistrationData[$event_id][$sectionName][$position][$className]) > 0){
echo(0);
wp_die();
}
}*/

if($sectionName != "optional")
  $eventClasses->deleteClass($sectionName, $position);
else
  $eventClasses->deleteOptionalClass($position);
$eventClasses->updatePostMeta($locationID);

$locationSecretaries = EventProperties::getLocationSecretaries($locationID);
$locationOptionalSettings = EventOptionalSettings::create($locationID);
$locationSectionTables = new LocationSectionTables($locationID);
echo($locationSectionTables->getSectionTablesHtml($eventClasses, $locationSecretaries, $locationOptionalSettings));
//update_post_meta($locationID, 'micerule_data_event_classes', json_encode($eventClasses, JSON_UNESCAPED_UNICODE));
wp_die();
