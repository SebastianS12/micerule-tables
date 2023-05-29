<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$prize = $_POST['prize'];
$section = $_POST['section'];
$age = $_POST['age'];
$oppositeAge = $_POST['oppositeAge'];
$checkValue = $_POST['checkValue'];

$entryBookData = EntryBookData::create($event_id);
$entry = $entryBookData->entries[$penNumber];

if($prize == "Section Challenge"){
  $sectionData = $entryBookData->sections[$section];
  $sectionData->setBIS($age, $oppositeAge, $checkValue);
}

if($prize == "Grand Challenge"){
  $grandChallengeData = $entryBookData->grandChallenge;
  $grandChallengeData->setBIS($age, $oppositeAge, $checkValue);
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
echo(AdminTabs::getAdminTabsHtml($event_id));

wp_die();
