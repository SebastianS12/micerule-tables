<?php
  global $post;
  global $wpdb;

  $sectionName = $_POST['section'];
  $locationID = $_POST['id'];
  /*
  $varietyOptions = $wpdb->get_results("SELECT option_name FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%".$sectionName."'",ARRAY_A);
  $eventClasses = EventClasses::create($locationID);

  $selectOptions = array();
  foreach($varietyOptions as $varietyOption){
    $varietyName = get_option($varietyOption['option_name'])['name'];
    if(!in_array($varietyName, $eventClasses->getSectionClasses($sectionName))){
      array_push($selectOptions, $varietyName);
    }
  }

  echo(json_encode($selectOptions));
  */
  echo(ClassSelectOptions::getClassSelectOptionsHtml($sectionName, $locationID));
  wp_die();
