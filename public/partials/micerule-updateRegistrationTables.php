<?php
global $post;
global $wpdb;

$url     = wp_get_referer();
$eventID = url_to_postid( $url );
$userName = $_POST['userName'];

echo(RegistrationTablesView::getRegistrationTablesHtml($eventID, $userName));
wp_die();
