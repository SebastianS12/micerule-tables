<?php

class EntryBook implements IAdminTab {
  private $wpdb;
  private $entryBookData;
  private $eventID;
  private $eventLocationID;

  public function __construct($eventID) {
    global $wpdb;
    $this->wpdb = $wpdb;

    $this->eventID = $eventID;
    $this->eventLocationID = EventProperties::getEventLocationID($eventID);
    $this->entryBookData = EntryBookData::create($eventID);
  }

  function getHtml(){
    $sectionNames = EventProperties::SECTIONNAMES;
    $challengeNames = EventProperties::CHALLENGENAMES;
    $eventDeadline = EventProperties::getEventDeadline($this->eventID);
    $standardClassesObject = new StandardClasses();

    $html = "<div class = 'entryBook content' style = 'display : none'>";
    $html .= "<div>";
    $html .= (time() > strtotime($eventDeadline)) ? "<a class = 'button addEntry'>Add Entry</a>" : "";
    foreach($this->entryBookData->sections as $sectionName => $sectionData){
      //Array of all standard section classes
      $standardClasses = $standardClassesObject->sectionStandardClasses[$sectionName];


      $html .= "<div class = '".$sectionName."-div'>";
      foreach($sectionData->classNames as $className){
        $classData = $this->entryBookData->classes[$className];
        $html .= "<div class = 'class-pairing'>";
        $adsTableHtml = "<table><tbody>";
        $u8TableHtml = "<table><tbody>";
        $adsTableHtml .= $this->getBreedNameHeader($classData->getClassIndex("Ad"), $classData->className, "Ad");
        $u8TableHtml .= $this->getBreedNameHeader(($classData->getClassIndex("U8")), $classData->className, "U8");

        $adRowCount = 0;
        $u8RowCount = 0;
        foreach($classData->penNumbers as $penNumber){
          $entry = $this->entryBookData->entries[$penNumber];
          if($entry->age == "Ad"){
            $adsTableHtml .= $this->getEntryRow($entry, $sectionData, $classData, $standardClasses, $eventDeadline);
            $adRowCount++;
          }else{
            $u8TableHtml .= $this->getEntryRow($entry, $sectionData, $classData, $standardClasses, $eventDeadline);
            $u8RowCount++;
          }
        }
        $adsTableHtml .= ($adRowCount < $u8RowCount) ? $this->addEmptyRows($u8RowCount - $adRowCount, "Ad") : "";
        $u8TableHtml .= ($u8RowCount < $adRowCount) ? $this->addEmptyRows($adRowCount - $u8RowCount, "U8") : "";

        $adsTableHtml .= "</tbody></table>";
        $u8TableHtml .= "</tbody></table>";
        $html .= $adsTableHtml;
        $html .= $u8TableHtml;
        $html .= "</div>";
      }

      $html .= "<div class = 'class-pairing'>";
      $prize = "Section Challenge";
      $challengeName = EventProperties::getChallengeName($sectionData->sectionName);
      $html .= $this->getChallengeRow($prize, $sectionData, $challengeName, $sectionData->sectionName, $sectionData->getChallengeIndex("Ad"), "Ad");
      $html .= $this->getChallengeRow($prize, $sectionData, $challengeName, $sectionData->sectionName, $sectionData->getChallengeIndex("U8"), "U8");
      $html .= "</div>";

      $html .= "</div>";
    }

    $html .= (isset($this->entryBookData->grandChallenge)) ? $this->getGrandChallengeRows() : "";
    $html .= (isset($this->entryBookData->optionalSection)) ? $this->getEntryBookOptionalClassHtml($eventDeadline) : "";
    $html .= "<div id = 'editEntryModal' style = 'hidden'></div>";
    $html .= "</div>";
    $html .= "</div>";

    return $html;
  }


  private function getEntryRow($entry, $sectionData, $classData, $standardClasses, $eventDeadline){
    $classPlacementData = $classData->getPlacementData($entry->age);
    $sectionPlacementData = $sectionData->getPlacementData($entry->age);
    $grandChallengePlacementData = $this->entryBookData->grandChallenge->getPlacementData($entry->age);

    $classMoved = ($entry->moved) ? "moved" : "";
    $classAbsent = ($entry->absent) ? "absent" : "";
    $classAdded = ($entry->added) ? "added" : "";

    $html = "<tr class='entry-pen-number'>";
    $html .= "<td class='pen-numbers ".$classMoved." ".$classAbsent." ".$classAdded."'><span>".$entry->penNumber."</span></td>";
    $html .= $this->getAbsentCell($classPlacementData, $entry);
    $html .= "<td class='user-names ".$classMoved."'><span>".$entry->userName."</span></td>";
    $html .= $this->getEditCell($eventDeadline, $entry, $classPlacementData, $standardClasses);

    //TODO: function name ClassPrize
    $html .= $this->getPlacementEditCell($entry, $classPlacementData, $sectionPlacementData);
    $html .= $this->getSectionBestEditCell($entry, $classPlacementData, $sectionPlacementData, $grandChallengePlacementData);
    $html .= $this->getAgeBestEditCell($entry, $grandChallengePlacementData, $sectionPlacementData);

    $html .= "</tr>";

    return $html;
  }

  private function addEmptyRows($rowCount, $age){
  $html = "";

    for($i = 0; $i < $rowCount; $i++){
      $html .= "<tr class='entry-pen-number'>";
      $html .= "<td class='pen-numbers'>";
      $html .= "<td class='absent-td'></td>";
      $html .= "<td class='user-names'></td>";
      $html .= "<td class='editEntry-td'></td>";
      $html .= "<td class='placement-".$age."'></td>";
      $html .= "<td class='sectionBest-".$age."'></td>";
      $html .= "<td class='ageBest-".$age."'></td>";
      $html .= "</tr>";
    }

    return $html;
  }


  private function getAbsentCell($classPlacementData, $entry){
    $absentChecked = ($entry->absent) ? "checked" : "";
    $classAbsent = ($entry->absent) ? "absent" : "";

    $html = "<td class = 'absent-td'>";
    $html .= (!$classPlacementData->entryHasPlacement($entry)) ? "<input type = 'checkbox' class = 'absentCheck' id = '".$entry->penNumber."&-&absent&-&check' ".$absentChecked."></input><label for='".$entry->penNumber."&-&absent&-&check'><img src='/wp-content/plugins/micerule-tables/admin/svg/absent-not.svg'></label>" : "";
    $html .= "</td>";

    return $html;
  }

  private function getBreedNameHeader($classIndex, $className, $age){
    $html = "<tr class='breed-name-header'>";
    $html .= "<td class='table-pos'>".$classIndex."</td>";
    $html .= "<td class = 'absent-td'>Abs</td>";
    $html .= "<td class='breed-class'>".$className." ".$age."</td>";
    $html .= "<td class='age'></td>";
    $html .= "<td class = 'placement-".$age."'><img src='/wp-content/plugins/micerule-tables/admin/svg/class-ranking.svg'></td>";
    $html .= "<td class = 'sectionBest-".$age."'><img src='/wp-content/plugins/micerule-tables/admin/svg/section-first.svg'></td>";
    $html .= "<td class = 'ageBest-".$age."'><img src='/wp-content/plugins/micerule-tables/admin/svg/challenge-first.svg'></td>";
    $html .= "</tr>";

    return $html;
  }


  private function getChallengeRow($prize, $prizeSectionData, $challengeName, $sectionName, $challengeIndex, $age){
    $BISChecked = ($prizeSectionData->challengeBISChecked($age)) ? "checked" : "";
    $BISDisabled = ($prizeSectionData->getPlacementData($age)->bestOA) ? "disabled" : "";
    $html = "<table><tbody>";
    $html .= "<tr class='challenge-row'><td class='table-pos'>".$challengeIndex."</td><td class='breed-class'>".$challengeName." ".$age."</td><td class='age'></td><td class='placement-".$age."'></td><td class='sectionBest-".$age."'><div class='placement-checks'>";
    $html .= ($prizeSectionData->bestsChecked()) ? "<input type = 'checkbox' class = 'BISCheck' id = '".$prize."&-&".$age."&-&".$sectionName."&-&BIS&-&check' ".$BISChecked." ".$BISDisabled."></input><label for = '".$prize."&-&".$age."&-&".$sectionName."&-&BIS&-&check'><span class='is-best'>BEST</span><span class='is-boa'>BOA</span></label>" : "";
    $html .= "</div></td><td class='ageBest-".$age."'></td></tr>";
    $html .= $this->getChallengePlacementOverviewHtml($prizeSectionData->getPlacementData($age));
    $html .= "</tbody></table>";

    return $html;
  }

  private function getChallengePlacementOverviewHtml($prizeData){
    $html = "";
    for($placement = 1; $placement < 4; $placement++){
      $placementEntry = $this->entryBookData->getPlacementEntry($placement, $prizeData);
      $html .= "<tr>";
      $html .= "<td>".$placementEntry->penNumber."</td>";
      $html .= "<td>".$placementEntry->userName."</td>";
      $html .= "<td>".$placementEntry->varietyName."</td>";
      $html .= "<td>".$placement."</td>";
      $html .= "<td></td>";
      $html .= "<td></td>";
    }

    return $html;
  }

  private function getEditCell($eventDeadline, $entry, $classPlacementData, $standardClasses){
    $html  = "<td class = 'editEntry-td'>";
    $html .= (!$classPlacementData->entryHasPlacement($entry) && time() > strtotime($eventDeadline)) ? "<div class='button-wrapper'><button class = 'moveEntry' id = '".$entry->penNumber."&-&move'><img src='/wp-content/plugins/micerule-tables/admin/svg/move.svg'></button>
              <button class = 'deleteEntry' id = '".$entry->penNumber."&-&delete'><img src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></div>" : "";
    $varietyName = $entry->varietyName;
    $classSelectOptions = ClassSelectOptions::getClassSelectOptionsHtml($entry->sectionName, $this->eventLocationID, $varietyName);
    $html .= (!in_array($entry->className, $standardClasses) && $classPlacementData->entryHasPlacement($entry)) ? "<select class = 'classSelect-entryBook' id = '".$entry->penNumber."&-&varietySelect' autocomplete='off'><option value = ''>Select a Variety</option>".$classSelectOptions."</select>" : "";
    $html .= "</td>";

    return $html;
  }


  private function getPlacementEditCell($entry, $classPlacementData, $sectionPlacementData){
    $placementCheckDisabled = (isset($sectionPlacementData) && $sectionPlacementData->entryHasPlacement($entry)) ? "checked" : "";
    $firstPlaceChecked = ($classPlacementData->hasPlacement($entry, "1")) ? "checked" : "";
    $secondPlaceChecked = ($classPlacementData->hasPlacement($entry, "2")) ? "checked" : "";
    $thirdPlaceChecked = ($classPlacementData->hasPlacement($entry, "3")) ? "checked" : "";
    $prize = "Class";

    $html = "<td class = 'placement-".$entry->age."'>";
    $html .= "<div class='placement-checks'>";
    if(!$entry->absent){
      $html .= ($classPlacementData->showPlacementCheck($entry, "1")) ? "<input type = 'checkbox' name = 'firstPlaceCheck' class = 'placementCheck' id = '".$prize."&-&1&-&".$entry->penNumber."&-&check' ".$firstPlaceChecked." ".$placementCheckDisabled."><label for = '".$prize."&-&1&-&".$entry->penNumber."&-&check'>1</label>" : "";
      $html .= ($classPlacementData->showPlacementCheck($entry, "2")) ? "<input type = 'checkbox' name = 'secondPlaceCheck' class = 'placementCheck' id = '".$prize."&-&2&-&".$entry->penNumber."&-&check' ".$secondPlaceChecked." ".$placementCheckDisabled."><label for = '".$prize."&-&2&-&".$entry->penNumber."&-&check'>2</label>" : "";
      $html .= ($classPlacementData->showPlacementCheck($entry, "3")) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '".$prize."&-&3&-&".$entry->penNumber."&-&check' ".$thirdPlaceChecked." ".$placementCheckDisabled."><label for = '".$prize."&-&3&-&".$entry->penNumber."&-&check'>3</label>" : "";
    }
    $html .= "</div>";
    $html .= "</td>";

    return $html;
  }


  private function getSectionBestEditCell($entry, $classPlacementData, $sectionPlacementData, $grandChallengePlacementData){
    $sectionBestDisabled = ($grandChallengePlacementData->entryHasPlacement($entry)) ? "disabled" : "";
    $firstPlaceChecked = ($sectionPlacementData->hasPlacement($entry, "1")) ? "checked" : "";
    $secondPlaceChecked = ($sectionPlacementData->hasPlacement($entry, "2")) ? "checked" : "";
    $thirdPlaceChecked = ($sectionPlacementData->hasPlacement($entry, "3")) ? "checked" : "";
    $prize = "Section Challenge";
    $html = "<td class = 'sectionBest-".$entry->age."'>";
    $html .= "<div class='sectionBest-checks'>";

    if($classPlacementData->hasPlacement($entry, "1")){
      $html .= ($sectionPlacementData->showPlacementCheck($entry, "1")) ? "<input type = 'checkbox' name = 'firstPlaceCheck' class = 'placementCheck' id = '".$prize."&-&1&-&".$entry->penNumber."&-&check' ".$firstPlaceChecked." ".$sectionBestDisabled."><label for = '".$prize."&-&1&-&".$entry->penNumber."&-&check'>1</label>" : "";
      $html .= ($sectionPlacementData->showPlacementCheck($entry, "2")) ? "<input type = 'checkbox' name = 'secondPlaceCheck' class = 'placementCheck' id = '".$prize."&-&2&-&".$entry->penNumber."&-&check' ".$secondPlaceChecked." ".$sectionBestDisabled."><label for = '".$prize."&-&2&-&".$entry->penNumber."&-&check'>2</label>" : "";
      $html .= ($sectionPlacementData->showPlacementCheck($entry, "3")) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '".$prize."&-&3&-&".$entry->penNumber."&-&check' ".$thirdPlaceChecked." ".$sectionBestDisabled."><label for = '".$prize."&-&3&-&".$entry->penNumber."&-&check'>3</label>" : "";
    }

    if($classPlacementData->hasPlacement($entry, "2")){
      if(($sectionPlacementData->isPlacementChecked("1") && $this->entryBookData->getPlacementEntry("1", $sectionPlacementData)->className == $entry->className) || ($sectionPlacementData->isPlacementChecked("2") && $this->entryBookData->getPlacementEntry("2", $sectionPlacementData)->className == $entry->className)){
        $html .= ($sectionPlacementData->showPlacementCheck($entry, "2")) ? "<input type = 'checkbox' name = 'secondPlaceCheck' class = 'placementCheck' id = '".$prize."&-&2&-&".$entry->penNumber."&-&check' ".$secondPlaceChecked." ".$sectionBestDisabled."><label for = '".$prize."&-&2&-&".$entry->penNumber."&-&check'>2</label>" : "";
        $html .=  ($sectionPlacementData->showPlacementCheck($entry, "3")) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '".$prize."&-&3&-&".$entry->penNumber."&-&check' ".$thirdPlaceChecked." ".$sectionBestDisabled."><label for = '".$prize."&-&3&-&".$entry->penNumber."&-&check'>3</label>" : "";
      }
    }

    if($classPlacementData->hasPlacement($entry, "3")){
      if(($sectionPlacementData->isPlacementChecked("1") && $this->entryBookData->getPlacementEntry("1", $sectionPlacementData)->className == $entry->className) && ($sectionPlacementData->isPlacementChecked("2") && $this->entryBookData->getPlacementEntry("2", $sectionPlacementData)->className == $entry->className)){
        $html .= ($sectionPlacementData->showPlacementCheck($entry, "3")) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '".$prize."&-&3&-&".$entry->penNumber."&-&check' ".$thirdPlaceChecked." ".$sectionBestDisabled."><label for = '".$prize."&-&3&-&".$entry->penNumber."&-&check'>3</label>" : "";
      }
    }

    $html .= "</div>";
    $html .= "</td>";

    return $html;
  }


  private function getAgeBestEditCell($entry, $grandChallengePlacementData, $sectionPlacementData){
    $firstPlaceChecked = ($grandChallengePlacementData->hasPlacement($entry, "1")) ? "checked" : "";
    $secondPlaceChecked = ($grandChallengePlacementData->hasPlacement($entry, "2")) ? "checked" : "";
    $thirdPlaceChecked = ($grandChallengePlacementData->hasPlacement($entry, "3")) ? "checked" : "";

    $prize = "Grand Challenge";
    $html = "<td class = 'ageBest-".$entry->age."'>";
    $html .= "<div class='ageBest-checks'>";
    if($sectionPlacementData->hasPlacement($entry, "1")){
      $html .= ($grandChallengePlacementData->showPlacementCheck($entry, "1")) ? "<input type = 'checkbox' name = 'firstPlaceCheck' class = 'placementCheck' id = '".$prize."&-&1&-&".$entry->penNumber."&-&check' ".$firstPlaceChecked."><label for = '".$prize."&-&1&-&".$entry->penNumber."&-&check'>1</label>" : "";
      $html .= ($grandChallengePlacementData->showPlacementCheck($entry, "2")) ? "<input type = 'checkbox' name = 'secondPlaceCheck' class = 'placementCheck' id = '".$prize."&-&2&-&".$entry->penNumber."&-&check' ".$secondPlaceChecked."><label for = '".$prize."&-&2&-&".$entry->penNumber."&-&check'>2</label>" : "";
      $html .= ($grandChallengePlacementData->showPlacementCheck($entry, "3")) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '".$prize."&-&3&-&".$entry->penNumber."&-&check' ".$thirdPlaceChecked."><label for = '".$prize."&-&3&-&".$entry->penNumber."&-&check'>3</label>" : "";
    }

    if($sectionPlacementData->hasPlacement($entry, "2")){
      if(($grandChallengePlacementData->isPlacementChecked("1") && $this->entryBookData->getPlacementEntry("1", $grandChallengePlacementData)->sectionName == $entry->sectionName) || ($grandChallengePlacementData->isPlacementChecked("2") && $this->entryBookData->getPlacementEntry("2", $grandChallengePlacementData)->sectionName == $entry->sectionName)){
        $html .= ($grandChallengePlacementData->showPlacementCheck($entry, "2")) ? "<input type = 'checkbox' name = 'secondPlaceCheck' class = 'placementCheck' id = '".$prize."&-&2&-&".$entry->penNumber."&-&check' ".$secondPlaceChecked."><label for = '".$prize."&-&2&-&".$entry->penNumber."&-&check'>2</label>" : "";
        $html .=  ($grandChallengePlacementData->showPlacementCheck($entry, "3")) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '".$prize."&-&3&-&".$entry->penNumber."&-&check' ".$thirdPlaceChecked."><label for = '".$prize."&-&3&-&".$entry->penNumber."&-&check'>3</label>" : "";
      }
    }

    if($sectionPlacementData->hasPlacement($entry, "3")){
      if(($grandChallengePlacementData->isPlacementChecked("1") && $this->entryBookData->getPlacementEntry("1", $grandChallengePlacementData)->sectionName == $entry->sectionName) && ($grandChallengePlacementData->isPlacementChecked("2") && $this->entryBookData->getPlacementEntry("2", $grandChallengePlacementData)->sectionName == $entry->sectionName)){
        $html .= ($grandChallengePlacementData->showPlacementCheck($entry, "3")) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '".$prize."&-&3&-&".$entry->penNumber."&-&check' ".$thirdPlaceChecked."><label for = '".$prize."&-&3&-&".$entry->penNumber."&-&check'>3</label>" : "";
      }
    }
    $html .= "</div>";
    $html .= "</td>";

    return $html;
  }

  private function getEntryBookOptionalClassHtml($eventDeadline){
    $html = "";

    $sectionName = 'optional';
    $optionalSectionData = $this->entryBookData->optionalSection;
    foreach($optionalSectionData->classNames as $className){
      $classData = $this->entryBookData->classes[$className];
      $classPlacementData = $classData->getPlacementData("AA");

      $html .= "<table class='optional'><tbody>";
      $html .= "<tr class='breed-name-header'>";
      $html .= "<td class='table-pos'>".$classData->getClassIndex("AA")."</td>";
      $html .= "<td class = 'absent-td'>Abs</td>";
      $html .= "<td class='breed-class'>".ucfirst($className)." AA</td>";
      $html .= "<td class='age'></td>";
      $html .= "<td class = 'placement-ads'><img src='/wp-content/plugins/micerule-tables/admin/svg/class-ranking.svg'></td>";
      $html .= "</tr>";

      foreach($classData->penNumbers as $penNumber){
        $entry = $this->entryBookData->entries[$penNumber];
        $classMoved = ($entry->moved) ? "moved" : "";
        $classAdded = ($entry->added) ? "added" : "";

        $html .= "<tr class='entry-pen-number'>";
        $html .= "<td class='pen-numbers ".$classMoved." ".$classAdded."'><span>".$entry->penNumber."</span></td>";
        $html .= $this->getAbsentCell($classPlacementData, $entry);
        $html .= "<td class='user-names ".$classMoved."'>".$entry->userName."</td>";
        $html .= "<td class = 'editEntry-td'>";
        $html .= (!$classPlacementData->entryHasPlacement($entry) && time() > strtotime($eventDeadline)) ? "<div class='button-wrapper'><button class = 'moveEntry' id = '".$entry->penNumber."&-&move'><img src='/wp-content/plugins/micerule-tables/admin/svg/move.svg'></button>
                  <button class = 'deleteEntry' id = '".$entry->penNumber."&-&delete'><img src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></div>" : "";
        $html .= ($className == "unstandardised" && $classPlacementData->entryHasPlacement($entry)) ? "<input type = 'text' class = 'unstandardised-input' id = '".$entry->penNumber."&-&varietySelect' val = '".$entry->varietyName."' placeholder = '".$entry->varietyName."'></input>" : "";
        $html .= "</td>";
        $html .= $this->getPlacementEditCell($entry, $classPlacementData, NULL);
        $html .= "</tr>";
      }
      $html .= "</table></tbody>";
    }

    return $html;
  }


  private function getGrandChallengeRows(){
    $html = "<div class = 'grand-challenge-div'>";
    $html .= "<div class = 'class-pairing'>";

    $prize = "Grand Challenge";
    $challengeName = "GRAND CHALLENGE";
    $grandChallengeData = $this->entryBookData->grandChallenge;
    $html .= $this->getChallengeRow($prize, $grandChallengeData, $challengeName, $challengeName, $grandChallengeData->getChallengeIndex("Ad"), "Ad");
    $html .= $this->getChallengeRow($prize, $grandChallengeData, $challengeName, $challengeName, $grandChallengeData->getChallengeIndex("U8"), "U8");

    $html .= "</div>";
    $html .= "</div>";

    return $html;
  }
}
