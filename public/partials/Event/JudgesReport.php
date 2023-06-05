<?php

class JudgesReport implements IAdminTab {

  public function __construct($eventID){
    $this->eventID = $eventID;
    $this->entryBookData = EntryBookData::create($eventID);
    $this->locationID = EventProperties::getEventLocationID($eventID);
    $this->standardClasses = new StandardClasses();
    $this->judgesReportData = new JudgesReportData($eventID);
  }

  function getHtml(){
    $judgesReportData = new JudgesReportData($this->eventID);
    $eventJudges = new EventJudges($this->eventID);
    $user = wp_get_current_user();
    $userName = $user->display_name;

    $html = "<div class = 'judgesReport content' style = 'display: none'>";

    foreach($eventJudges->judgeNames as $index => $judgeName){
      if($userName == $judgeName || current_user_can('administrator')){
        $html .= "<table>";
        $html .= $this->getJudgeReportHeaderHtml($judgeName);
        foreach($eventJudges->judgeSections[$index] as $sectionName){
          $html .= $this->getClassReportsHtml($sectionName);
          $html .= $this->getChallengeReportsHtml($sectionName);
        }
        $html .= "</table>";
      }
    }

    $html .= "</div>";
    return $html;
  }

  private function getJudgeReportHeaderHtml($judgeName){
    $eventMetaData = EventProperties::getEventMetaData($this->eventID);
    $html = "   <thead class='header-wrapper'>
                  <tr class='header-row'>
                    <th>
                      <ul class='show-data-header'>
                        <li>Show: ".$eventMetaData['event_name']."</li>
                        <li>Date: ".date("d F Y", strtotime($eventMetaData['event_start_date']))."</li>
                        <li>Judge: <span class = 'jr-judge-name'>".$judgeName."</span></li>
                      </ul>
                      <div class='general-comments'>
                        <h3>General Comments</h3>
                        <div class='textarea-wrapper'>
                        <textarea style='height: 60px; font-size: 16px' name='report'>".$this->entryBookData->getJudgeComment($judgeName)."</textarea>
                        </div>
                       <a class = 'button submitGeneralComment'>Submit Changes</a>
                      </div>
                    <th>
                  </tr>
                </thead>";

    return $html;
  }

  private function getChallengeReportsHtml($sectionName){
    $html = "";
    foreach($this->judgesReportData->challengeReports[$sectionName] as $age => $reportDataItem){
      $html .= "<tr class='body-row'>
                  <td style='background-color: transparent'>
                     <div class='section-report'>
                      <div class='report-form'>";

      $html .= $this->getClassReportClassDataHtml($reportDataItem, $sectionName);

      $html .= "        <table class='section-table'>";

      foreach($reportDataItem->placementEntries as $placement => $prizeEntry){
        if(isset($prizeEntry)){
          $placementEntry = $this->entryBookData->entries[$prizeEntry->penNumber];
          if(isset($placementEntry))
            $html .= $this->getChallengeReportPlacementHtml($reportDataItem, $placement, $placementEntry);
        }
      }
      $noEntries = (!isset($reportDataItem->placementEntries["1"]) && !isset($reportDataItem->placementEntries["2"]) && !isset($reportDataItem->placementEntries["3"]));
      $html .= ($noEntries) ? "<tr><td colspan = 3>No Entries</td></tr>" : "";

      $html .=  "        </table>
                      </div>";

      $html .= "     </div>
                    </td>
                   </tr>";
    }

    return $html;
  }

  private function getClassReportsHtml($sectionName){
    $html = "";
    foreach($this->entryBookData->getSectionData(strtolower($sectionName))->classNames as $className){
      foreach($this->judgesReportData->classReports[$className] as $age => $reportDataItem){
        $html .= "<tr class='body-row'>
                    <td style='background-color: transparent'>
                       <div class='class-report'>
                        <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-class-report' placeholder='Optional class comment'>".$reportDataItem->classComments."</textarea>
                        <div class='report-form'>";

        $html .= $this->getClassReportClassDataHtml($reportDataItem, $sectionName);

        $html .= "        <table class='class-table'>";

        foreach($reportDataItem->placementEntries as $placement => $prizeEntry){
          if(isset($prizeEntry))
            $html .= $this->getClassReportPlacementHtml($reportDataItem, $placement, $prizeEntry);
        }
        $noEntries = (!isset($reportDataItem->placementEntries["1"]) && !isset($reportDataItem->placementEntries["2"]) && !isset($reportDataItem->placementEntries["3"]));
        $html .= ($noEntries) ? "<tr><td colspan = 3>No Entries</td></tr>" : "";

        $html .=  "        </table>
                        </div>";
       $html .= "<a class = 'button submitReport'>Submit Changes</a>";

       $html .= "      </div>
                      </td>
                     </tr>";
      }
    }

    return $html;
  }

  private function getClassReportClassDataHtml($reportDataItem, $sectionName){
    $html = "                 <div class='class-details'>
                                <ul style='list-style: none'>
                                  <li>".$sectionName." Class ".$reportDataItem->classIndex."</li>
                                  <li class = 'jr-classData-li'><span class = 'jr-classData-className'>".$reportDataItem->className."</span> <span class = 'jr-classData-age'>".$reportDataItem->age."</span></li>
                                  <li>Entries: ".$reportDataItem->entryCount."</li>
                                </ul>
                              </div>";

    return $html;
  }

  private function getClassReportPlacementHtml($reportDataItem, $placement, $prizeEntry){
    $html = "";
    $entry = $this->entryBookData->entries[$prizeEntry->penNumber];
    $html .= "<tr class = 'jr-placement-tr' id = '".$reportDataItem->className."&-&".$placement."'>";
    $html .= $this->getPlacementFancierDataHtml($entry, $reportDataItem, $placement);
    $html .= $this->getPlacementReportHtml($reportDataItem, $prizeEntry, $entry, $placement);
    $html .= "</tr>";

    return $html;
  }

  private function getChallengeReportPlacementHtml($reportDataItem, $placement, $placementEntry){
    $html = "";
    if($placementEntry != NULL){
      $entry = $this->entryBookData->entries[$placementEntry->penNumber];
      $html .= "<tr class = 'jr-placement-tr' id = '".$reportDataItem->className."&-&".$placement."'>";
      $html .= $this->getChallengePlacementFancierDataHtml($entry, $reportDataItem, $placement);
      $html .= "<td></td>";
      $html .= "</tr>";
    }

    return $html;
  }

  private function getChallengePlacementFancierDataHtml($entry, $reportDataItem, $placement){
    $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
    $html = "";
    if(isset($entry)){
      $html = "<td class='jr-placement'><span>".$displayedPlacements[$placement]."</span></td>
               <td class='jr-exhibitor'>
                <div class='exhibit-details'>
                 <div>
                  <span>".$entry->userName."</span>
                 </div>
                 <div>
                  <span>".$entry->varietyName."</span>
                 </div>
                </div>
               </td>";
    }

    return $html;
  }

  private function getPlacementFancierDataHtml($entry, $reportDataItem, $placement){
    $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
    $html = "";
    if(isset($entry)){
      $html = "<td class='jr-placement'><span>".$displayedPlacements[$placement]."</span></td>
                    <td class='jr-exhibitor'>
                      <div class='exhibit-details'>
                        <div>
                          <span>".$entry->userName."</span>
                        </div>
                     <div>";
      $classSelectOptions = ClassSelectOptions::getClassSelectOptionsHtml($entry->sectionName, $this->locationID, $entry->varietyName);
      $html .= (!$this->standardClasses->isStandardClass($entry->className, $entry->sectionName)) ? "<select class='classSelect-judgesReports' id = '".$entry->penNumber."&-&varietySelect' autocomplete='off'><option value=''>Select a Variety</option>".$classSelectOptions."</select>" : "";
      $html .=      "</div>
                   </div>
                  </td>";
    }

    return $html;
  }

  private function getPlacementReportHtml($reportDataItem, $prizeEntry, $entry, $placement){
    $buckChecked = ($prizeEntry->buck) ? "checked" : "";
    $doeChecked = ($prizeEntry->doe) ? "checked" : "";
    $html = "<td>";
    $html .= "<div style='display: flex'>
               <div style='display: flex; flex-direction: column; justify-content: space-around;'>
                <div style='display: flex; align-items: center; width: 62px;'>
                 <input type='radio' id = '".$reportDataItem->className."&-&".$placement."&-&".$entry->age."&-&buck' class='buck' name='".$reportDataItem->className."&-&".$placement."&-&".$entry->age."' value='B' ".$buckChecked.">
                 <label for = '".$reportDataItem->className."&-&".$placement."&-&".$entry->age."&-&buck'>B</label>
                </div>
               <div style='display: flex; align-items: center;'>
                <input type='radio' id = '".$reportDataItem->className."&-&".$placement."&-&".$entry->age."&-&doe' class='doe' name='".$reportDataItem->className."&-&".$placement."&-&".$entry->age."' value='D' ".$doeChecked.">
                <label for = '".$reportDataItem->className."&-&".$placement."&-&".$entry->age."&-&doe'>D</label>
               </div>
              </div>
              <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-report'>".$prizeEntry->judgesComments."</textarea>";
    $html .="</td>";

    return $html;
  }
}
