<?php
global $post;

$locationID = $_POST['id'];
$sectionName = $_POST['section'];
$className = $_POST['className'];

ShowClassesController::addClass($locationID, $className, $sectionName);
echo(ShowOptionsView::getSectionTablesHtml($locationID));

// $model = new ShowClassesModel();
// $model->convertPostMeta();
wp_die();
