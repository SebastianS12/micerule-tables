<?php

class ShowOptionsController{
    public static function getShowOptions(int $locationID, ShowOptionsService $showOptionsService, ShowOptionsRepository $showOptionsRepository): ShowOptionsModel
    {
        return $showOptionsService->getShowOptions($showOptionsRepository, $locationID);
    }

    public function saveShowOptions(?int $id, int $locationID, bool $allowOnlineRegistrations, float $registrationFee, float $pmFirstPlace, float $pmSecondPlace, float $pmThirdPlace, bool $allowUnstandardised, bool $allowJunior, bool $allowAuction, float $pmBiSec, float $pmBoSec, float $pmBIS, float $pmBOA, ?float $auctionFee): WP_REST_Response
    {
        $showOptionsService = new ShowOptionsService();
        $showOptionsService->saveShowOptions(new ShowOptionsRepository, $id, $locationID, $allowOnlineRegistrations, $registrationFee, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace, $allowUnstandardised, $allowJunior, $allowAuction, $pmBiSec, $pmBoSec, $pmBIS, $pmBOA, $auctionFee ?? 0.0);

        return new WP_REST_Response(Logger::getInstance()->getLogs());
    }
}