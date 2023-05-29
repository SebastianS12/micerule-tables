<?php

class UserEntryData{
  public $userEntries;

  public function __construct($eventID){
    $this->entryBookData = EntryBookData::create($eventID);
    $this->userEntries = array();
    $this->populateUserEntries();
  }

  private function populateUserEntries(){
    foreach($this->entryBookData->entries as $entry){
      $this->addEntryData($entry);
    }
  }

  private function addEntryData($entry){
    if(!isset($this->userEntries[$entry->userName])){
      $this->userEntries[$entry->userName] = array();
    }

    $classData = $this->entryBookData->classes[$entry->className];
    $newEntryData = new EntryData($entry, $classData);
    array_push($this->userEntries[$entry->userName], $newEntryData);
  }

  public function allAbsent($userName){
    $allAbsent = true;
    foreach($this->userEntries[$userName] as $entryData){
      $allAbsent = $allAbsent && $entryData->absent;
    }
    return $allAbsent;
  }

  public function getUserEntryFee($userName, $registrationFee){
    $entryFee = 0;
    foreach($this->userEntries[$userName] as $userEntryData){
        $entryFee += $registrationFee;
    }

    return $entryFee;
  }
}

class EntryData{
  public $className;
  public $classIndex;
  public $penNumber;
  public $age;
  public $absent;

  public function __construct($entry, $classData){
    $this->className = $classData->className;
    $this->classIndex = $classData->getClassIndex($entry->age);
    $this->age = $entry->age;
    $this->absent = $entry->absent;
    $this->penNumber = $entry->penNumber;
  }
}


class UserPrizeData{

  public function __construct($eventID){
    $this->entryBookData = EntryBookData::create($eventID);
    $this->users = array();
    $this->getUserClassPrizes();
    $this->getUserSectionPrizes();
    $this->getUserGrandChallengePrizes();
  }

  private function getUserClassPrizes(){
    foreach($this->entryBookData->classes as $classData){
      $this->setSectionPrizes($classData);
    }
  }

  private function getUserSectionPrizes(){
    foreach($this->entryBookData->sections as $sectionData){
      $this->setSectionPrizes($sectionData);
    }
  }

  private function getUserGrandChallengePrizes(){
    $this->setSectionPrizes($this->entryBookData->grandChallenge);
  }

  private function setSectionPrizes($sectionData){
    foreach($sectionData->placementEntries as $age => $placementData){
      foreach($placementData->placements as $placement => $prizeEntryData){
      if($prizeEntryData != NULL){
        $placementEntry = $this->entryBookData->getPlacementEntry($placement, $placementData);
        $this->addUserPlacement($placement, $placementEntry);
      }
    }
    }
  }

  private function addUserPlacement($placement, $placementEntry){
    if(!isset($this->users[$placementEntry->userName]))
      $this->users[$placementEntry->userName] = new UserPrizes();

    if($placement == "1"){
      $this->users[$placementEntry->userName]->addFirstPrize($placementEntry);
    }else if($placement == "2"){
      $this->users[$placementEntry->userName]->addSecondPrize($placementEntry);
    }else{
      $this->users[$placementEntry->userName]->addThirdPrize($placementEntry);
    }
  }

  public function getUserPrizeMoney($userName, $firstPrize, $secondPrize, $thirdPrize){
    $userPrizeMoney = 0;
    if(isset($this->users[$userName]))
      $userPrizeMoney = $this->users[$userName]->getPrizeMoney($firstPrize, $secondPrize, $thirdPrize);

    return $userPrizeMoney;
  }
}


class UserPrizes {
  public function __construct(){
    $this->firstPrizes = array();
    $this->secondPrizes = array();
    $this->thirdPrizes = array();
  }

  public function addFirstPrize($placementEntry){
    array_push($this->firstPrizes, $placementEntry);
  }

  public function addSecondPrize($placementEntry){
    array_push($this->secondPrizes, $placementEntry);
  }

  public function addThirdPrize($placementEntry){
    array_push($this->thirdPrizes, $placementEntry);
  }

  public function getPrizeMoney($firstPrize, $secondPrize, $thirdPrize){
    return count($this->firstPrizes) * $firstPrize + count($this->secondPrizes) * $secondPrize + count($this->thirdPrizes) * $thirdPrize;
  }
}


class JudgeData{
  public $judges;
  public $judgeClasses;
  //public $judgeSectionChallenges;
  public $judgeGrandChallenge;
  public $optionalClasses;

  public function __construct($eventID){
    $this->eventID = $eventID;
    $this->entryBookData = EntryBookData::create($eventID);
    $this->eventJudges = new EventJudges($eventID);
    $this->judges = $this->eventJudges->judgeNames;
    $this->judgeClasses = array();
    $this->judgeGrandChallenge = array();
    $this->optionalClasses = array();
    $this->setJudgeSheetData();
  }

  private function setJudgeSheetData(){
    $judgeSections = $this->eventJudges->judgeSections;
    $this->setGrandChallengeSheetData();
    $this->setOptionalClasses();

    foreach($this->judges as $index => $judge){
      $this->judgeClasses[$judge] = array();
      $this->judgeSectionChallenges[$judge] = array();
      foreach($judgeSections[$index] as $sectionName){
        $this->addJudgeSectionSheetData($sectionName, $judge);
        foreach($this->entryBookData->getSectionData(strtolower($sectionName))->classNames as $className){
          $this->addJudgeClassSheetData($className, $judge);
        }
      }
      ksort($this->judgeClasses[$judge]);
    }
  }

  private function setOptionalClasses(){
    $classes= $this->entryBookData->classes;
    if(isset($classes['juvenile']))
      $this->setOptionalClassSheetData($classes['juvenile']);

    if(isset($classes['unstandardised']))
      $this->setOptionalClassSheetData($classes['unstandardised']);
  }

  private function setOptionalClassSheetData($classData){
    $judgeSheet = new JudgeSheetData($classData->className, $classData->getClassIndex("AA"), "AA");

    foreach($classData->penNumbers as $penNumber){
      $entry = $this->entryBookData->entries[$penNumber];
      $judgeSheet->addEntry($entry);
    }

    $this->optionalClasses[$judgeSheet->classIndex] = $judgeSheet;
  }

  private function setGrandChallengeSheetData(){
    $grandChallengeData = $this->entryBookData->grandChallenge;
    $adJudgeSheet = new JudgeSheetData("Grand Challenge", $grandChallengeData->getChallengeIndex("Ad"), "Ad", true);
    $u8JudgeSheet = new JudgeSheetData("Grand Challenge", $grandChallengeData->getChallengeIndex("U8"), "U8", true);

    //TODO: function for section + grand challenge?
    foreach($grandChallengeData->placementEntries as $age => $placementData){
      foreach($placementData->placements as $placement => $prizeEntryData){
        if($prizeEntryData != NULL){
          $placementEntry = $this->entryBookData->getPlacementEntry($placement, $grandChallengeData->getPlacementData($age));
          if($placementEntry->age == "Ad")
          $adJudgeSheet->addEntry($placementEntry);
          else
          $u8JudgeSheet->addEntry($placementEntry);
        }
      }
    }

    $this->judgeGrandChallenge[$adJudgeSheet->classIndex] = $adJudgeSheet;
    $this->judgeGrandChallenge[$u8JudgeSheet->classIndex] = $u8JudgeSheet;
  }

  private function addJudgeSectionSheetData($sectionName, $judgeName){
    if(isset($this->entryBookData->sections[strtolower($sectionName)])){
      $sectionData = $this->entryBookData->sections[strtolower($sectionName)];
      $challengeName = EventProperties::getChallengeName($sectionData->sectionName);
      $adJudgeSheet = new JudgeSheetData($challengeName, $sectionData->getChallengeIndex("Ad"), "Ad", true);
      $u8JudgeSheet = new JudgeSheetData($challengeName, $sectionData->getChallengeIndex("U8"), "U8", true);

      foreach($sectionData->placementEntries as $age => $placementData){
        foreach($placementData->placements as $placement => $prizeEntryData){
          if($prizeEntryData != NULL){
            $placementEntry = $this->entryBookData->getPlacementEntry($placement, $sectionData->getPlacementData($age));
            if($placementEntry->age == "Ad")
              $adJudgeSheet->addEntry($placementEntry);
            else
              $u8JudgeSheet->addEntry($placementEntry);
          }
        }
      }

      $this->judgeClasses[$judgeName][$adJudgeSheet->classIndex] = $adJudgeSheet;
      $this->judgeClasses[$judgeName][$u8JudgeSheet->classIndex] = $u8JudgeSheet;
    }
}

private function addJudgeClassSheetData($className, $judgeName){
  $classData = $this->entryBookData->classes[$className];
  $adJudgeSheet = new JudgeSheetData($classData->className, $classData->getClassIndex("Ad"), "Ad");
  $u8JudgeSheet = new JudgeSheetData($classData->className, $classData->getClassIndex("U8"), "U8");

  foreach($classData->penNumbers as $penNumber){
    $entry = $this->entryBookData->entries[$penNumber];
    if($entry->age == "Ad")
    $adJudgeSheet->addEntry($entry);
    else
    $u8JudgeSheet->addEntry($entry);
  }

  $this->judgeClasses[$judgeName][$adJudgeSheet->classIndex] = $adJudgeSheet;
  $this->judgeClasses[$judgeName][$u8JudgeSheet->classIndex] = $u8JudgeSheet;
}

}

class JudgeSheetData{
  public $className;
  public $classIndex;
  public $age;
  public $challengeSheet;
  public $entries;

  public function __construct($className, $classIndex, $age, $challengeSheet = false){
    $this->className = $className;
    $this->classIndex = $classIndex;
    $this->age = $age;
    $this->challengeSheet = $challengeSheet;
    $this->entries = array();
  }

  public function addEntry($entry){
    array_push($this->entries, $entry);
  }

  public function getAbsentees(){
    $absentees = array();
    foreach($this->entries as $entry){
      if($entry->absent)
      array_push($absentees, $entry->penNumber);
    }
    return $absentees;
  }
}

class PrizeCards {
  public $printedCards;
  public $unprintedCards;

  public function __construct($eventID){
    $this->printedCards = array();
    $this->unprintedCards = array();
    $this->eventJudges = new EventJudges($eventID);
    $this->entryBookData = EntryBookData::create($eventID);
    $this->eventRegistrationData = EventRegistrationData::create($eventID);
    $this->eventClasses = EventClasses::create(EventProperties::getEventLocationID($eventID));
    $this->getPrizeCards($eventID);
  }

  public function getPrizeCards($eventID){
    $this->getGrandChallengePrizeCards($this->entryBookData->grandChallenge);
    $this->getSectionChallengePrizeCards($this->entryBookData->sections);
    $this->getClassPrizeCards($this->entryBookData->classes);
    //grand challenge prize cards
    //section prize cards
    //class prize cards
    //optional class prize cards
  }

  private function getGrandChallengePrizeCards($grandChallengeData){
    foreach($grandChallengeData->placementEntries as $age => $placementData){
      $challengeIndex = $this->eventClasses->getClassIndex("GRAND CHALLENGE", $age);//$grandChallengeData->getChallengeIndex($age);
      $entryCount = $this->eventRegistrationData->getGrandChallengeRegistrationCount($age);
      foreach($placementData->placements as $placement => $prizeEntryData){
        if($prizeEntryData != NULL){
          $placementEntry = $this->entryBookData->getPlacementEntry($placement, $grandChallengeData->getPlacementData($age));
          $displayPlacement = ($placement == '1' && $placementData->best) ? "BIS" : $placement;
          $displayPlacement = ($placement == '1' && $placementData->bestOA) ? "BOA" : $displayPlacement;
          $prizeCardData = new PrizeCardData("Grand Challenge", "Grand Challenge", $challengeIndex, $placementEntry, $placement, $displayPlacement, $this->getGrandChallengeJudgeString(), date("d/m/Y"), $entryCount);
          $this->addPrizeCard($prizeCardData, $prizeEntryData->printed);
        }
      }
    }
  }

  private function getGrandChallengeJudgeString(){
    $judgeString = "";
    foreach($this->eventJudges->judgeNames as $judgeName){
      $judgeString .= $judgeName." ";
    }
    return $judgeString;
  }

  private function getSectionChallengePrizeCards($sections){
    foreach($sections as $sectionName => $sectionData){
      $challengeName = EventProperties::getChallengeName($sectionData->sectionName);
      foreach($sectionData->placementEntries as $age => $placementData){
        $challengeIndex = $this->eventClasses->getClassIndex($challengeName, $age);//$sectionData->getChallengeIndex($age);
        $entryCount = $this->getSectionEntryCount($sectionName, $age);
        foreach($placementData->placements as $placement => $prizeEntryData){
          if($prizeEntryData != NULL){
            $placementEntry = $this->entryBookData->getPlacementEntry($placement, $sectionData->getPlacementData($age));
            $displayPlacement = ($placement == '1' && $placementData->best) ? "BISec" : $placement;
            $displayPlacement = ($placement == '1' && $placementData->bestOA) ? "BOSec" : $displayPlacement;
            $prizeCardData = new PrizeCardData("Section Challenge", $challengeName, $challengeIndex, $placementEntry, $placement, $displayPlacement, $this->eventJudges->getSectionJudge($sectionName), date("d/m/Y"), $entryCount);
            $this->addPrizeCard($prizeCardData, $prizeEntryData->printed);
          }
        }
      }
    }
  }

  private function getSectionEntryCount($sectionName, $age){
    $sectionEntryCount = 0;
    foreach($this->eventClasses->getSectionClasses($sectionName) as $className){
      $sectionEntryCount += $this->eventRegistrationData->getClassRegistrationData($className)->getRegistrationCount($age);
    }

    return $sectionEntryCount;
  }

  private function getClassPrizeCards($classes){
    foreach($classes as $className => $classData){
      foreach($classData->placementEntries as $age => $placementData){
        $classIndex = $this->eventClasses->getClassIndex($className, $age);
        $entryCount = $this->eventRegistrationData->getClassRegistrationData($className)->getRegistrationCount($age);
        foreach($placementData->placements as $placement => $prizeEntryData){
          if($prizeEntryData != NULL){
            $placementEntry = $this->entryBookData->getPlacementEntry($placement, $classData->getPlacementData($age));
            $prizeCardData = new PrizeCardData("Class", $className, $classIndex, $placementEntry, $placement, $placement, $this->eventJudges->getSectionJudge($placementEntry->sectionName), date("d/m/Y"), $entryCount);
            $this->addPrizeCard($prizeCardData, $prizeEntryData->printed);
          }
        }
      }
    }
  }

  private function addPrizeCard($prizeCardData, $printed){
    if($printed){
      if(!isset($this->printedCards[$prizeCardData->placementEntry->userName]))
        $this->printedCards[$prizeCardData->placementEntry->userName] = array();

      array_push($this->printedCards[$prizeCardData->placementEntry->userName], $prizeCardData);
    }else{
      array_push($this->unprintedCards, $prizeCardData);
    }
  }
}

class PrizeCardData{
  public $prize;
  public $challengeName;
  public $classIndex;
  public $placementEntry;
  public $placement;
  public $displayPlacement;
  public $judge;
  public $date;
  public $entryCount;

  public function __construct($prize, $challengeName, $classIndex, $placementEntry, $placement, $displayPlacement, $judge, $date, $entryCount){
    $this->prize = $prize;
    $this->challengeName = $challengeName;
    $this->classIndex = $classIndex;
    $this->placementEntry = $placementEntry;
    $this->placement = $placement;
    $this->displayPlacement = $displayPlacement;
    $this->judge = $judge;
    $this->date = $date;
    $this->entryCount = $entryCount;
  }
}

class JudgesReportData {
  //public $judges;
  //public $judgesReportData;
  public $classReports;
  public $challengeReports;
  public function __construct($eventID){
    $this->entryBookData = EntryBookData::create($eventID);
    $this->eventJudges = new EventJudges($eventID);
    //$this->judges = $this->eventJudges->judgeNames;
    $this->classReports = array();
    $this->challengeReports = array();
    $this->setJudgesReportData();
  }

  private function setJudgesReportData(){
    $judgeSections = $this->eventJudges->judgeSections;

    foreach($this->eventJudges->judgeNames as $index => $judge){
      foreach($judgeSections[$index] as $sectionName){
        $this->challengeReports[$sectionName] = array();
        $this->addSectionJudgeReportData($sectionName, $judge);
        foreach($this->entryBookData->getSectionData(strtolower($sectionName))->classNames as $className){
          $this->classReports[$className] = array();
          $this->addClassJudgeReportData($sectionName, $className, $judge);
        }
      }
    }
  }

  private function addClassJudgeReportData($sectionName, $className, $judge){
    $classData = $this->entryBookData->classes[$className];
    if(isset($classData)){
      $adJudgeReport = new JudgesReportDataItem($className, $classData->getClassIndex("Ad"), "Ad", $classData->getPlacementData("Ad")->placements, $this->getClassEntryCount($classData, "Ad"), $classData->getJudgesComments("Ad"));
      $u8JudgeReport = new JudgesReportDataItem($className, $classData->getClassIndex("U8"), "U8", $classData->getPlacementData("U8")->placements, $this->getClassEntryCount($classData, "U8"), $classData->getJudgesComments("U8"));

      $this->classReports[$className]["Ad"] = $adJudgeReport;
      $this->classReports[$className]["U8"] = $u8JudgeReport;
    }
  }

  private function addSectionJudgeReportData($sectionName, $judge){
    if(isset($this->entryBookData->sections[strtolower($sectionName)])){
      $sectionData = $this->entryBookData->sections[strtolower($sectionName)];
      $challengeName = EventProperties::getChallengeName($sectionData->sectionName);
      $adJudgeReport = new JudgesReportDataItem($challengeName, $sectionData->getChallengeIndex("Ad"), "Ad", $sectionData->getPlacementData("Ad")->placements, $this->getSectionEntryCount($sectionData, "Ad"), "");
      $u8JudgeReport = new JudgesReportDataItem($challengeName, $sectionData->getChallengeIndex("U8"), "U8", $sectionData->getPlacementData("U8")->placements, $this->getSectionEntryCount($sectionData, "U8"), "");

      $this->challengeReports[$sectionName]["Ad"] = $adJudgeReport;
      $this->challengeReports[$sectionName]["U8"] = $u8JudgeReport;
    }
  }

  private function getClassEntryCount($classData, $age){
    $classEntries = 0;
    foreach($classData->penNumbers as $penNumber){
      $entry = $this->entryBookData->entries[$penNumber];
      if($entry->age == $age)
        $classEntries++;
    }

    return $classEntries;
  }

  private function getSectionEntryCount($sectionData, $age){
    $sectionEntryCount = 0;
    foreach($sectionData->classNames as $className){
      $sectionEntryCount += $this->getClassEntryCount($this->entryBookData->classes[$className], $age);
    }

    return $sectionEntryCount;
  }
}


class JudgesReportDataItem {
  public $className;
  public $classIndex;
  public $age;
  public $placementEntries;
  public $entryCount;

  public function __construct($className, $classIndex, $age, $placementEntries, $entryCount, $classComments){
    $this->className = $className;
    $this->classIndex = $classIndex;
    $this->age = $age;
    $this->placementEntries = $placementEntries;
    //TODO: Siehe Google Doc, ClassData Attribute aufteilen und dann getClassEntryCounts überarbeiten, pro Alter
    $this->entryCount = (isset($entryCount)) ? $entryCount : 0;
    $this->classComments = $classComments;
  }
}


class EventJudges {
  public $judgeNames;
  public $judgeSections;

  public function __construct($eventID){
    $this->setJudgeNames($eventID);
    $judgeSectionsPostMeta = get_post_meta($eventID, 'micerule_data_settings', true);
    $this->judgeSections = isset($judgeSectionsPostMeta['classes']) ? $judgeSectionsPostMeta['classes'] : array();
  }

  private function setJudgeNames($eventID){
    $this->judgeNames = array();
    $judgesPostMeta = get_post_meta($eventID, 'micerule_data_settings', true);
    $judges = (isset($judgesPostMeta['judges'])) ? $judgesPostMeta['judges'] : array();

    foreach($judges as $index => $judge){
      if($judge != "")
      array_push($this->judgeNames, $judge);
    }
  }

  public function getSectionJudge($sectionName){
    $judge = "";

    foreach($this->judgeSections as $index => $sections){
      if(in_array($sectionName, array_map('strtolower', $sections))){
        $judge = $this->judgeNames[$index];
      }
    }

    return $judge;
  }
}
