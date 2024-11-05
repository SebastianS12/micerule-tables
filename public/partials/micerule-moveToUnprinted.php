<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$placementID = intval($_POST['placementID']);
$prizeID = intval($_POST['prizeID']);

$prizeCardsService = new PrizeCardsService(new PrizeCardsRepository());
$prizeCardsController = new PrizeCardsController($prizeCardsService);
$prizeCardsController->moveToUnprinted($placementID, $prizeID);
wp_die();