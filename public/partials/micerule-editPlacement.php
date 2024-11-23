<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

//$section = $_POST['section'];
$prize = $_POST['prize'];
$placement = $_POST['placement'];
$indexID = $_POST['indexID'];
$entryID = $_POST['entryID'];


$entryBookController = new EntryBookController();
$entryBookController->editPlacement($event_id, new PlacementsRowService(), $placement, $indexID, $entryID, $prize);

wp_die();