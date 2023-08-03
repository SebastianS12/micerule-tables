<?php

/*
* inserts data for season table into db 
*
*/

global $post;

$dateTo = (int) $_POST['dateTo'];
$dateFrom = (int)$_POST['dateFrom'];

SeasonResultsController::createSeasonTable($dateFrom, $dateTo);
//Drop Season Results Column
//$wpdb->query("ALTER TABLE ".$wpdb->prefix."micerule_result_tables DROP COLUMN season_results");

wp_die();
