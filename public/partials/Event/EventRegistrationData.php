<?php

class EventRegistrationData implements JsonSerializable {
  public $classRegistrationData;
  public $optionalClassRegistrationData;
  public $userRegistrationData;

  public function __construct(){
    $this->classRegistrationData = array();
    $this->optionalClassRegistrationData = array();
    $this->userRegistrationData = array();
  }

  public function addClassRegistration($userName, $className, $age, $juvenileMember){
    if(!isset($this->classRegistrationData[$className]))
      $this->classRegistrationData[$className] = new ClassRegistrationData($className);

    $registrationIndex = $this->classRegistrationData[$className]->newRegistrationIndex;
    $this->classRegistrationData[$className]->addRegistration($userName, $age, $juvenileMember);

    if(!isset($this->userRegistrationData[$userName]))
      $this->userRegistrationData[$userName] = new UserRegistrationData($userName);

    $this->userRegistrationData[$userName]->addRegistration($className, $age, $registrationIndex, $juvenileMember);
  }

  public function addOptionalClassRegistration($userName, $className, $age){
    if(!isset($this->optionalClassRegistrationData[$className]))
      $this->optionalClassRegistrationData[$className] = new ClassRegistrationData($className);

    $registrationIndex = $this->optionalClassRegistrationData[$className]->newRegistrationIndex;
    $this->optionalClassRegistrationData[$className]->addRegistration($userName, $age, false);

    if(!isset($this->userRegistrationData[$userName]))
      $this->userRegistrationData[$userName] = new UserRegistrationData($userName);

    $this->userRegistrationData[$userName]->addRegistration($className, $age, $registrationIndex, false);
  }

  public function removeClassRegistration($userName, $className, $age, $juvenileMember){
    $classRegistrationIndex = $this->userRegistrationData[$userName]->removeRegistration($className, $age, $juvenileMember);
    $this->classRegistrationData[$className]->removeRegistration($age, $classRegistrationIndex, $juvenileMember);
  }

  public function removeOptionalClassRegistration($userName, $className, $age){
    $classRegistrationIndex = $this->userRegistrationData[$userName]->removeRegistration($className, $age, false);
    $this->optionalClassRegistrationData[$className]->removeRegistration($age, $classRegistrationIndex, false);
  }

  public function getClassRegistrationData($className){
    if(!isset($this->classRegistrationData[$className]))
      $this->classRegistrationData[$className] = new ClassRegistrationData($className);

    return $this->classRegistrationData[$className];
  }

  public function getOptionalClassRegistrationData($className){
    if(!isset($this->optionalClassRegistrationData[$className]))
      $this->optionalClassRegistrationData[$className] = new ClassRegistrationData($className);

    return $this->optionalClassRegistrationData[$className];
  }

  public function getUserRegistrationData($userName){
    if(!isset($this->userRegistrationData[$userName]))
      $this->userRegistrationData[$userName] = new UserRegistrationData($userName);

    return $this->userRegistrationData[$userName];
  }

  public function getJuvenileRegistrationCount(){
    $juvenileRegistrationCount = 0;
    foreach($this->classRegistrationData as $classRegistrationData){
      $juvenileRegistrationCount += $classRegistrationData->juvenileRegistrationCount;
    }
    return $juvenileRegistrationCount;
  }

  public function getGrandChallengeRegistrationCount($age){
    $grandChallengeRegistrationCount = 0;
    foreach($this->classRegistrationData as $classRegistrationData){
      $grandChallengeRegistrationCount += $classRegistrationData->getRegistrationCount($age);
    }
    return $grandChallengeRegistrationCount;
  }

  public function getEntryCount(){
    $entryCount = 0;
    foreach($this->classRegistrationData as $classRegistrationData){
      $entryCount += 3 * $classRegistrationData->getRegistrationCount("Ad");
      $entryCount += 3 * $classRegistrationData->getRegistrationCount("U8");
      $entryCount += $classRegistrationData->juvenileRegistrationCount;
    }
    foreach($this->optionalClassRegistrationData as $optionalClassRegistrationData){
      $entryCount += $optionalClassRegistrationData->getRegistrationCount("AA");
    }

    return $entryCount;
  }

  public function getExhibitCount(){
    $exhibitCount = 0;
    $exhibitCount += $this->getGrandChallengeRegistrationCount("Ad");
    $exhibitCount += $this->getGrandChallengeRegistrationCount("U8");
    foreach($this->optionalClassRegistrationData as $optionalClassRegistrationData){
      $exhibitCount += $optionalClassRegistrationData->getRegistrationCount("AA");
    }

    return $exhibitCount;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJson($json){
    $jsonObject = json_decode($json);

    $eventRegistrationData = new EventRegistrationData();
    foreach($jsonObject->classRegistrationData as $className => $classRegistrationData){
      $eventRegistrationData->classRegistrationData[$className] = ClassRegistrationData::createFromJsonObject($classRegistrationData);
    }

    foreach($jsonObject->optionalClassRegistrationData as $className => $classRegistrationData){
      $eventRegistrationData->optionalClassRegistrationData[$className] = ClassRegistrationData::createFromJsonObject($classRegistrationData);
    }

    foreach($jsonObject->userRegistrationData as $userName => $userRegistrationData){
      $eventRegistrationData->userRegistrationData[$userName] = UserRegistrationData::createFromJsonObject($userRegistrationData);
    }

    return $eventRegistrationData;
  }

  public static function create($eventID){
    $eventRegistrationData = new EventRegistrationData();
    $eventRegistrationDataJson = get_post_meta($eventID, 'micerule_data_event_registrations', true);
    if($eventRegistrationDataJson != "")
      $eventRegistrationData = EventRegistrationData::createFromJson($eventRegistrationDataJson);

    return $eventRegistrationData;
  }

  public function updatePostMeta($eventID){
    update_post_meta($eventID, 'micerule_data_event_registrations', json_encode($this, JSON_UNESCAPED_UNICODE));
  }
}


class ClassRegistrationData implements JsonSerializable {
  public $className;
  public $classRegistrations;
  public $juvenileRegistrationCount;
  public $newRegistrationIndex;

  public function __construct($className){
    $this->className = $className;
    $this->classRegistrations = array();
    $this->juvenileRegistrationCount = 0;
    $this->newRegistrationIndex = 0;
  }

  public function addRegistration($userName, $age, $juvenileMember){
    if(!isset($this->classRegistrations[$age]))
      $this->classRegistrations[$age] = array();

    if($juvenileMember)
      $this->juvenileRegistrationCount++;

    $this->classRegistrations[$age][$this->newRegistrationIndex] = new ClassRegistration($userName, $juvenileMember);
    $this->newRegistrationIndex++;
  }

  public function removeRegistration($age, $classRegistrationIndex, $juvenileMember){
    if($juvenileMember)
      $this->juvenileRegistrationCount--;

    unset($this->classRegistrations[$age][$classRegistrationIndex]);
  }

  public function getClassIndex($age){
    $classIndex = 0;
    if(isset($this->classIndex[$age]))
      $classIndex = $this->classIndex[$age];

    return $classIndex;
  }

  public function getRegistrationCount($age){
    $registrationCount = 0;
    if(isset($this->classRegistrations[$age]))
      $registrationCount = count($this->classRegistrations[$age]);

    return $registrationCount;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJsonObject($jsonObject){
    $classRegistrationData = new ClassRegistrationData($jsonObject->className);

    foreach($jsonObject->classRegistrations as $age => $registrations){
      $classRegistrationData->classRegistrations[$age] = array();
      foreach($registrations as $registrationIndex => $classRegistrationJsonObject){
        $classRegistrationData->classRegistrations[$age][$registrationIndex] = ClassRegistration::createFromJsonObject($classRegistrationJsonObject);
      }
    }

    $classRegistrationData->newRegistrationIndex = $jsonObject->newRegistrationIndex;
    $classRegistrationData->juvenileRegistrationCount = $jsonObject->juvenileRegistrationCount;

    return $classRegistrationData;
  }
}


class UserRegistrationData implements JsonSerializable {
  public $userName;
  public $userRegistrations;
  public $juvenileRegistrationCount;

  public function __construct($userName){
    $this->userName = $userName;
    $this->userRegistrations = array();
    $this->juvenileRegistrationCount = 0;
  }

  public function addRegistration($className, $age, $classRegistrationIndex, $juvenileMember){
    if(!isset($this->userRegistrations[$className]))
      $this->userRegistrations[$className] = array();

    if(!isset($this->userRegistrations[$className][$age]))
      $this->userRegistrations[$className][$age] = array();

    if($juvenileMember)
      $this->juvenileRegistrationCount++;

    array_push($this->userRegistrations[$className][$age], $classRegistrationIndex);
  }

  public function removeRegistration($className, $age, $juvenileMember){
    if($juvenileMember)
      $this->juvenileRegistrationCount--;

    $removedClassIndex = array_pop($this->userRegistrations[$className][$age]);
    if(count($this->userRegistrations[$className][$age]) == 0)
      unset($this->userRegistrations[$className][$age]);

    if(count($this->userRegistrations[$className]) == 0)
      unset($this->userRegistrations[$className]);

    return $removedClassIndex;
  }

  public function getEntryCount(){
    $entryCount = 0;
    foreach($this->userRegistrations as $className => $ageClassRegistrations){
      foreach($ageClassRegistrations as $age => $classRegistrations){
        $entryCount += $this->getUserClassRegistrationCount($className, $age);
      }
    }
    $entryCount += $this->juvenileRegistrationCount;

    return $entryCount;
  }

  public function getUserClassRegistrationCount($className, $age){
    $classRegistrationCount = 0;
    if(isset($this->userRegistrations[$className]) && isset($this->userRegistrations[$className][$age]))
      $classRegistrationCount = count($this->userRegistrations[$className][$age]);

    return $classRegistrationCount;
  }

  public function getUserRegistrationOverviewHtml($locationID){
    $eventClasses = EventClasses::create($locationID);
    $userRegistrationOverviewHtml = "<h2>Registered Classes for ".$this->userName."</h2>";
    $userRegistrationOverviewHtml .= "<ul>";
    foreach($this->userRegistrations as $className => $ageClassRegistrations){
      foreach($ageClassRegistrations as $age => $classRegistrations){
        $userRegistrationOverviewHtml .= "<li><span class='class-entered'>".$eventClasses->getClassIndex($className, $age)."</span> ".$className." ".$age.": <span class='number-entered'>".count($classRegistrations)."</span></li>";
      }
    }
    if($this->juvenileRegistrationCount > 0)
      $userRegistrationOverviewHtml .= "<li><span class='class-entered'>".$eventClasses->getClassIndex("juvenile", "AA")."</span> juvenile AA: <span class='number-entered'>".$this->juvenileRegistrationCount."</span></li>";

    $userRegistrationOverviewHtml .= "</ul>";

    return $userRegistrationOverviewHtml;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJsonObject($jsonObject){
    $userRegistrationData = new UserRegistrationData($jsonObject->userName);
    $userRegistrationData->juvenileRegistrationCount = $jsonObject->juvenileRegistrationCount;
    foreach($jsonObject->userRegistrations as $className => $ageRegistrations){
      $userRegistrationData->userRegistrations[$className] = array();
      foreach($ageRegistrations as $age => $registrationIndices){
        $userRegistrationData->userRegistrations[$className][$age] = array();
        foreach($registrationIndices as $registrationIndex){
          array_push($userRegistrationData->userRegistrations[$className][$age], $registrationIndex);
        }
      }
    }

    return $userRegistrationData;
  }
}


class ClassRegistration implements JsonSerializable {
  public $userName;
  public $juvenile;

  public function __construct($userName, $juvenile = false){
    $this->userName = $userName;
    $this->juvenile = $juvenile;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJsonObject($jsonObject){
    return new ClassRegistration($jsonObject->userName, $jsonObject->juvenile);
  }
}
