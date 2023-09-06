<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );
$penNumber = $_POST['penNumber'];

EntryBookController::deleteEntry($event_id, $penNumber);
wp_die();
