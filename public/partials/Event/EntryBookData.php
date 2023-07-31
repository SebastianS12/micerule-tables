<?php

class EntryBookData implements JsonSerializable {
  public $grandChallenge;
  public $sections;
  public $optionalSection;
  public $classes;
  public $entries;
  public $judgesComments;

  public function __construct(){
    //$this->prizeData = new PrizeData();
    $this->grandChallenge = new GrandChallengeData();
    $this->sections = array();
    $this->optionalSection = new SectionData("optional");
    $this->classes = array();
    $this->entries = array();
    $this->judgesComments = array();
  }

  public function addSectionData($sectionData){
    $this->sections[$sectionData->sectionName] = $sectionData;
  }

  public function getSectionData($sectionName){
    $sectionData = new SectionData("");
    if(isset($this->sections[$sectionName]))
      $sectionData = $this->sections[$sectionName];
      
    return $sectionData;
  }

  public function addClassData($classData, $sectionName){
    $this->classes[$classData->className] = $classData;
    $this->sections[$sectionName]->addClassName($classData->className);
  }

  public function addOptionalClassData($classData){
    $this->classes[$classData->className] = $classData;
    $this->optionalSection->addClassName($classData->className);
  }

  public function getClassData($className){
    $classData = new ClassData("");
    if(isset($this->classes[$className]))
      $classData = $this->classes[$className];

    return $classData;
  }

  public function addEntry($entry, $className){
    $this->entries[$entry->penNumber] = $entry;
    $this->classes[$className]->addPenNumber($entry->penNumber);

    if(EventUser::isJuniorMember($entry->userName)  && isset($this->classes['junior']))
      $this->classes['junior']->addPenNumber($entry->penNumber);
  }

  public function removeEntry($entry){
    unset($this->entries[$entry->penNumber]);
    unset($this->classes[$entry->className]->penNumbers[$entry->penNumber]);

    if(EventUser::isJuniorMember($entry->userName) && isset($this->classes['junior']))
      unset($this->classes['junior']->penNumber[$entry->penNumber]);
  }

  public function getPlacementEntry($placement, $prizeData){
    $placementEntryPenNumber = $prizeData->getPlacementEntryPenNumber($placement);
    $placementEntry = new Entry("", "", "", "", "", "");
    if($placementEntryPenNumber != NULL)
      $placementEntry = $this->entries[$placementEntryPenNumber];

    return $placementEntry;
  }

  public function getJudgeComment($judgeName){
    $judgeComment = "";
    if(isset($this->judgesComments[$judgeName]))
      $judgeComment = $this->judgesComments[$judgeName];
    return $judgeComment;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJson($json){
    $entryBookData = new EntryBookData();
    $entryBookData->grandChallenge = GrandChallengeData::createFromJson($json->grandChallenge);

    foreach($json->sections as $sectionName => $sectionDataJson){
      $entryBookData->sections[$sectionName] = SectionData::createFromJson($sectionDataJson);
    }

    $entryBookData->optionalSection = SectionData::createFromJson($json->optionalSection);

    foreach($json->classes as $className => $classDataJson){
      $entryBookData->classes[$className] = ClassData::createFromJson($classDataJson);
    }
    foreach($json->entries as $penNumber => $entryJson){
      $entryBookData->entries[$penNumber] = Entry::createFromJson($entryJson);
    }

    foreach($json->judgesComments as $judgeName => $comment){
      $entryBookData->judgesComments[$judgeName] = $comment;
    }

    return $entryBookData;
  }

  public static function create($eventID){
    $entryBookData = new EntryBookData();
    $entryBookDataJson = json_decode(get_post_meta($eventID, 'micerule_data_event_entry_book_test', true), false, 512, JSON_UNESCAPED_UNICODE);
    if($entryBookDataJson != NULL)
      $entryBookData = self::createFromJson($entryBookDataJson);

    return $entryBookData;
  }
}

//TODO: super class for challenge

class GrandChallengeData implements JsonSerializable {
  public $challengeIndices;
  public $placementEntries;
  public $judgesComments;

  public function __construct(){
    $this->challengeIndices = array();
    $this->placementEntries = array();
    $this->judgesComments = array();
  }

  public function setChallengeIndex($age, $challengeIndex){
    $this->challengeIndices[$age] = $challengeIndex;
  }

  public function getChallengeIndex($age){
    $challengeIndex = 0;
    if(isset($this->challengeIndices[$age]))
      $challengeIndex = $this->challengeIndices[$age];

    return $challengeIndex;
  }

  public function getPlacementData($age){
    if(!isset($this->placementEntries[$age])){
      $this->placementEntries[$age] = new PlacementData();
    }

    return $this->placementEntries[$age];
  }

  public function bestsChecked(){
    $bestsChecked = (count($this->placementEntries) > 0);
    foreach($this->placementEntries as $age => $placementEntry){
      $bestsChecked = $bestsChecked && $placementEntry->isPlacementChecked("1");
    }

    return $bestsChecked;
  }

  public function setBIS($age, $oppositeAge, $checkValue){
    if($this->bestsChecked()){
      $checkValue = ($checkValue == "true") ? true : false;
      $this->placementEntries[$age]->best = $checkValue;
      $this->placementEntries[$oppositeAge]->bestOA = $checkValue;
    }
  }

  public function challengeBISChecked($age){
    $challengeBISChecked = false;
    if($this->bestsChecked()){
      $challengeBISChecked = $challengeBISChecked || $this->placementEntries[$age]->challengeBISChecked();
    }

    return $challengeBISChecked;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJson($json){
    $grandChallengeData = new GrandChallengeData();
    foreach($json->challengeIndices as $age => $challengeIndex){
      $grandChallengeData->setChallengeIndex($age, $challengeIndex);
    }
    foreach($json->placementEntries as $age => $placementDataJson){
      $placementData = PlacementData::createFromJson($placementDataJson);
      $grandChallengeData->placementEntries[$age] = $placementData;
    }

    foreach($json->judgesComments as $age => $judgesComments){
      $grandChallengeData->judgesComments[$age] = $judgesComments;
    }

    return $grandChallengeData;
  }
}


class SectionData implements JsonSerializable{
  public $sectionName;
  public $challengeIndices;
  public $classNames;
  public $placementEntries;
  public $judgesComments;

  public function __construct($sectionName){
    $this->sectionName = $sectionName;
    $this->challengeIndices = array();
    $this->placementEntries = array();
    $this->classNames = array();
    $this->judgesComments = array();
  }

  public function addClassName($className){
    array_push($this->classNames, $className);
  }

  public function setChallengeIndex($age, $challengeIndex){
    $this->challengeIndices[$age] = $challengeIndex;
  }

  public function getChallengeIndex($age){
    $challengeIndex = 0;
    if(isset($this->challengeIndices[$age]))
      $challengeIndex = $this->challengeIndices[$age];

    return $challengeIndex;
  }

  public function bestsChecked(){
    $bestsChecked = (count($this->placementEntries) > 0);
    foreach($this->placementEntries as $age => $placementEntry){
      $bestsChecked = $bestsChecked && $placementEntry->isPlacementChecked("1");
    }

    return $bestsChecked;
  }

  public function setBIS($age, $oppositeAge, $checkValue){
    if($this->bestsChecked()){
      $checkValue = ($checkValue == "true") ? true : false;
      $this->placementEntries[$age]->best = $checkValue;
      if(isset($this->placementEntries[$oppositeAge]))
        $this->placementEntries[$oppositeAge]->bestOA = $checkValue;
    }
  }

  public function challengeBISChecked($age){
    $challengeBISChecked = false;
    if($this->bestsChecked()){
      $challengeBISChecked = $challengeBISChecked || $this->getPlacementData($age)->challengeBISChecked();
    }

    return $challengeBISChecked;
  }

  public function getPlacementData($age){
    if(!isset($this->placementEntries[$age])){
      $this->placementEntries[$age] = new PlacementData();
    }

    return $this->placementEntries[$age];
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJson($json){
    $sectionData = new SectionData($json->sectionName);
    foreach($json->challengeIndices as $age => $challengeIndex){
      $sectionData->setChallengeIndex($age, $challengeIndex);
    }
    foreach($json->classNames as $className){
      $sectionData->addClassName($className);
    }

    foreach($json->placementEntries as $age => $placementDataJson){
      $placementData = PlacementData::createFromJson($placementDataJson);
      $sectionData->placementEntries[$age] = $placementData;
    }

    foreach($json->judgesComments as $age => $judgesComments){
      $sectionData->judgesComments[$age] = $judgesComments;
    }

    return $sectionData;
  }
}


class ClassData implements JsonSerializable {
  public $className;
  public $classIndices;
  public $penNumbers;
  public $placementEntries;
  public $judgesComments;
  public $nextPenNumber;

  public function __construct($className){
    $this->className = $className;
    $this->classIndices = array();
    $this->penNumbers = array();
    $this->placementEntries = array();
    $this->judgesComments = array();
    $this->nextPenNumber = array();
  }

  public function addPenNumber($penNumber){
    $this->penNumbers[$penNumber] = $penNumber;
  }

  public function setClassIndex($age, $classIndex){
    $this->classIndices[$age] = $classIndex;
  }

  public function getClassIndex($age){
    $classIndex = 0;
    if(isset($this->classIndices[$age]))
      $classIndex = $this->classIndices[$age];

    return $classIndex;
  }

  public function setNextPenNumber($age, $nextPenNumber){
    $this->nextPenNumber[$age] = $nextPenNumber;
  }

  public function getNextPenNumber($age){
    $nextPenNumber = 0;
    if(isset($this->nextPenNumber[$age]))
      $nextPenNumber = $this->nextPenNumber[$age];

    return $nextPenNumber;
  }

  public function getPlacementData($age){
    if(!isset($this->placementEntries[$age])){
      $this->placementEntries[$age] = new PlacementData();
    }

    return $this->placementEntries[$age];
  }

  public function getJudgesComments($age){
    if(!isset($this->judgesComments[$age])){
      $this->judgesComments[$age] = "";
    }

    return $this->judgesComments[$age];
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJson($json){
    $classData = new ClassData($json->className);
    foreach($json->classIndices as $age => $classIndex){
      $classData->setClassIndex($age, $classIndex);
    }

    foreach($json->nextPenNumber as $age => $nextPenNumber){
      $classData->setNextPenNumber($age, $nextPenNumber);
    }

    foreach($json->penNumbers as $penNumber){
      $classData->addPenNumber($penNumber);
    }

    foreach($json->placementEntries as $age => $placementDataJson){
      $placementData = PlacementData::createFromJson($placementDataJson);
      $classData->placementEntries[$age] = $placementData;
    }

    foreach($json->judgesComments as $age => $judgesComments){
      $classData->judgesComments[$age] = $judgesComments;
    }

    return $classData;
  }
}


class Entry implements JsonSerializable {
  public $penNumber;
  public $userName;
  public $age;
  public $className;
  public $varietyName;
  public $sectionName;
  public $moved;
  public $absent;
  public $added;

  public function __construct($penNumber, $userName, $age, $className, $varietyName, $sectionName, $moved = false, $absent = false, $added = false){
    $this->penNumber = $penNumber;
    $this->userName = $userName;
    $this->age = $age;
    $this->className = $className;
    $this->varietyName = $varietyName;
    $this->sectionName = $sectionName;
    $this->moved = $moved;
    $this->absent = $absent;
    $this->added = $added;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJson($json){
    return new Entry($json->penNumber, $json->userName, $json->age, $json->className, $json->varietyName, $json->sectionName, $json->moved, $json->absent, $json->added);
  }
}


class PrizeEntryData implements JsonSerializable {
  public $printed;
  public $buck;
  public $doe;
  public $judgesComments;

  public function __construct($penNumber, $printed = false, $buck = false, $doe = false, $judgesComments = ""){
    $this->penNumber = $penNumber;
    $this->printed = $printed;
    $this->buck = $buck;
    $this->doe = $doe;
    $this->judgesComments = $judgesComments;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public static function createFromJson($json){
    return new PrizeEntryData($json->penNumber, $json->printed, $json->buck, $json->doe, $json->judgesComments);
  }
}

class PlacementData implements JsonSerializable {
  public $placements;
  public $best;
  public $bestOA;

  public function __construct($firstPlaceEntry=NULL, $secondPlaceEntry=NULL, $thirdPlaceEntry=NULL, $best = false, $bestOA = false){
    $this->placements = array();
    $this->placements['1'] = $firstPlaceEntry;
    $this->placements['2'] = $secondPlaceEntry;
    $this->placements['3'] = $thirdPlaceEntry;
    $this->best = $best;
    $this->bestOA = $bestOA;
  }

  public function getPlacementEntryPenNumber($placement){
    $penNumber = "";
    if(isset($this->placements[$placement]))
      $penNumber = $this->placements[$placement]->penNumber;

    return $penNumber;
  }

  public function editPlacement($placement, $entry, $checkValue){
    if($checkValue == "true"){
        $this->placements[$placement] = new PrizeEntryData($entry->penNumber);
    }else{
        $this->placements[$placement] = NULL;
    }
  }

  public function editPlacementPrinted($placement, $printed){
    $this->placements[$placement]->printed = $printed;
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }

  public function hasPlacement($entry, $placement){
    return (isset($this->placements[$placement]) && $this->placements[$placement]->penNumber == $entry->penNumber);
  }


  public function showPlacementCheck($entry, $placement){
    return ((!isset($this->placements[$placement]) && !$this->hasPlacement($entry, ($placement % 3 + 1)) && !$this->hasPlacement($entry, ($placement + 1) % 3 + 1)) || $this->hasPlacement($entry, $placement));
  }

  public function isPlacementChecked($placement){
    return (isset($this->placements[$placement]));
  }

  public function entryHasPlacement($entry){
    $firstPlacement = (isset($this->placements['1']) && $this->placements['1']->penNumber == $entry->penNumber);
    $secondPlacement = (isset($this->placements['2']) && $this->placements['2']->penNumber == $entry->penNumber);
    $thirdPlacement = (isset($this->placements['3']) && $this->placements['3']->penNumber == $entry->penNumber);

    return ($firstPlacement || $secondPlacement || $thirdPlacement);
  }

  public function challengeBISChecked(){
    return $this->isPlacementChecked("1") && ($this->best || $this->bestOA);
  }

  public static function createFromJson($json){
    $arrayKeys = array();
    foreach($json->placements as $key => $placement){
      array_push($arrayKeys, $placement);
    }
    //var_export($json->placements->{"1"}, true)
    $firstPlaceEntry = ($json->placements->{"1"} != NULL) ? PrizeEntryData::createFromJson($json->placements->{"1"}) : NULL;
    $secondPlaceEntry = ($json->placements->{"2"} != NULL) ? PrizeEntryData::createFromJson($json->placements->{"2"}) : NULL;
    $thirdPlaceEntry = ($json->placements->{"3"} != NULL) ? PrizeEntryData::createFromJson($json->placements->{"3"}) : NULL;
    return new PlacementData($firstPlaceEntry, $secondPlaceEntry, $thirdPlaceEntry, $json->best, $json->bestOA);
  }
}
