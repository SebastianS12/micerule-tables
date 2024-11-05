<?php

class PrizeCardsView
{
    public static function getHtml($eventPostID)
    {
        $html = "<div class = 'prizeCards content' style = 'display : none'>";
        $prizeCardsRepository = new PrizeCardsRepository();
        $prizeCardsService = new PrizeCardsService($prizeCardsRepository);
        $viewModel = $prizeCardsService->prepareViewModel($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $html .= self::getPrintedPrizeCardsHtml($viewModel->getPrintedCards());
        $html .= self::getUnprintedPrizeCardsHtml($viewModel->getUnprintedCards());
        $html .= "</div>";

        return $html;
    }

    private static function getPrintedPrizeCardsHtml(array $prizeCards)
    {
        $html = "<div class='prize-cards-split-panel'>
                  <div class='prize-cards-sent'>
                    <h3>Printed</h3>
                    <div class='card-container'>
                      <div class='card-per-exhibitor'>
                        <div  class='card-wrapper firsts'>";

        foreach ($prizeCards as $prizeCardData) {
            $html .= "<div class = 'user-cards'>";
            $html .= self::getPrizeCardHtml($prizeCardData);
            $html .= "</div>";
        }

        $html .= "      </div>
                      </div>
                    </div>
                   </div>
                  </div>";

        return $html;
    }

    private static function getUnprintedPrizeCardsHtml(array $prizeCards)
    {
        $html = "<div class='prize-cards-print'>
                  <div class='print-tray-header'><h3>Labels to Print <span class='print-alert'>On Mac, these must be printed from Safari</span></h3><a class='print-button'><img src='/wp-content/plugins/micerule-tables/admin/svg/print.svg'></a></div>
                    <div class='card-container'>";

        foreach ($prizeCards as $prizeCardData) {
            $html .= self::getPrizeCardHtml($prizeCardData);
        }

        $html .=    "</div>
                    </div>";

        return $html;
    }

    private static function getPrizeCardHtml(PrizeCardModel $prizeCard)
    {
        $html = " <div class= 'prize-card class-card " . $prizeCard->placementClass . " " . $prizeCard->prizeClass . "' data-placement-id= ".$prizeCard->placementID." data-prize = ".$prizeCard->prize->value.">
               <ul class='card-content-wrapper'>
                <li style = 'visibility:hidden; position: absolute; top: 0;'><span class = 'prize'>" . $prizeCard->prize->value . "</span><span class = 'prize-card-section-name'>" . $prizeCard->section . "</span></li>
                <li style = 'visibility:hidden; position: absolute; top: 0;'><span class = 'prize-card-class-name'>" . $prizeCard->className . "</span><span class = 'prize-card-age'>" . $prizeCard->age . "</span><span class = 'prize-card-placement'>" . $prizeCard->placement . "</span></li>
                <li><span class='line exhibitor'>" . $prizeCard->userName . "</span><span class='placed card-info'>" . $prizeCard->indexNumber . ", " . $prizeCard->className . " " . $prizeCard->age . "</span><span class='placed'>" . $prizeCard->displayedPlacement . "</span>";

        $html .= ($prizeCard->printed) ? "<div class='label-print-options'>
                            <a class = 'move-to-unprinted'><img src='/wp-content/plugins/micerule-tables/admin/svg/to-print-tray.svg'></a>
                           </div>" : "";

        $hideVarietyName = ($prizeCard->prize == Prize::STANDARD && $prizeCard->className == $prizeCard->varietyName) ? "style = 'display : none'" : "";
        $html .= "   </li>
                 <li><span class='line class'>" . $prizeCard->indexNumber . ", " . $prizeCard->className . " " . $prizeCard->age . "</span><span class='line variety' " . $hideVarietyName . ">" . $prizeCard->varietyName . "</span><span class = 'line entry-count'>" . $prizeCard->entryCount . "</span><span class='line pen-no'>" . $prizeCard->penNumber . "</span></li>
                 <li><span class='line judge'>" . $prizeCard->judge . "</span><span class='line date'>" . $prizeCard->date . "</span></li>
                </ul>
               </div>";

        return $html;
    }
}
