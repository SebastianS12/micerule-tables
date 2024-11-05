<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$classIndexID = $_POST['classIndexID'];
$userName = $_POST['userName'];

$locationID = LocationHelper::getIDFromEventPostID($event_id);
$entryBookService = new EntryBookService();
EntryBookController::addEntry($entryBookService, $event_id, $locationID, $userName, $classIndexID);
wp_die();
