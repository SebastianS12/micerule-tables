<?php

class Absentees implements IAdminTab {
  private $eventID;

  public function __construct($eventID){
    $this->eventID = $eventID;
  }


  public function getHtml(){
    $dataFactory = new AdminTabDataFactory($this->eventID);
    $judgeData = $dataFactory->getJudgeData();

    $html = "<div class = 'absentees content' style = 'display : none'>";

    foreach($judgeData->judges as $judgeName){
      $html .= "<div class='absentees-summary'>
                  <table>
                    <thead>
                      <tr>
                        <th class='judge-absentees' colspan = 2>Judge:<br> ".$judgeName."</th>
                      </tr>
                      <tr>
                        <th>Class</th>
                        <th>Pen №</th>
                      </tr>
                    </thead>
                    <tbody>";
      foreach($judgeData->judgeClasses[$judgeName] as $sheetData){
        foreach($sheetData->getAbsentees() as $absentee){
          $html .=     "<tr>
                          <td class='abs-class'>".$sheetData->classIndex."</td>
                          <td>".$absentee."</td>
                        </tr>";
        }
      }
      $html .=      "</tbody>
                    </table>
                  </div>";
    }

    $html .= "</div>";

    return $html;
  }
}
