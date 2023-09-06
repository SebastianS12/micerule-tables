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
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $classPlacementModel);
}

if($prize == "Junior"){
  $juniorPlacementModel = new JuniorPlacement();
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $juniorPlacementModel);
}

if($prize == "Section Challenge"){
  $sectionPlacementModel = new SectionPlacement();
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $sectionPlacementModel);
}

if($prize == "Grand Challenge"){
  $grandChallengePlacementModel = new GrandChallengePlacement();
  EntryBookController::editPlacement($entryID, $placement, $checkValue, $grandChallengePlacementModel);
}

wp_die();