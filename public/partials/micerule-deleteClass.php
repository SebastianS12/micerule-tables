<?php
global $post;
global $wpdb;

$locationID = $_POST['id'];
$classID = $_POST['classID'];
$section = $_POST['section'];

$showClassesController = new ShowClassesController();
$showClassesController->deleteClass($classID, $locationID, $section);
echo(ShowOptionsView::getSectionTablesHtml($locationID));
// echo(ShowClassesView::render($locationID));
wp_die();
