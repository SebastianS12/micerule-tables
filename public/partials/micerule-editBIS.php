<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

// $prize = $_POST['prize'];
// $section = $_POST['section'];
// $age = $_POST['age'];
// $checkValue = ($_POST['checkValue'] == "true");
$prizeID = $_POST['prizeID'];
$bisChallengeIndexID = $_POST['challengeIndexID'];
$boaChallengeIndexID = $_POST['oaChallengeIndexID'];

$challengeRowService = new ChallengeRowService($event_id, new PlacementsRepository(new ChallengePlacementDAO()), new ChallengeIndexRepository(EventProperties::getEventLocationID($event_id)), new EntryRepository($event_id), new UserRegistrationsRepository($event_id), new AwardsRepository());
$challengeRowController = new ChallengeRowController($challengeRowService);
$challengeRowController->editAwards($prizeID, $bisChallengeIndexID, $boaChallengeIndexID);

// if($prize == "Section Challenge"){
//   $sectionChallengeAwardsModel = new SectionChallengeAwards($event_id, $section);
//   EntryBookController::editBIS($age,$checkValue, $sectionChallengeAwardsModel);
// }

// if($prize == "Grand Challenge"){
//   $grandChallengeAwardsModel = new GrandChallengeAwards($event_id, $section);
//   EntryBookController::editBIS($age,$checkValue, $grandChallengeAwardsModel);
// }

wp_die();
