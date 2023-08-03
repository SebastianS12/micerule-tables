<?php

/*
* deletes table from db, based on id 
*
*/

global $post;

//Get Table ID to delete
$id = $_POST['id'];
SeasonResultsController::deleteSeasonTable($id);

wp_die();
