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

$optionalSettings = new EventOptionalSettings($allowOnlineRegistrations, $registrationFee, $allowUnstandardised, $allowJunior, $allowAuction, $firstPrize, $secondPrize, $thirdPrize);

update_post_meta($locationID, 'micerule_data_location_optional_settings', json_encode($optionalSettings));
wp_die();
