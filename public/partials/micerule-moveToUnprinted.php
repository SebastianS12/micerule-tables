<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$placementID = $_POST['placementID'];
$prizeID = $_POST['prizeID'];

$prizeCardsService = new PrizeCardsService(new PrizeCardsRepository());
$prizeCardsController = new PrizeCardsController($prizeCardsService);
$prizeCardsController->moveToUnprinted($placementID, $prizeID);
wp_die();