<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );
$entryID = intval($_POST['entryID']);

$entriesService = new EntriesService(new EntryRepository($event_id));
$entryBookController = new EntryBookController();
$entryBookController->deleteEntry($entriesService, $entryID);
wp_die();
