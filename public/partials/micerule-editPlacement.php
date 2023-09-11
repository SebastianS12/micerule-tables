<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

//$section = $_POST['section'];
$prize = $_POST['prize'];
$placement = $_POST['placement'];
$entryID = $_POST['entryID'];
$checkValue = ($_POST['checkValue'] == "true");

if($prize == "Class"){
  $classPlacementModel = new ClassPlacement();
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $classPlacementModel, EventProperties::getEventLocationID($event_id));
}

if($prize == "Junior"){
  $juniorPlacementModel = new JuniorPlacement();
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $juniorPlacementModel, EventProperties::getEventLocationID($event_id));
}

if($prize == "Section Challenge"){
  $sectionPlacementModel = new SectionPlacement();
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $sectionPlacementModel, EventProperties::getEventLocationID($event_id));
}

if($prize == "Grand Challenge"){
  $grandChallengePlacementModel = new GrandChallengePlacement();
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $grandChallengePlacementModel, EventProperties::getEventLocationID($event_id));
}

wp_die();