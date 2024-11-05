<?php

global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$entryID = $_POST['entryID'];
$newClassIndexID = $_POST['newClassIndexID'];


$locationID = LocationHelper::getIDFromEventPostID($event_id);
$entryBookService = new EntryBookService();
$entryBookService->moveEntry($entryID, $newClassIndexID, new EntryRepository($event_id), new ClassIndexRepository($locationID), new UserRegistrationsRepository($event_id), new RegistrationOrderRepository($event_id));
// EntryBookController::moveEntry($event_id, $penNumber, $newClassName, $newAge);
wp_die();
