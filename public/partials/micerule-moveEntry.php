<?php

global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$newSection = $_POST['newSection'];
$newClassName = $_POST['newClassName'];
$newAge = $_POST['newAge'];
$penNumber = $_POST['penNumber'];

EntryBookController::moveEntry($event_id, $penNumber, $newClassName, $newAge);
wp_die();
