<?php

class EventClasses implements JsonSerializable{
  public $locationID;
  public $sectionClasses;
  public $optionalClasses;
  public $classIndices;

  public function __construct($locationID){
    $this->locationID = $locationID;
    $this->sectionClasses = array();
    $this->optionalClasses = array();
    $this->classIndices = array();
    $this->updateClassIndices();
  }

  private function updateClassIndices(){
    $classIndex = 1;
    foreach(EventProperties::SECTIONNAMES as $sectionName){
      $sectionName = strtolower($sectionName);
      foreach($this->getSectionClasses($sectionName) as $className){
        $this->classIndices[$className] = array();
        foreach(EventProperties::AGESECTIONS as $age){
          $this->classIndices[$className][$age] = $classIndex;
          $classIndex++;
        }
      }

      foreach(EventProperties::AGESECTIONS as $age){
        $this->classIndices[EventProperties::getChallengeName($sectionName)][$age] = $classIndex;
        $classIndex++;
      }
    }

    foreach(EventProperties::AGESECTIONS as $age){
      $this->classIndices["GRAND CHALLENGE"][$age] = $classIndex;
      $classIndex++;
    }

    foreach($this->optionalClasses as $className){
        $this->classIndices[$className]['AA'] = $classIndex;
        $classIndex++;
    }
  }

  public function getClassIndex($className, $age){
    $classIndex = 0;
    if(isset($this->classIndices[$className][$age]))
      $classIndex = $this->classIndices[$className][$age];

    return $classIndex;
  }

  public function addClass($sectionName, $className){
    if(!isset($this->sectionClasses[$sectionName])){
      $this->sectionClasses[$sectionName] = array();
    }

    array_push($this->sectionClasses[$sectionName], $className);
    $this->updateClassIndices();
  }

  public function addOptionalClass($className){
    array_push($this->optionalClasses, $className);
    $this->updateClassIndices();
  }

  public function deleteOptionalClass($position){
    if(isset($this->optionalClasses[$position])){
      array_splice($this->optionalClasses, $position, 1);
    }
    $this->updateClassIndices();
  }

  public function deleteClass($sectionName, $position){
    if(isset($this->getSectionClasses($sectionName)[$position])){
      array_splice($this->sectionClasses[$sectionName], $position, 1);
    }
    $this->updateClassIndices();
  }

  public function moveClass($sectionName, $position, $direction){
    if(isset($this->getSectionClasses($sectionName)[$position]) && isset($this->getSectionClasses($sectionName)[$position + $direction])){
        $classToMove = $this->getSectionClasses($sectionName)[$position];
        $this->sectionClasses[$sectionName][$position] = $this->sectionClasses[$sectionName][$position + $direction];
        $this->sectionClasses[$sectionName][$position + $direction] = $classToMove;
    }
    $this->updateClassIndices();
  }

  public function moveOptionalClass($position, $direction){
    if(isset($this->optionalClasses[$position]) && isset($this->optionalClasses[$position + $direction])){
        $classToMove = $this->optionalClasses[$position];
        $this->optionalClasses[$position] = $this->optionalClasses[$position + $direction];
        $this->optionalClasses[$position + $direction] = $classToMove;
    }
    $this->updateClassIndices();
  }

  public function getSectionClasses($sectionName){
    $sectionClasses = array();
    if(isset($this->sectionClasses[$sectionName]))
      $sectionClasses = $this->sectionClasses[$sectionName];

    return $sectionClasses;
  }

  public function getSectionClassCount($sectionName){
    $sectionClassCount = 0;
    if(isset($this->sectionClasses[$sectionName]))
      $sectionClassCount = count($this->sectionClasses[$sectionName]);

    return $sectionClassCount;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public function updatePostMeta($locationID){
    update_post_meta($locationID, 'micerule_data_event_classes', json_encode($this, JSON_UNESCAPED_UNICODE));
  }

  public static function createFromJson($json){
    $jsonObject = json_decode($json);

    $eventClasses = new EventClasses($jsonObject->locationID);
    foreach($jsonObject->sectionClasses as $sectionName => $sectionClasses){
      foreach($sectionClasses as $sectionClass){
        $eventClasses->addClass($sectionName, $sectionClass);
      }
    }

    if(isset($jsonObject->optionalClasses))
      $eventClasses->optionalClasses = $jsonObject->optionalClasses;

    foreach($jsonObject->classIndices as $className => $ageIndices){
      foreach($ageIndices as $age => $classIndex){
        $eventClasses->classIndices[$className][$age] = $classIndex;
      }
    }

    return $eventClasses;
  }

  public static function create($locationID){
    $eventClasses = new EventClasses($locationID);
    $eventClassesJson = get_post_meta($locationID, 'micerule_data_event_classes', true);
    if($eventClassesJson != "")
      $eventClasses = EventClasses::createFromJson($eventClassesJson);

    return $eventClasses;
  }
}
