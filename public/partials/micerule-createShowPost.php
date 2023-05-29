<?php
global $post;

$url = wp_get_referer();
$event_id = url_to_postid( $url );

$createShowPost = new ShowReportPost($event_id);
$newPost = $createShowPost->createPost();


$postID = wp_insert_post($newPost);
if($postID == 0){
  echo("Something went wrong");
}else{
  echo("Show Post successfully created");
}

wp_die();
