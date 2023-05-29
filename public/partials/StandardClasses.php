<?php

class StandardClasses {

  public function __construct(){
    global $wpdb;
    $this->wpdb = $wpdb;

    $this->sectionStandardClasses = array();
    foreach(EventProperties::SECTIONNAMES as $sectionName){
      $this->sectionStandardClasses[strtolower($sectionName)] = $this->getSectionStandardClasses($sectionName);
    }
  }

  private function getSectionStandardClasses($sectionName){
    $varietyOptions = $this->wpdb->get_results("SELECT option_name FROM ".$this->wpdb->prefix."options WHERE option_name LIKE 'mrTables%".$sectionName."'",ARRAY_A);

    $standardClasses = array();
    foreach($varietyOptions as $varietyOption){
      $className = get_option($varietyOption['option_name'])['name'];
      array_push($standardClasses, $className);
    }

    return $standardClasses;
  }

  public function isStandardClass($className, $sectionName){
    return (isset($this->sectionStandardClasses[$sectionName]) && in_array($className, $this->sectionStandardClasses[$sectionName]));
  }
}
