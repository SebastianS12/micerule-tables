<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );
$penNumber = $_POST['penNumber'];
$checkValue = ($_POST['checkValue'] == "true");

EntryBookController::editEntryAbsent($event_id, $penNumber, $checkValue);
wp_die();
