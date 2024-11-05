<?php

class PrizeCardsController
{
    private PrizeCardsService $prizeCardsService;

    public function __construct(PrizeCardsService $prizeCardsService)
    {
        $this->prizeCardsService = $prizeCardsService;
    }

    public function printAll(array $prizeCardsToPrint){
        $this->prizeCardsService->printAll($prizeCardsToPrint);
    }

    public function moveToUnprinted(int $placementID, int $prizeID){
        $this->prizeCardsService->moveToUnprinted($placementID, $prizeID);
    }
}
