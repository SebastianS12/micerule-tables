<?php
global $post;
global $wpdb;

$url     = wp_get_referer();
$eventID = url_to_postid( $url );
$userName = $_POST['userName'];

$registrationTables = new RegistrationTables($eventID, $userName);

echo(json_encode($registrationTables->getHtml()));
wp_die();
