<?php

class Label implements IAdminTab {
  private $eventID;
  private $entryBookData;
  private $userEntryData;

  public function __construct($eventID){
    $this->eventID = $eventID;

    $adminTabDataFactory = new AdminTabDataFactory($this->eventID);
    $this->userEntryData = $adminTabDataFactory->getUserEntryData();
  }


  function getHtml(){
    $html = "<div class = 'label content' style = 'display : none'>";
    $html .= "<div class='print-tray-header'><h3>Labels Print Preview<span class='print-alert'>On Mac, these must be printed from Safari</span></h3><a class='print-button'><img src='/wp-content/plugins/micerule-tables/admin/svg/print.svg'></a></div>";
    $html .= "<div class = 'printLabels'>";
    foreach($this->userEntryData->userEntries as $userName => $userLabelData){
      $html .= "<div class = 'label-block'>";
      $html .= "<ul class='card-wrapper'>";
      $html .= "<li class='pen-label fancier-name-label'>
                  <div>
                    <span>".$userName."</span>
                  </div>
                </li>";
      foreach($userLabelData as $labelData){
        if($labelData->className != "Junior"){
          $absentClass = ($labelData->absent) ? "absent" : "";
          $html .= "<li class='pen-label ".$absentClass."'>
                      <div class='label-class'>
                        <span class='label-header'>CLASS</span>
                        <span class='label-class-no'>".$labelData->classIndex."</span>
                      </div>
                      <div class='label-pen'>
                        <span class='label-header'>PEN</span>
                        <span  class='label-pen-no'>".$labelData->penNumber."</span>
                      </div>
                    </li>";
        }
      }
      $html .= "</ul>";
      $html .= "</div>";
    }
    $html .= "</div>";
    $html .= "</div>";

    return $html;
  }
}
