<?php

class ShowReportPost {
  private $eventPostID;

  public function __construct($eventPostID){
    $this->eventPostID = $eventPostID;
  }

  public function createPost(){
    $postTitle = EventProperties::getEventMetaData($this->eventPostID)['event_name'];
    $post = array(
      'post_title' => $postTitle,
      'post_content' => html_entity_decode($this->getHtml()),
      'post_status' => 'draft',
      'post_type' => array(1),
    );

    return $post;
  }

  public function getHtml(){
    $judgesModel = new EventJudgesHelper();
    $html = "";
    //TODO: get Judge Data Model that contains judgeNo, name, sections
    foreach($judgesModel->getEventJudgeNames($this->eventPostID) as $judgeNo => $judgeName){
      $html .= $this->getJudgeReportHtml($this->eventPostID, $judgeName, $judgeNo + 1, $judgesModel);
    }
    return $html;
  }

  // public function getHtml(){
  //   $html = "";
  //   foreach($this->eventJudges->judgeNames as $index => $judgeName){
  //     $html .= $this->getJudgeReportHtml($judgeName, $index);
  //   }
  //   return $html;
  // }

  private function getJudgeReportHtml($eventPostID, $judgeName, $judgeNo, $judgesModel){
    $html = "<H2 class='judge-header'>".$this->getJudgeInfoString($eventPostID, $judgeName, $judgesModel)."</h2>";

    $generalComment = GeneralComment::loadFromDB($eventPostID, $judgeNo);
    $html .= "<h4 class = 'p1'>General Comments</h4>";
    $html .= "<div>".$generalComment->comment."</div>";

    foreach($judgesModel->getJudgeSections($eventPostID, $judgeName) as $sectionName){
      $html .= $this->getJudgeSectionReportHtml($this->eventPostID, $sectionName, $judgeName, $judgeNo);
    }

    return $html;
  }

  // private function getJudgeReportHtml($judgeName, $index){
  //   $html = "<H2 class='judge-header'>".$this->getJudgeInfoString($judgeName, $index)."</h2>";

  //   $html .= "<h4 class = 'p1'>General Comments</h4>";
  //   $judgeGeneralComments = (isset($this->entryBookData->judgesComments[$judgeName])) ? $this->entryBookData->judgesComments[$judgeName] : "";
  //   $html .= "<div>".$judgeGeneralComments."</div>";

  //   foreach($this->eventJudges->judgeSections[$index] as $sectionName){
  //     $html .= $this->getJudgeSectionReportHtml($sectionName);
  //   }

  //   return $html;
  // }

  private function getJudgeInfoString($eventPostID, $judgeName, $judgesModel){
    $judgeString = "Judge: ".$judgeName." ";
    foreach($judgesModel->getJudgeSections($eventPostID, $judgeName) as $judgeSection){
      $judgeString .= $judgeSection.", ";
    }
    $judgeString = rtrim($judgeString, ", ");


    return $judgeString;
  }

  private function getJudgeSectionReportHtml($eventPostID, $sectionName, $judgeName, $judgeNo){
    $classReportData = array();
    $challengeReportData = array();
    $judgesReportModel = new JudgesReportModel();
    foreach ($judgesReportModel->getJudgeClassesData($eventPostID, $judgeName) as $judgeClassData) {
      if ($judgeClassData['prize'] == "Class" && $judgeClassData['section'] == $sectionName)
          array_push($classReportData, $judgeClassData);
      if ($judgeClassData['prize'] == "Section Challenge" && $judgeClassData['section'] == $sectionName)
          array_push($challengeReportData, $judgeClassData);
    }

    $html = "<h1>".$sectionName."</h1>";
    $html .= "<div class = 'section-placement-reports'>";
    $html .= "<div class = 'class-reports' style = 'width: 65.667%'>";
    $html .= $this->getClassReportsHtml($classReportData, $judgeNo);
    $html .= "</div>";

    $html .= "<div class = 'section-challenge-reports' style = 'width: 31.3333%'>";
    $html .= $this->getSectionChallengeReportsHtml($challengeReportData, $judgeNo);
    $html .= "</div>";

    $html .= "</div>";

    return $html;
  }

  // private function getJudgeSectionReportHtml($sectionName){
  //   $html = "<h1>".$sectionName."</h1>";
  //   $html .= "<div class = 'section-placement-reports'>";
  //   $html .= "<div class = 'class-reports' style = 'width: 65.667%'>";
  //   $html .= $this->getClassReportsHtml($sectionName);
  //   $html .= "</div>";

  //   $html .= "<div class = 'section-challenge-reports' style = 'width: 31.3333%'>";
  //   $html .= $this->getSectionChallengeReportsHtml($sectionName);
  //   $html .= "</div>";

  //   $html .= "</div>";

  //   return $html;
  // }

  private function getClassReportsHtml($classReportData, $judgeNo){
    $html = "";
    $registrationTablesModel = new RegistrationTablesModel();
    foreach($classReportData as $classReport){
      $placementsModel = new ClassPlacements($this->eventPostID, $classReport['age'], $classReport['class_name']);
      $classRegistrationCount = $registrationTablesModel->getClassRegistrationCount($this->eventPostID, $classReport['class_name'], $classReport['age']);
      $html .= $this->getReportDataItemHtml($classReport, false, $judgeNo, $classRegistrationCount, $placementsModel);
    }
    // foreach($this->entryBookData->sections[strtolower($sectionName)]->classNames as $className){
    //   foreach($this->judgesReportData->classReports[$className] as $age => $reportDataItem){
    //     $html .= $this->getReportDataItemHtml($reportDataItem, false);
    //   }
    // }
    return $html;
  }

  private function getSectionChallengeReportsHtml($sectionReportData, $judgeNo){
    $html = "";
    $registrationTablesModel = new RegistrationTablesModel();
    foreach($sectionReportData as $sectionReport){
      $placementsModel = new SectionPlacements($this->eventPostID, $sectionReport['age'], $sectionReport['section']);
      $sectionRegistrationCount = $registrationTablesModel->getSectionRegistrationCount($this->eventPostID, $sectionReport['section'], $sectionReport['age']);
      $html .= $this->getReportDataItemHtml($sectionReport, true, $judgeNo, $sectionRegistrationCount, $placementsModel);
    }
    return $html;
  }

  private function getReportDataItemHtml($reportDataItem, $isSectionChallengeReport, $judgeNo, $registrationCount, $placementsModel){
    $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');

    $classCommentModel = ClassComment::loadFromDB($this->eventPostID, $reportDataItem['class_name'], $reportDataItem['age'], $judgeNo);
    $html = "<h4>".$reportDataItem['class_name']." ".$reportDataItem['age']." - ".$registrationCount."</h4>";
    $html .= "<div>".$classCommentModel->comment."</div>";
    foreach ($placementsModel->placements as $placement => $placementEntryID) {
      if (isset($placementEntryID)){
        $entry = ShowEntry::createWithEntryID($placementEntryID);
        $placementReport = PlacementReport::loadFromDB($this->eventPostID, $entry->className, $entry->age, $judgeNo, $placement);
        $displayedGender = "";
        $displayedGender = ($placementReport->gender != "" && $placementReport->gender == "Buck") ? "B" : "D";
        $displayedVariety = ($entry->className != $entry->varietyName || $isSectionChallengeReport) ? $entry->varietyName : "";
        $html .= "<div>".$displayedPlacements[$placement]." ".$this->formatNameString($entry->userName)." ".$displayedVariety." ".$displayedGender." ".$placementReport->comment."</div>";
      }
    }

    return $html;
  }

  // private function getReportDataItemHtml($reportDataItem, $isSectionChallengeReport, $judgeNo){
  //   $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');

  //   $classCommentModel = ClassComment::loadFromDB($this->eventPostID, $reportDataItem['class_name'], $reportDataItem['age'], $judgeNo);
  //   $html = "<h4>".$reportDataItem->className." ".$reportDataItem->age." - ".$reportDataItem->entryCount."</h4>";
  //   $html .= "<div>".$this->entryBookData->getClassData($reportDataItem->className)->getJudgesComments($reportDataItem->age)."</div>";
  //   foreach($reportDataItem->placementEntries as $placement => $prizeEntry){
  //     if($prizeEntry != null){
  //       $entry = $this->entryBookData->entries[$prizeEntry->penNumber];
  //       $displayedGender = ($prizeEntry->buck) ? "B" : "D";
  //       $displayedGender = (!$prizeEntry->buck && !$prizeEntry->doe) ? "" : $displayedGender;
  //       if(isset($entry)){
  //         $displayedVariety = ($entry->className != $entry->varietyName || $isSectionChallengeReport) ? $entry->varietyName : "";
  //         $html .= "<div>".$displayedPlacements[$placement]." ".$this->formatNameString($entry->userName)." ".$displayedVariety." ".$displayedGender." ".$prizeEntry->judgesComments."</div>";
  //       }
  //     }
  //   }

  //   return $html;
  // }

  private function formatNameString($name){
    $formattedName = $name;
    if(count(explode(" ", $name, 2)) == 2){
      $firstName = explode(" ", $name)[0];
      $surName = explode(" ", $name)[1];

      $formattedName = $firstName[0]." ".$surName;
    }
    return $formattedName;
  }
}
