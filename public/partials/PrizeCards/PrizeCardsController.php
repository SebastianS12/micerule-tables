<?php

class PrizeCardsController
{
    public static function updatePrizeCardsPrinted($eventPostID, $prizeCardsData, $print)
    {
        $prizeCardsModel = new PrizeCardsModel();
        foreach ($prizeCardsData as $prizeCardData) {
            if($prizeCardData->placementID != null){
                $prizeCard = PrizeCardFactory::createPrizeCard($eventPostID, $prizeCardsModel->getSinglePrizeCard($prizeCardData->placementID, $prizeCardData->prize));
                $prizeCard->updatePrinted($print);
            }
        }
    }
}
