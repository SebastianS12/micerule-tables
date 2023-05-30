<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

//$section = $_POST['section'];
$prize = $_POST['prize'];
$placement = $_POST['placement'];
$penNumber = $_POST['penNumber'];
$checkValue = $_POST['checkValue'];

$entryBookData = EntryBookData::create($event_id);
$entry = $entryBookData->entries[$penNumber];
//$prizeEntry = new PrizeEntry($entry->penNumber, $entry->userName, $entry->age, $entry->className, $entry->varietyName, $entry->sectionName, $entry->moved, $entry->absent, false);

if($prize == "Class"){
  $entryBookData->classes[$entry->className]->getPlacementData($entry->age)->editPlacement($placement, $entry, $checkValue);
}

if($prize == "Section Challenge"){
  $entryBookData->sections[$entry->sectionName]->getPlacementData($entry->age)->editPlacement($placement, $entry, $checkValue);
}

if($prize == "Grand Challenge"){
  $entryBookData->grandChallenge->getPlacementData($entry->age)->editPlacement($placement, $entry, $checkValue);
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
wp_die();