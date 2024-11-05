<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$prizeCardsData = $_POST['prizeCardsData'];

$prizeCardsService = new PrizeCardsService(new PrizeCardsRepository());
$prizeCardsController = new PrizeCardsController($prizeCardsService);
$prizeCardsController->printAll($prizeCardsData);
wp_die();
