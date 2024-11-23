<?php

class ShowOptionsService{
    public function getShowOptions(ShowOptionsRepository $showOptionsRepository, int $locationID): ShowOptionsModel
    {
        return $showOptionsRepository->getShowOptions($locationID);
    }

    public function saveShowOptions(ShowOptionsRepository $showOptionsRepository, ?int $id, int $locationID, bool $allowOnlineRegistrations, float $registrationFee, float $pmFirstPlace, float $pmSecondPlace, float $pmThirdPlace, bool $allowUnstandardised, bool $allowJunior, bool $allowAuction, float $pmBiSec, float $pmBoSec, float $pmBIS, float $pmBOA, float $auctionFee): void
    {
        $showOptionsModel = ($id !== null) ? ShowOptionsModel::createWithID($id, $locationID, $allowOnlineRegistrations, $registrationFee, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace, $allowUnstandardised, $allowJunior, $allowAuction, $pmBiSec, $pmBoSec, $pmBIS, $pmBOA, $auctionFee) : ShowOptionsModel::create($locationID, $allowOnlineRegistrations, $registrationFee, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace, $allowUnstandardised, $allowJunior, $allowAuction, $pmBiSec, $pmBoSec, $pmBIS, $pmBOA, $auctionFee);
        $showOptionsRepository->saveShowOptions($showOptionsModel);
    }
}