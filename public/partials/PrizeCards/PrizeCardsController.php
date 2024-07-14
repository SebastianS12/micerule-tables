<?php

class PrizeCardsController
{
    public static function updatePrizeCardsPrinted($eventPostID, $prizeCardsData, $print)
    {
        $prizeCardsModel = new PrizeCardsModel();
        foreach ($prizeCardsData as $prizeCardData) {
            $prizeCard = PrizeCardFactory::createPrizeCard($eventPostID, $prizeCardsModel->getSinglePrizeCard($eventPostID, !$print, $prizeCardData->penNumber, $prizeCardData->prize));
            $prizeCard->updatePrinted($print);
        }
    }
}
