<?php
global $post;

$url = wp_get_referer();
$event_id = url_to_postid( $url );

$createShowPost = new ShowReportPost($event_id);
$judgesReportService = new JudgesReportService(new JudgesReportRepository($event_id));
echo($createShowPost->getHtml($judgesReportService->prepareReportPostData($event_id, new JudgesRepository())));
$newPost = $createShowPost->createPost($judgesReportService->prepareReportPostData($event_id, new JudgesRepository()));

$postID = wp_insert_post($newPost);
if($postID == 0){
  echo("Something went wrong");
}else{
  echo("Show Post successfully created");
}

wp_die();
