<?php

class PrizeCardsController
{
    public function printAll(array $prizeCardsToPrint): WP_REST_Response
    {
        $prizeCardsService = new PrizeCardsService(new PrizeCardsRepository());
        $prizeCardsService->printAll($prizeCardsToPrint);

        return new WP_REST_Response(Logger::getInstance()->getLogs());
    }

    public function moveToUnprinted(int $placementID, int $prizeID): WP_REST_Response
    {
        $prizeCardsService = new PrizeCardsService(new PrizeCardsRepository());
        $prizeCardsService->moveToUnprinted($placementID, $prizeID);

        return new WP_REST_Response("");
    }
}
