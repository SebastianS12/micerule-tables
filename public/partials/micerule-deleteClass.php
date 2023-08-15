<?php
global $post;
global $wpdb;

$locationID = $_POST['id'];
$className = $_POST['className'];

ShowOptionsController::deleteClass($locationID, $className);
echo(ShowOptionsView::getSectionTablesHtml($locationID));
wp_die();
