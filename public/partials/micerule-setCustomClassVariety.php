<?php
global $post;

$entryID = $_POST['entryID'];
$varietyName = $_POST['varietyName'];

EntryBookController::editVarietyName($entryID, $varietyName);
wp_die();
