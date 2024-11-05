<?php
global $post;
global $wpdb;

$locationID = $_POST['id'];
$allowOnlineRegistrations = ($_POST['allowOnlineRegistrations'] == "true");
$registrationFee = $_POST['registrationFee'];
$allowUnstandardised = ($_POST['allowUnstandardised'] == "true");
$allowJunior = ($_POST['allowJunior'] == "true");
$allowAuction = ($_POST['allowAuction'] == "true");
$firstPrize = $_POST['firstPrize'];
$secondPrize = $_POST['secondPrize'];
$thirdPrize = $_POST['thirdPrize'];

ShowOptionsController::saveShowOptions($locationID, $allowOnlineRegistrations, $registrationFee, $firstPrize, $secondPrize, $thirdPrize, $allowUnstandardised, $allowJunior, $allowAuction);
wp_die();
