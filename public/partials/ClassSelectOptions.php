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
    $sectionBreedNames = Breed::getSectionBreedNames($sectionName);
    $showClassesModel = new ShowClassesModel();
    $showSectionClassNames = $showClassesModel->getShowSectionClassNames($locationID, $sectionName);
    if($sectionBreedNames != null){
      foreach($sectionBreedNames as $breedName){
        if(!in_array($breedName, $showSectionClassNames)){
          array_push($selectOptions, $breedName);
        }
      }
    }

    return $selectOptions;
  }
}
