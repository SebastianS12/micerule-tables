<?php
global $post;
global $wpdb;

$id = intval($_POST['id']);
$locationID = $_POST['locationID'];
$allowOnlineRegistrations = ($_POST['allowOnlineRegistrations'] == "true");
$registrationFee = $_POST['registrationFee'];
$allowUnstandardised = ($_POST['allowUnstandardised'] == "true");
$allowJunior = ($_POST['allowJunior'] == "true");
$allowAuction = ($_POST['allowAuction'] == "true");
$firstPrize = $_POST['firstPrize'];
$secondPrize = $_POST['secondPrize'];
$thirdPrize = $_POST['thirdPrize'];
$pmBiSec = $_POST['pmBiSec'];
$pmBoSec = $_POST['pmBoSec'];
$pmBIS = $_POST['pmBIS'];
$pmBOA = $_POST['pmBOA'];

ShowOptionsController::saveShowOptions($id, $locationID, $allowOnlineRegistrations, $registrationFee, $firstPrize, $secondPrize, $thirdPrize, $allowUnstandardised, $allowJunior, $allowAuction, $pmBiSec, $pmBoSec, $pmBIS, $pmBOA);
wp_die();
