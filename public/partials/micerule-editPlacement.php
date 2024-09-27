<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

//$section = $_POST['section'];
$prize = $_POST['prize'];
$placement = $_POST['placement'];
$indexID = $_POST['indexID'];
$entryID = $_POST['entryID'];

//TODO: Factory Pattern?
$entryBookPlacementController = new EntryBookPlacementController();
if($prize == "Class"){
  //$classPlacementModel = new ClassPlacement();
  $classPlacementsRepository = new PlacementsRepository(new ClassPlacementDAO());
  $entryBookPlacementController->editPlacement($event_id, $classPlacementsRepository, $placement, $indexID, $entryID, Prize::STANDARD);
  //EntryBookController::editPlacement($entryID, $placement, $checkValue, $classPlacementModel, EventProperties::getEventLocationID($event_id));
}

if($prize == "Junior"){
  $classPlacementsRepository = new PlacementsRepository(new ClassPlacementDAO());
$entryBookPlacementController->editPlacement($event_id, $classPlacementsRepository, $placement, $indexID, $entryID, Prize::JUNIOR);
}

if($prize == "Section Challenge"){
  // $sectionPlacementModel = new SectionPlacement();
  // EntryBookController::editPlacement($entryID, $placement, $checkValue, $sectionPlacementModel, EventProperties::getEventLocationID($event_id));
  $challengePlacementsRepository = new PlacementsRepository(new ChallengePlacementDAO());
  $entryBookPlacementController->editPlacement($event_id, $challengePlacementsRepository, $placement, $indexID, $entryID, Prize::SECTION);
}

if($prize == "Grand Challenge"){
  // $sectionPlacementModel = new SectionPlacement();
  // EntryBookController::editPlacement($entryID, $placement, $checkValue, $sectionPlacementModel, EventProperties::getEventLocationID($event_id));
  $challengePlacementsRepository = new PlacementsRepository(new ChallengePlacementDAO());
  $entryBookPlacementController->editPlacement($event_id, $challengePlacementsRepository, $placement, $indexID, $entryID, Prize::GRANDCHALLENGE);
}

// if($prize == "Grand Challenge"){
//   $grandChallengePlacementModel = new GrandChallengePlacement();
//   EntryBookController::editPlacement($entryID, $placement, $checkValue, $grandChallengePlacementModel, EventProperties::getEventLocationID($event_id));
// }

wp_die();