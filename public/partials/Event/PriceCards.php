<?php

class PriceCards implements IAdminTab {
  private $eventID;

  public function __construct($eventID){
    $this->eventID = $eventID;
  }


  public function getHtml(){
    $dataFactory = new AdminTabDataFactory($this->eventID);
    $prizeCardData = $dataFactory->getPrizeCardData();

    $html = "<div class = 'prizeCards content' style = 'display : none'>";
    $html .= $this->getPrintedPrizeCardsHtml($prizeCardData->printedCards);
    $html .= $this->getUnprintedPrizeCardsHtml($prizeCardData->unprintedCards);
    $html .= "</div>";

    return $html;
  }

  private function getPrintedPrizeCardsHtml($prizeCards){
    $html = "<div class='prize-cards-split-panel'>
              <div class='prize-cards-sent'>
                <h3>Printed</h3>
                <div class='card-container'>
                  <div class='card-per-exhibitor'>
                    <div  class='card-wrapper firsts'>";

    foreach($prizeCards as $userName => $userPrizeCards){
      $html .= "<div class = 'user-cards'>";
      foreach($userPrizeCards as $prizeCard){
        $html .= $this->getPrizeCardHtml($prizeCard, true);
      }
      $html .= "</div>";
    }

    $html .= "      </div>
                  </div>
                </div>
               </div>
              </div>";

    return $html;
  }

  private function getUnprintedPrizeCardsHtml($prizeCards){
    $html = "<div class='prize-cards-print'>
              <div class='print-tray-header'><h3>Labels to Print <span class='print-alert'>On Mac, these must be printed from Safari</span></h3><a class='print-button'><img src='/wp-content/plugins/micerule-tables/admin/svg/print.svg'></a></div>
                <div class='card-container'>";

    foreach($prizeCards as $prizeCard){
      $html .= $this->getPrizeCardHtml($prizeCard, false);
    }

    $html .=    "</div>
                </div>";

    return $html;
  }

  private function getPrizeCardHtml($prizeCard, $printed){
    //$className = ($prizeCard->getChallengeName() != "") ? $prizeCard->getChallengeName() : $prizeCard->getClassName();
    //$displayedPlacement = $this->getDisplayedPlacement($prizeCard);
    $placementClass = ($prizeCard->placement == "1") ? "first" : "second";
    $placementClass = ($prizeCard->placement == "3") ? "third" : $placementClass;
    $placementEntry = $prizeCard->placementEntry;
    $prizeClass = ($prizeCard->prize == "Class") ? "breed-class" : "section-challenge";
    $prizeClass = ($prizeCard->prize == "Grand Challenge") ? "grand-challenge" : $prizeClass;
    $html = " <div class= 'prize-card class-card ".$placementClass." ".$prizeClass."'>
               <ul class='card-content-wrapper'>
                <li style = 'visibility:hidden; position: absolute; top: 0;'><span class = 'prize'>".$prizeCard->prize."</span><span class = 'prize-card-section-name'>".$placementEntry->sectionName."</span></li>
                <li style = 'visibility:hidden; position: absolute; top: 0;'><span class = 'prize-card-class-name'>".$prizeCard->challengeName."</span><span class = 'prize-card-age'>".$placementEntry->age."</span><span class = 'prize-card-placement'>".$prizeCard->placement."</span></li>
                <li><span class='line exhibitor'>".$placementEntry->userName."</span><span class='placed card-info'>".$prizeCard->classIndex.", ".$placementEntry->varietyName." ".$placementEntry->age."</span><span class='placed'>".$prizeCard->displayPlacement."</span>";

    $html .= ($printed) ? "<div class='label-print-options'>
                            <a class = 'move-to-unprinted'><img src='/wp-content/plugins/micerule-tables/admin/svg/to-print-tray.svg'></a>
                           </div>" : "";

    $hideVarietyName = ($prizeCard->prize == "Class" && $placementEntry->className == $placementEntry->varietyName) ? "style = 'display : none'" : "";
    $html .= "   </li>
                 <li><span class='line class'>".$prizeCard->classIndex.", ".$prizeCard->challengeName." ".$placementEntry->age."</span><span class='line variety' ".$hideVarietyName.">".$placementEntry->varietyName."</span><span class = 'line entry-count'>".$prizeCard->entryCount."</span><span class='line pen-no'>".$placementEntry->penNumber."</span></li>
                 <li><span class='line judge'>".$prizeCard->judge."</span><span class='line date'>".$prizeCard->date."</span></li>
                </ul>
               </div>";

    return $html;
  }
}
