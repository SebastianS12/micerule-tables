<?php

class ShowOptionsService{
    public function getShowOptions(ShowOptionsRepository $showOptionsRepository, int $locationID): ShowOptionsModel
    {
        return $showOptionsRepository->getShowOptions($locationID);
    }

    public function saveShowOptions(ShowOptionsRepository $showOptionsRepository, ?int $id, int $locationID, bool $allowOnlineRegistrations, float $registrationFee, float $pmFirstPlace, float $pmSecondPlace, float $pmThirdPlace, bool $allowUnstandardised, bool $allowJunior, bool $allowAuction, float $pmBiSec, float $pmBoSec, float $pmBIS, float $pmBOA): void
    {
        $showOptionsModel = ($id !== null) ? ShowOptionsModel::createWithID($id, $locationID, $allowOnlineRegistrations, $registrationFee, $allowUnstandardised, $allowJunior, $allowAuction, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace, $pmBiSec, $pmBoSec, $pmBIS, $pmBOA) : ShowOptionsModel::create($locationID, $allowOnlineRegistrations, $registrationFee, $allowUnstandardised, $allowJunior, $allowAuction, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace, $pmBiSec, $pmBoSec, $pmBIS, $pmBOA);
        $showOptionsRepository->saveShowOptions($showOptionsModel);
    }
}