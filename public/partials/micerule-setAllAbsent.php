<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$userName = $_POST['userName'];
$absent = ($_POST['checkValue'] == "true");

EntrySummaryController::setAllAbsent($event_id, $absent, $userName);

wp_die();
