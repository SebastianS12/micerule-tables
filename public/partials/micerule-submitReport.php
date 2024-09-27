<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid($url);
$submitType = $_POST['submitType'];

$judgesReportController = new JudgesReportController(new JudgesReportService(new JudgesReportRepository($event_id)));
$judgesReportController->submit($submitType);

wp_die();
