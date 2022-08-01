<?php

/*
*	deletes given icon file 
*
*/

global $post;

//Get Uploads for Select
$paths = get_option("mrOption_paths");
$uploads = array();

foreach($paths as $value){
	if(! in_array($value,$uploads)){
		array_push($uploads,$value);
	}
}



$deleteFile= $_POST['path'];

$path = parse_url($deleteFile, PHP_URL_PATH);
$fullPath = get_home_path() . $path;

wp_delete_file($fullPath);
echo var_dump(parse_url($deleteFile)['host'].parse_url($deleteFile)['path']);

$optionUpdate= array();
//update path option
foreach($paths as $key=> $value){
	if($value != $deleteFile){
		$optionUpdate[$key]=$value;
	}
}
update_option("mrOption_paths",$optionUpdate);

wp_die();
