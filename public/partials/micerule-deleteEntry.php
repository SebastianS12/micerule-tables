<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );
$entryID = intval($_POST['entryID']);

$entryBookController = new EntryBookController();
$entryBookController->deleteEntry($entryID, $event_id);
wp_die();
