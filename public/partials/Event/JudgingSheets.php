<?php

class JudgingSheets implements IAdminTab{
  private $eventID;
  private $standardClasses;

  public function __construct($eventID){
    $this->eventID = $eventID;
    $this->standardClasses = new StandardClasses();
  }


  function getHtml(){
    $dataFactory = new AdminTabDataFactory($this->eventID);
    $judgeData = $dataFactory->getJudgeData();

    $html = "<div class = 'judgingSheets content' style = 'display : none'>";
    $html .= "<div class = 'sheet-set'>";

    $html .= $this->getGrandChallengeSheets($judgeData->judgeGrandChallenge, $judgeData->judges);
    foreach($judgeData->judges as $judgeName){
      $html .= $this->getClassSheets($judgeData->judgeClasses[$judgeName], $judgeName);
    }
    $html .= $this->getClassSheets($judgeData->optionalClasses, "");
    $html .= "</div>";
    $html .= "</div>";

    return $html;
  }

  private function getGrandChallengeSheets($judgeGrandChallenge, $judgeNames){
    $html = "";
    $grandChallengeJudgeName = "";
    foreach($judgeNames as $judgeName){
      $grandChallengeJudgeName .= $judgeName."  ";
    }
    foreach($judgeGrandChallenge as $sheetData){
      $html .= $this->getChallengeSheetHtml($sheetData, $grandChallengeJudgeName);
    }
    return $html;
  }

  private function getClassSheets($judgeClasses, $judgeName){
    $html = "";
    foreach($judgeClasses as $sheetData){
      if($sheetData->challengeSheet)
        $html .= $this->getChallengeSheetHtml($sheetData, $judgeName);
      else
        $html .= $this->getSheetHtml($sheetData, $judgeName);
    }
    return $html;
  }


  private function getSheetHtml($sheetData, $judgeName){
    $html = "<div class='breed-class-report'>
              <table>
                <thead>";
    $html .= $this->getSheetHeaderHtml($sheetData, $judgeName);
    $html .= "  </thead>
                <tbody>";

    if(count($sheetData->entries) > 0){
      foreach($sheetData->entries as $entry){
        $showVarietyPrompt = ($this->standardClasses->isStandardClass($entry->className, $entry->sectionName)) ? "style = 'display : none'" : "";
        $html .= $this->getSheetEntryRowHtml("", $entry->penNumber, $showVarietyPrompt);
      }
    }else{
      $html .= "<tr><span>No Entries</span></tr>";
    }

    $html .= $this->getEmptyRowHtml();
    $html .=      "</tbody>
    </table>
    </div>";

    return $html;
  }

  private function getChallengeSheetHtml($sheetData, $judgeName){
    $html = "<div class='breed-class-report challenge'>
              <table>
               <thead>";
    $html .= $this->getSheetHeaderHtml($sheetData, $judgeName);
    $html .= " </thead>
               <tbody>";

    for($placement = 1; $placement < 4; $placement++){
      $html .= $this->getSheetEntryRowHtml($placement, "", "style = 'display : none'");
    }

    if($sheetData->age == "U8"){
      $section = ($sheetData->className == "Grand Challenge") ? "" : explode(" ", $sheetData->className)[0];
      $html .= $this->getChallengeSheetBestHtml($section, "Best");
      $html .= $this->getChallengeSheetBestHtml($section, "BOA");
    }

    $html .= $this->getEmptyRowHtml();
    $html .=   "</tbody>
              </table>
             </div>";

    return $html;
  }

  private function getSheetHeaderHtml($sheetData, $judgeName){
    $html = "<tr>
              <th colspan=3><span>Class ".$sheetData->classIndex." | ".$sheetData->className." | ".$sheetData->age." - Judge: <strong>".$judgeName."</strong></span></th>
              <th></th>
              <th colspan ='2' class='side-slip'><p> Class ".$sheetData->classIndex."</p></th>
              </tr>
              <tr>
              <th class='js-pen-no'>№</th>
              <th class='js-award'>Pl.</th>
              <th class='js-notes'>Judge's Notes</th>
              <th class='perforation'></th>
              <th class='js-pen-no'>№</th>
              <th class='js-award'>Pl.</th>
            </tr>";

   return $html;
  }

  private function getSheetEntryRowHtml($placement, $penNumber, $showVarietyPrompt){
    $html = "<tr>
              <td class='js-pen-no'>".$penNumber."</td>
              <td class='js-award'>".$placement."</td>
              <td class='js-notes'><span class='variety-prompt' ".$showVarietyPrompt.">! →</span></td>
              <td class='perforation'></td>
              <td class='js-pen-no'><hr ".$showVarietyPrompt."></td>
              <td class='js-award'>".$placement."</td>
            </tr>";

    return $html;
  }

  private function getChallengeSheetBestHtml($section, $best){
    //static Best/BOA block
    $html =     "<tr>
    <td class='js-pen-no'></td>
    <td class='js-award'></td>
    <td class='js-notes'></td>
    <td class='perforation'></td>
    <td class='js-pen-no'></td>
    <td class='js-award'></td>
    </tr>";
    $html .=     "<tr>
    <td class='js-pen-no'></td>
    <td class='js-award'></td>
    <td class='js-notes'></td>
    <td class='perforation'></td>
    <td class='js-pen-no' colspan = 2>".$best." ".$section."</td>
    </tr>";
    $html .=     "<tr>
    <td class='js-pen-no'></td>
    <td class='js-award'></td>
    <td class='js-notes'></td>
    <td class='perforation'></td>
    <td class='js-pen-no'>№</td>
    <td class='js-award'></td>
    </tr>";
    $html .=     "<tr>
    <td class='js-pen-no'></td>
    <td class='js-award'></td>
    <td class='js-notes'></td>
    <td class='perforation'></td>
    <td class='js-pen-no'>Age</td>
    <td class='js-award'></td>
    </tr>";
    $html .=     "<tr>
    <td class='js-pen-no'></td>
    <td class='js-award'></td>
    <td class='js-notes'></td>
    <td class='perforation'></td>
    <td class='js-pen-no'>Var</td>
    <td class='js-award'></td>
    </tr>";

    return $html;
  }

  private function getEmptyRowHtml(){
    $html = "<tr>
              <td class='js-pen-no'></td>
              <td class='js-award'></td>
              <td class='js-notes'></td>
              <td class='perforation'></td>
              <td class='js-pen-no'></td>
              <td class='js-award'></td>
            </tr>";

    return $html;
  }
}
