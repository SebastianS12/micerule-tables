<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$prize = $_POST['prize'];
$section = $_POST['section'];
$age = $_POST['age'];
$checkValue = $_POST['checkValue'];

if($prize == "Section Challenge"){
  $sectionChallengeAwardsModel = new SectionChallengeAwards($event_id, $section);
  EntryBookController::editBIS($age,$checkValue, $sectionChallengeAwardsModel);
}

if($prize == "Grand Challenge"){
  $grandChallengeAwardsModel = new GrandChallengeChallengeAwards($event_id, $section);
  EntryBookController::editBIS($age,$checkValue, $grandChallengeAwardsModel);
}

wp_die();
