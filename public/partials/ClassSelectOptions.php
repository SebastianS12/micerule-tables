<?php

class ClassSelectOptions {
  private static $instance;
  private $varietyOptions;
  private $eventClasses;

  private function __construct($locationID){
    $varietyOptions = array();
    global $wpdb;
    foreach(EventProperties::SECTIONNAMES as $sectionName){
      $sectionName = strtolower($sectionName);
      $this->varietyOptions[$sectionName] = $wpdb->get_results("SELECT option_name FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%".$sectionName."'",ARRAY_A);
    }
    $this->eventClasses = EventClasses::create($locationID);
  }

  public static function getClassSelectOptionsHtml($sectionName, $locationID, $selectedVariety = ""){
    if(!isset(self::$instance))
      self::$instance = new ClassSelectOptions($locationID);

    $selectOptions = array();
    foreach(self::$instance->varietyOptions[$sectionName] as $varietyOption){
      $varietyName = get_option($varietyOption['option_name'])['name'];
    if(!in_array($varietyName, self::$instance->eventClasses->getSectionClasses($sectionName))){
        array_push($selectOptions, $varietyName);
      }
    }

    $optionsHtml = "";
    foreach($selectOptions as $selectOption){
      $optionSelected = ($selectedVariety == $selectOption) ? "selected" : "";
      $optionsHtml .= "<option value = '".$selectOption."' ".$optionSelected.">".$selectOption."</option>";
    }

    return $optionsHtml;
  }
}
