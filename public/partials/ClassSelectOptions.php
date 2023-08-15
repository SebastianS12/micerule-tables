<?php

class ClassSelectOptions {
  private static $instance;
  private $varietyOptions;
  private $eventClasses;

  private function __construct($locationID){
    $varietyOptions = array();
    /*
    global $wpdb;
    foreach(EventProperties::SECTIONNAMES as $sectionName){
      $sectionName = strtolower($sectionName);
      $this->varietyOptions[$sectionName] = $wpdb->get_results("SELECT option_name FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%".$sectionName."'",ARRAY_A);
    }*/
    $this->eventClasses = EventClasses::create($locationID);
  }

  public static function getClassSelectOptionsHtml($sectionName, $locationID, $selectedVariety = ""){
    if(!isset(self::$instance))
      self::$instance = new ClassSelectOptions($locationID);

    $selectOptions = self::getClassSelectOptions($locationID, $sectionName);
    $optionsHtml = "";
    foreach($selectOptions as $selectOption){
      $optionSelected = ($selectedVariety == $selectOption) ? "selected" : "";
      $optionsHtml .= "<option value = '".$selectOption."' ".$optionSelected.">".$selectOption."</option>";
    }

    return $optionsHtml;
  }

  private static function getClassSelectOptions($locationID, $sectionName){
    $selectOptions = array();
    $sectionBreedNamesSQL = Breed::getSectionBreedNames($sectionName);
    $showClassesModel = new ShowClassesModel();
    $showSectionClassNames = $showClassesModel->getShowSectionClassNames($locationID, $sectionName);
    if($sectionBreedNamesSQL != null){
      foreach($sectionBreedNamesSQL as $breedNameSQLResult){
        if(!in_array($breedNameSQLResult['name'], $showSectionClassNames)){
          array_push($selectOptions, $breedNameSQLResult['name']);
        }
      }
    }

    return $selectOptions;
  }
}
