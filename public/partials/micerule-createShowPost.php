<?php
global $post;

$url = wp_get_referer();
$event_id = url_to_postid( $url );

$viewModel = ShowReportPostController::prepareViewModel(LocationHelper::getIDFromEventPostID($event_id), $event_id);
$newPost = ShowReportPostController::createPost($viewModel);

$postID = wp_insert_post($newPost);
if($postID == 0){
  echo("");
}else{
  update_post_meta($event_id, "show_report_post_id", $postID);
  echo(get_post_permalink($postID));
}

wp_die();
