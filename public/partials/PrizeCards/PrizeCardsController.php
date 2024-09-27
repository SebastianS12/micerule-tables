<?php

class PrizeCardsController
{
    private PrizeCardsService $prizeCardsService;

    public function __construct(PrizeCardsService $prizeCardsService)
    {
        $this->prizeCardsService = $prizeCardsService;
    }
    public function preparePrizeCardsData(int $eventPostID, JudgesService $judgesService): array{
        return $this->prizeCardsService->preparePrizeCardsData($eventPostID, $judgesService);
    }

    public function printAll(int $eventPostID){
        $this->prizeCardsService->printAll($eventPostID);
    }

    public function moveToUnprinted(int $placementID, int $prizeID){
        $this->prizeCardsService->moveToUnprinted($placementID, $prizeID);
    }
}
