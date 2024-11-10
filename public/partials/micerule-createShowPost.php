<?php
global $post;

$url = wp_get_referer();
$event_id = url_to_postid( $url );

$viewModel = ShowReportPostController::prepareViewModel(LocationHelper::getIDFromEventPostID($event_id), $event_id);
$newPost = ShowReportPostController::createPost($viewModel);

$postID = wp_insert_post($newPost);
if($postID == 0){
  echo("Something went wrong");
}else{
  echo("Show Post successfully created");
  update_post_meta($event_id, "show_report_post_id", $postID);
}

wp_die();
