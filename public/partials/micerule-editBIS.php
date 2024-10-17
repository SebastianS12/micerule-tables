<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$prizeID = $_POST['prizeID'];
$bisChallengeIndexID = $_POST['challengeIndexID'];
$boaChallengeIndexID = $_POST['oaChallengeIndexID'];

$placementsRepository = new PlacementsRepository($event_id, new ChallengePlacementDAO());
$awardsRepository = new AwardsRepository($event_id);
$challengeRowService = new ChallengeRowService($event_id);
$entryBookController = new EntryBookController();
$entryBookController->editAwards($challengeRowService, $placementsRepository, $awardsRepository, $prizeID, $bisChallengeIndexID, $boaChallengeIndexID);

wp_die();
