<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$entryID = $_POST['entryID'];
$varietyName = $_POST['varietyName'];

EntryBookController::editVarietyName($entryID, $varietyName, new EntryRepository($event_id));
wp_die();
