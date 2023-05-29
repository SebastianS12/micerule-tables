<?php

class ShowReportPost {
  public function __construct($eventID){
    $this->eventID = $eventID;
    $this->entryBookData = EntryBookData::create($eventID);
    $this->eventJudges = new EventJudges($eventID);
    $this->judgesReportData = new JudgesReportData($eventID);
  }

  public function createPost(){
    $postTitle = EventProperties::getEventMetaData($this->eventID)['event_name'];
    $post = array(
      'post_title' => $postTitle,
      'post_content' => html_entity_decode($this->getHtml()),
      'post_status' => 'draft',
      'post_type' => array(1),
    );

    return $post;
  }

  public function getHtml(){
    $html = "";
    foreach($this->eventJudges->judgeNames as $index => $judgeName){
      $html .= $this->getJudgeReportHtml($judgeName, $index);
    }
    return $html;
  }

  private function getJudgeReportHtml($judgeName, $index){
    $html = "<H2 class='judge-header'>".$this->getJudgeInfoString($judgeName, $index)."</h2>";

    $html .= "<h4 class = 'p1'>General Comments</h4>";
    $judgeGeneralComments = (isset($this->entryBookData->judgesComments[$judgeName])) ? $this->entryBookData->judgesComments[$judgeName] : "";
    $html .= "<div>".$judgeGeneralComments."</div>";

    foreach($this->eventJudges->judgeSections[$index] as $sectionName){
      $html .= $this->getJudgeSectionReportHtml($sectionName);
    }

    return $html;
  }

  private function getJudgeInfoString($judgeName, $index){
    $judgeString = "Judge: ".$judgeName." ";
    foreach($this->eventJudges->judgeSections[$index] as $judgeSection){
      $judgeString .= $judgeSection.", ";
    }
    $judgeString = rtrim($judgeString, ", ");


    return $judgeString;
  }

  private function getJudgeSectionReportHtml($sectionName){
    $html = "<h1>".$sectionName."</h1>";
    $html .= "<div class = 'section-placement-reports'>";
    $html .= "<div class = 'class-reports' style = 'width: 65.667%'>";
    $html .= $this->getClassReportsHtml($sectionName);
    $html .= "</div>";

    $html .= "<div class = 'section-challenge-reports' style = 'width: 31.3333%'>";
    $html .= $this->getSectionChallengeReportsHtml($sectionName);
    $html .= "</div>";

    $html .= "</div>";

    return $html;
  }

  private function getClassReportsHtml($sectionName){
    $html = "";
    foreach($this->entryBookData->sections[strtolower($sectionName)]->classNames as $className){
      foreach($this->judgesReportData->classReports[$className] as $age => $reportDataItem){
        $html .= $this->getReportDataItemHtml($reportDataItem, false);
      }
    }
    return $html;
  }

  private function getSectionChallengeReportsHtml($sectionName){
    $html = "";
    foreach($this->judgesReportData->challengeReports[$sectionName] as $age => $reportDataItem){
      $html .= $this->getReportDataItemHtml($reportDataItem, true);
    }
    return $html;
  }

  private function getReportDataItemHtml($reportDataItem, $isSectionChallengeReport){
    $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');

    $html = "<h4>".$reportDataItem->className." ".$reportDataItem->age." - ".$reportDataItem->entryCount."</h4>";
    $html .= "<div>".$this->entryBookData->getClassData($reportDataItem->className)->getJudgesComments($reportDataItem->age)."</div>";
    foreach($reportDataItem->placementEntries as $placement => $prizeEntry){
      if($prizeEntry != null){
        $entry = $this->entryBookData->entries[$prizeEntry->penNumber];
        $displayedGender = ($prizeEntry->buck) ? "B" : "D";
        $displayedGender = (!$prizeEntry->buck && !$prizeEntry->doe) ? "" : $displayedGender;
        if(isset($entry)){
          $displayedVariety = ($entry->className != $entry->varietyName || $isSectionChallengeReport) ? $entry->varietyName : "";
          $html .= "<div>".$displayedPlacements[$placement]." ".$this->formatNameString($entry->userName)." ".$displayedVariety." ".$displayedGender." ".$prizeEntry->judgesComments."</div>";
        }
      }
    }

    return $html;
  }

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
