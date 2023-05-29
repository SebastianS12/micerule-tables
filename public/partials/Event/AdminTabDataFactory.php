<?php

class AdminTabDataFactory {
  private $eventID;
  private $entryBookData;
  private $data;

  public function __construct($eventID){
    $this->eventID = $eventID;
    //$this->entryBookData = get_post_meta($this->eventID, 'micerule_data_event_entry_book', true);
    $this->entryBookData = EntryBookData::create($eventID);
    $this->data = array();
  }

  public function getUserEntryData(){
    $userEntryData = new UserEntryData($this->eventID);
    return $userEntryData;
  }


  public function getJudgeData(){
    $judgeData = new JudgeData($this->eventID);
    return $judgeData;
  }


  public function getPrizeCardData(){
    /*
    $judges = get_post_meta($this->eventID, 'micerule_data_settings', true)['judges'];
    $judgeSections = get_post_meta($this->eventID, 'micerule_data_settings', true)['classes'];
    $date = date("d/m/Y");

    $this->data['printed'] = array();
    $this->data['unprinted'] = array();
    $this->addAgeBestPrizeCards($judges, $judgeSections, $date);
    foreach($this->entryBookData['sections'] as $sectionName => $sectionData){
      $judge = $this->getSectionJudge($judges, $judgeSections, $sectionName);
      $this->addSectionBestPrizeCards($sectionData, $sectionName, $judge, $date);
      foreach($sectionData['classes'] as $className => $classEntryBookData){
        $this->addClassBestPrizeCards($sectionName, $classEntryBookData, $className, $judge, $date);
      }
    }

    return $this->data;
    */
    $prizeCards = new PrizeCards($this->eventID);
    return $prizeCards;
  }

  private function addAgeBestPrizeCards($judges, $judgeSections, $date){
    foreach($this->entryBookData['ageBest'] as $age => $agePlacementData){
      foreach($agePlacementData as $placement => $placementData){
        if($placement != "BIS" && $placement != "BOA"){
          $sectionName = $placementData['sectionName'];
          $className = $placementData['className'];
          $penNumber = $placementData['penNumber'];
          $challengeName = "GRAND CHALLENGE";
          $printStatus = (isset($placementData['printed'])) ? "printed" : "unprinted";
          $challengeIndex = $this->entryBookData['sections']['grand challenge optional']['challengeIndex'];//$this->entryBookData['sections'][$sectionName]['classes'][$className]['classIndex'];
          $userName = $this->entryBookData['sections'][$sectionName]['classes'][$className][$age][$penNumber]['userName'];
          $varietyName = (isset($this->entryBookData['sections'][$sectionName]['classes'][$className][$age][$penNumber]['varietyName'])) ? $this->entryBookData['sections'][$sectionName]['classes'][$className][$age][$penNumber]['varietyName'] : $className;
          $judge = $this->getSectionJudge($judges, $judgeSections, $sectionName);
          $this->addPrizeCard("ageBest", "", $userName, $challengeName, $className, $varietyName, $challengeIndex, $age, $penNumber, $placement, $judge, $date, $printStatus);
        }
      }
    }
  }

  private function addSectionBestPrizeCards($sectionData, $sectionName, $judge, $date){
    foreach($sectionData['sectionBest'] as $age => $agePlacementData){
      foreach($agePlacementData as $placement => $placementData){
        if($placement != "BIS" && $placement != "BOA"){
          $className = $placementData['className'];
          $challengeName = substr(strtoupper($sectionName), 0, -1)." CHALLENGE";
          $penNumber = $placementData['penNumber'];
          $challengeIndex = $sectionData['challengeIndex'];//$sectionData['classes'][$className]['classIndex'];
          $userName = $sectionData['classes'][$className][$age][$penNumber]['userName'];
          $varietyName = (isset($sectionData['classes'][$className][$age][$penNumber]['varietyName'])) ? $sectionData['classes'][$className][$age][$penNumber]['varietyName'] : $className;
          $printStatus = (isset($placementData['printed'])) ? "printed" : "unprinted";
          $this->addPrizeCard("sectionBest", $sectionName, $userName, $challengeName, $className, $varietyName, $challengeIndex, $age, $penNumber, $placement, $judge, $date, $printStatus);
        }/*else{
          $this->addSectionChallengeCards($sectionData, $age, $placement, $sectionName, $judge, $date);
        }*/
      }
    }
  }

  private function addSectionChallengeCards($sectionData, $age, $placement, $sectionName, $judge, $date){
    $challengeName = substr(strtoupper($sectionName), 0, -1)." CHALLENGE";
    $className = $sectionData['sectionBest'][$age]['1']['className'];
    $penNumber = $sectionData['sectionBest'][$age]['1']['penNumber'];
    $challengeIndex = $sectionData['challengeIndex'];
    $userName = $sectionData['classes'][$className][$age][$penNumber]['userName'];
    $varietyName = (isset($sectionData['classes'][$className][$age][$penNumber]['varietyName'])) ? $sectionData['classes'][$className][$age][$penNumber]['varietyName'] : $className;
    $printStatus = (isset($sectionData['sectionBest'][$age][$placement]['printed'])) ? "printed" : "unprinted";
    $this->addPrizeCard("sectionBest", $sectionName, $userName, $challengeName, $className, $varietyName, $challengeIndex, $age, $penNumber, $placement, $judge, $date, $printStatus);
  }

  private function addClassBestPrizeCards($sectionName, $classData, $className, $judge, $date){
    $classIndex = $classData['classIndex'];

    foreach($classData['placements'] as $age => $agePlacementData){
      foreach($agePlacementData as $placement => $placementData){
        $penNumber = $placementData['penNumber'];
        $printStatus = (isset($placementData['printed'])) ? "printed" : "unprinted";
        $userName = $classData[$age][$penNumber]['userName'];
        $varietyName = (isset($classData[$age][$penNumber]['varietyName'])) ? $classData[$age][$penNumber]['varietyName'] : $className;
        $this->addPrizeCard("placements", $sectionName, $userName, "", $className, $varietyName, $classIndex, $age, $penNumber, $placement, $judge, $date, $printStatus);
      }
    }
  }

  private function addPrizeCard($prize, $sectionName, $userName, $challengeName, $className, $varietyName, $classIndex, $age, $penNumber, $placement, $judge, $date, $printStatus){
    $newPrizeCard = new PrizeCardData($prize, $sectionName, $userName, $challengeName, $className, $varietyName, $classIndex, $age, $penNumber, $placement, $judge, $date);

    if(!isset($this->data[$printStatus][$userName]))
      $this->data[$printStatus][$userName] = array();
    if(!isset($this->data[$printStatus][$userName][$prize]))
      $this->data[$printStatus][$userName][$prize] = array();
    array_push($this->data[$printStatus][$userName][$prize], $newPrizeCard);
  }

  private function getSectionJudge($judges, $judgeSections, $sectionName){
    $judge = "";

    foreach($judgeSections as $index => $sections){
      if(in_array($sectionName, array_map('strtolower', $sections))){
        $judge = $judges[$index];
      }
    }

    return $judge;
  }

}
