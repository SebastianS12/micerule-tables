<?php

class ShowReportPost {
  private $eventPostID;

  public function __construct($eventPostID){
    $this->eventPostID = $eventPostID;
  }

  public function createPost(array $data){
    $postTitle = $data['eventMetaData']['event_name'];
    $post = array(
      'post_title' => $postTitle,
      'post_content' => html_entity_decode($this->getHtml($data)),
      'post_status' => 'draft',
      'post_type' => array(1),
    );

    return $post;
  }

  public function getHtml(array $data){
    $html = "";
    foreach ($data['judge_data'] as $judgeName => $judgeCommentData) {
      $html .= $this->getJudgeReportHtml($judgeCommentData, $data['placement_reports']);
    }

    $html .= "<h1>Grand Challenge</h1><div class = 'section-placement-reports' style= 'flex-direction: column'>";
    $html .= $this->getSectionChallengeReportsHtml($data['grand_challenge'], $data['placement_reports']['section']);
    $html .= "</div>";
    $html .= "<h1>Junior</h1><div class = 'section-placement-reports' style= 'flex-direction: column'>";
    $html .= $this->getClassReportsHtml($data['junior'], $data['placement_reports']['junior']);
    $html .= "</div>";

    return $html;
  }

  private function getJudgeReportHtml(array $judgeCommentData, array $placementReports){
    $html = "<H2 class='judge-header'>".$this->getJudgeInfoString($judgeCommentData['general']['judge_name'], $judgeCommentData['sections'])."</h2>";

    $html .= "<h4 class = 'p1'>General Comments</h4>";
    $html .= "<div>".$judgeCommentData['general']['comment']."</div>";

    foreach($judgeCommentData['class'] as $sectionName => $judgeSectionData){
      $html .= $this->getJudgeSectionReportHtml($judgeSectionData, $placementReports, $sectionName);
    }

    return $html;
  }

  private function getJudgeInfoString($judgeName, $judgeSections){
    $judgeString = "Judge: ".$judgeName." ";
    foreach($judgeSections as $judgeSection){
      $judgeString .= ucfirst($judgeSection).", ";
    }
    $judgeString = rtrim($judgeString, ", ");


    return $judgeString;
  }

  private function getJudgeSectionReportHtml(array $judgeSectionData, array $placementReports, string $sectionName){
    $classReportData = array();
    $challengeReportData = array();
    foreach ($judgeSectionData as $judgeClassData) {
      if ($judgeClassData['prize'] == "Class")
          array_push($classReportData, $judgeClassData);
      if ($judgeClassData['prize'] == "Section Challenge")
          array_push($challengeReportData, $judgeClassData);
    }

    $html = "<h1>".$sectionName."</h1>";
    $html .= "<div class = 'section-placement-reports'>";
    $html .= "<div class = 'class-reports' style = 'width: 65.667%'>";
    $html .= $this->getClassReportsHtml($classReportData, $placementReports['class']);
    $html .= "</div>";

    $html .= "<div class = 'section-challenge-reports' style = 'width: 31.3333%'>";
    $html .= $this->getSectionChallengeReportsHtml($challengeReportData, $placementReports['section']);
    $html .= "</div>";

    $html .= "</div>";

    return $html;
  }

  private function getClassReportsHtml($classReportData, $placementReports){
    $html = "";
    foreach($classReportData as $classReport){
      $html .= $this->getReportDataItemHtml($classReport, $placementReports[$classReport['class_index']], false);
    }

    return $html;
  }

  private function getSectionChallengeReportsHtml(array $sectionReportData, array $placementReports){
    $html = "";
    foreach($sectionReportData as $sectionReport){
      $html .= $this->getReportDataItemHtml($sectionReport, $placementReports[$sectionReport['class_index']], true);
    }
    return $html;
  }

  private function getReportDataItemHtml($reportData, $placementReports, $isSectionChallengeReport){
    $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');

    $html = "<h4>".$reportData['class_name']." ".$reportData['age']." - ".$reportData['entry_count']."</h4>";
    $html .= "<div class = 'class-comments'>".$reportData['comment']."</div>";
    foreach ($placementReports as $placementReport) {
        $displayedGender = "";
        $displayedGender = ($placementReport['gender'] != "" && $placementReport['gender'] == "Buck") ? "B" : "D";
        $displayedVariety = ($placementReport['class_name'] != $placementReport['variety_name'] || $isSectionChallengeReport) ? $placementReport['variety_name'] : "";
        $html .= "<div>".$displayedPlacements[$placementReport['placement']]." ".$this->formatNameString($placementReport['user_name'])." ".$displayedVariety." ".$displayedGender." ".$placementReport['comment']."</div>";
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
