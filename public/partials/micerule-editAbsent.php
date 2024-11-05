<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );
$entryID = $_POST['entryID'];

$entriesService = new EntriesService(new EntryRepository($event_id));
$entryBookController = new EntryBookController();
$entryBookController->editEntryAbsent($entriesService, intval($entryID));
wp_die();
