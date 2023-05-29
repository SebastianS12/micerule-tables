<?php
  global $post;

  $locationID = $_POST['id'];
  $sectionName = $_POST['section'];
  $className = $_POST['className'];

  $eventClasses = EventClasses::create($locationID);
  if($sectionName != "optional")
    $eventClasses->addClass($sectionName, $className);
  else
    $eventClasses->addOptionalClass($className);
  $eventClasses->updatePostMeta($locationID);

  $locationSecretaries = EventProperties::getLocationSecretaries($locationID);
  $locationOptionalSettings = EventOptionalSettings::create($locationID);

  $locationSectionTables = new LocationSectionTables($locationID);
  echo($locationSectionTables->getSectionTablesHtml($eventClasses, $locationSecretaries, $locationOptionalSettings));
  //update_post_meta($locationID, 'micerule_data_event_classes', json_encode($eventClasses, JSON_UNESCAPED_UNICODE));
  wp_die();
