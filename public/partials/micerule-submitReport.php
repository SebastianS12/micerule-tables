<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid($url);
$submitType = $_POST['submitType'];

JudgesReportController::submit($event_id, $submitType);

wp_die();
