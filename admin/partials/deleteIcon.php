<?php
/*
*	deletes given icon file 
*
*/

global $post;
$iconPath = $_POST['path'];
if (issest($iconPath)) {
	$deleteFile = $_POST['path'];
	$path = parse_url($deleteFile, PHP_URL_PATH);
	$fullPath = get_home_path() . $path;
	wp_delete_file($fullPath);
}
wp_die();
