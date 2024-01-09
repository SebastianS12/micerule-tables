<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$entryBookData = EntryBookData::create($event_id);
$submitType = $_POST['submitType'];

if($submitType == "classReport"){
  $className = $_POST['className'];
  $age = $_POST['age'];
  $classComments = $_POST['classComments'];
  $placementReportData = json_decode(html_entity_decode(stripslashes($_POST['placementReportData'])));

  if(isset($entryBookData->classes[$className])){
    $entryBookData->classes[$className]->judgesComments[$age] = $classComments;
    $prizeData = $entryBookData->classes[$className]->getPlacementData($age);
    foreach($placementReportData as $placementReport){
      $prizeData->placements[$placementReport->placement]->buck = $placementReport->buckChecked;
      $prizeData->placements[$placementReport->placement]->doe = $placementReport->doeChecked;
      $prizeData->placements[$placementReport->placement]->judgesComments = sanitize_text_field($placementReport->reportText);
    }
  }
}

if($submitType == "generalComment"){
  $judgeName = $_POST['judgeName'];
  $text = html_entity_decode(stripslashes($_POST['text']));

  if($judgeName != "")
    $entryBookData->judgesComments[$judgeName] = sanitize_text_field($text);
}

$entryBookJson = json_encode($entryBookData, JSON_UNESCAPED_UNICODE);
if($entryBookJson)
  update_post_meta($event_id, 'micerule_data_event_entry_book_test', $entryBookJson);
else
  echo(-1);
wp_die();
