<?php

/*
*	returns an array of all icon paths either as php require or through an ajax call
*
*/

global $post;

$paths = get_option("mrOption_paths");
$uploads = array();

//php require
foreach($paths as $value){
	if(! in_array($value,$uploads)){
    	array_push($uploads,$value);
}
}

//ajax call
if(wp_doing_ajax()){
	foreach($uploads as $value){
    	$id = basename($value,".".pathinfo($value)['extension']);
    	$html .= "<option value='".$value."'>".$id."</option> ";
    }
    echo $html;
    wp_die();
}
