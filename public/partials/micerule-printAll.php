<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

// $prizeCardsData = json_decode(html_entity_decode(stripslashes($_POST['prizeCardsData'])));
// $print = ($_POST['print'] == "true");

// PrizeCardsController::updatePrizeCardsPrinted($event_id, $prizeCardsData, $print);
$prizeCardsService = new PrizeCardsService(new PrizeCardsRepository());
$prizeCardsController = new PrizeCardsController($prizeCardsService);
$prizeCardsController->printAll($event_id);
wp_die();
