<?php

class JudgesReport implements IAdminTab {

  public function __construct($eventID){
    $this->eventID = $eventID;
    $this->entryBookData = EntryBookData::create($eventID);
    $this->locationID = EventProperties::getEventLocationID($eventID);
    $this->standardClasses = new StandardClasses();
    global $wpdb;
    $this->wpdb = $wpdb;
  }

  function getHtml(){
    $judgesReportData = new JudgesReportData($this->eventID);
    $user = wp_get_current_user();
    $userName = $user->display_name;
    //$html = "<p>".var_export($judgesReportData->judgesClassReportData, true)."</p>";
    $html = "<div class = 'judgesReport content' style = 'display: none'>";

    foreach($judgesReportData->judges as $judgeName){
      if($userName == $judgeName || current_user_can('administrator')){
        $html .= "<table>";
        $html .= $this->getJudgeReportHeaderHtml($judgeName);
        $html .= $this->getClassReportHtml($judgeName, $judgesReportData);
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
                        <textarea style='height: 60px; font-size: 16px' name='report'>".$this->entryBookData->judgesComments[$judgeName]."</textarea>
                        </div>
                       <a class = 'button submitGeneralComment'>Submit Changes</a>
                      </div>
                    <th>
                  </tr>
                </thead>";

    return $html;
  }

  private function getClassReportHtml($judgeName, $judgesReportData){
    $html = "";
    foreach($judgesReportData->judgesReportData[$judgeName] as $classIndex => $reportDataItem){
      $html .= "<tr class='body-row'>
                  <td style='background-color: transparent'>
                     <div class='class-report'>
                      <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-class-report' placeholder='Optional class comment'>".$reportDataItem->classComments."</textarea>
                      <div class='report-form'>";

      $html .= $this->getClassReportClassDataHtml($reportDataItem);

      $html .= "        <table class='class-table'>";

      foreach($reportDataItem->placementEntries as $placement => $prizeEntry){
        $placementEntry = $this->entryBookData->entries[$prizeEntry->penNumber];
        if(isset($placementEntry))
          $html .= $this->getClassReportPlacementHtml($reportDataItem, $placement, $placementEntry);
      }
      $noEntries = (!isset($reportDataItem->placementEntries["1"]) && !isset($reportDataItem->placementEntries["2"]) && !isset($reportDataItem->placementEntries["3"]));
      $html .= ($noEntries) ? "<tr><td colspan = 3>No Entries</td></tr>" : "";

      $html .=  "        </table>
                      </div>";
     $html .= ($reportDataItem->classReport && !$noEntries) ? "<a class = 'button submitReport'>Submit Changes</a>" : "";

     $html .= "      </div>
                    </td>
                   </tr>";
    }

    return $html;
  }

  private function getClassReportClassDataHtml($reportDataItem){
    $html = "                 <div class='class-details'>
                                <ul style='list-style: none'>
                                  <li>".$reportDataItem->sectionName." Class ".$reportDataItem->classIndex."</li>
                                  <li class = 'jr-classData-li'>".$reportDataItem->className." ".$reportDataItem->age."</li>
                                  <li>Entries: ".$reportDataItem->entryCount."</li>
                                </ul>
                              </div>";

    return $html;
  }

  private function getClassReportPlacementHtml($reportDataItem, $placement, $placementEntry){
    $html = "";
    if($placementEntry != NULL){
      $entry = $this->entryBookData->entries[$placementEntry->penNumber];
      $html .= "<tr class = 'jr-placement-tr' id = '".$reportDataItem->className."&-&".$placement."'>";
      $html .= $this->getPlacementFancierDataHtml($entry, $reportDataItem, $placement);
      $html .= $this->getPlacementReportHtml($reportDataItem, $placementEntry, $entry, $placement);
      $html .= "</tr>";
    }

    return $html;
  }

  private function getPlacementFancierDataHtml($entry, $reportDataItem, $placement){
    $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');

    $html = "<td class='jr-placement'>".$displayedPlacements[$placement]."</td>
                  <td class='jr-exhibitor'>
                    <div style='display: flex; flex-direction: column;'>
                      <div>
                        <span>".$entry->userName."</span>
                      </div>
                   <div>";
    $classSelectOptions = ClassSelectOptions::getClassSelectOptionsHtml($entry->sectionName, $this->locationID, $entry->varietyName);
    $html .= (!$this->standardClasses->isStandardClass($entry->className, $entry->sectionName)) ? "<select class='classSelect-judgesReports' id = '".$entry->penNumber."&-&varietySelect' autocomplete='off'><option value=''>Select a Variety</option>".$classSelectOptions."</select>" : "";
    $html .=      "</div>
                 </div>
                </td>";

    return $html;
  }

  private function getPlacementReportHtml($reportDataItem, $placementEntry, $entry, $placement){
    $buckChecked = ($placementEntry->buck) ? "checked" : "";
    $doeChecked = ($placementEntry->doe) ? "checked" : "";
    $html = "<td>";
    $html .= ($reportDataItem->classReport) ? "<div style='display: flex'>
                                                <div style='display: flex; flex-direction: column; justify-content: space-around;'>
                                                  <div style='display: flex; align-items: center; width: 62px;'>
                                                  <input type='radio' class='buck' name='".$reportDataItem->className."&-&".$placement."&-&".$entry->age."' value='B' ".$buckChecked.$placementEntry->buck.">
                                                  <label for='buck'>B</label>
                                                  </div>
                                                  <div style='display: flex; align-items: center;'>
                                                  <input type='radio' class='doe' name='".$reportDataItem->className."&-&".$placement."&-&".$entry->age."' value='D' ".$doeChecked.">
                                                  <label for='doe'>D</label>
                                                  </div>
                                                </div>
                                            <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-report'>".$placementEntry->judgesComments."</textarea>" : "";
    $html .= "</td>";

    return $html;
  }


  private function getClassSelectOptions($sectionName, $locationID, $selectedVariety){/*
    global $wpdb;
    $varietyOptions = $this->wpdb->get_results("SELECT option_name FROM ".$this->wpdb->prefix."options WHERE option_name LIKE 'mrTables%".$sectionName."'",ARRAY_A);
    $eventClasses = get_post_meta($locationID, 'micerule_data_event_classes',true);

    $selectOptions = array();
    foreach($varietyOptions as $varietyOption){
      $varietyName = get_option($varietyOption['option_name'])['name'];
      if(!in_array($varietyName, $eventClasses[$sectionName])){
        array_push($selectOptions, $varietyName);
      }
    }

    $optionsHtml = "";
    foreach($selectOptions as $selectOption){
      $optionSelected = ($selectedVariety == $selectOption) ? "selected" : "";
      $optionsHtml .= "<option value = '".$selectOption."' ".$optionSelected.">".$selectOption."</option>";
    }

    return $optionsHtml;
  */}
}
