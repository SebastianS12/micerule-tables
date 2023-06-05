<?php

/*
*	returns an array of all icon paths either as php require or through an ajax call
*
*/

global $post;
global $wpdb;

$breed_icon_urls = $wpdb->get_results("SELECT icon_url FROM " . $wpdb->prefix . "micerule_breeds", ARRAY_A);
$icon_urls = array();

//php require
foreach($breed_icon_urls as $breed_icon_url){
    array_push($icon_urls, $breed_icon_url['icon_url']);
}
$icon_urls = array_unique($icon_urls);

//ajax call
if(wp_doing_ajax()){
	$html = "";
	foreach($icon_urls as $icon_url){
    	$iconName = basename($icon_url,".".pathinfo($icon_url)['extension']);
    	$html .= "<option value='".$icon_url."'>".$iconName."</option> ";
    }
    echo $html;
    wp_die();
}
