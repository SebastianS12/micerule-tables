<?php

class ShowOptionsService{
    public function getShowOptions(ShowOptionsRepository $showOptionsRepository, int $locationID): ShowOptions
    {
        return $showOptionsRepository->getShowOptions($locationID);
    }

    public function saveShowOptions(ShowOptionsRepository $showOptionsRepository, int $locationID, bool $allowOnlineRegistrations, float $registrationFee, float $pmFirstPlace, float $pmSecondPlace, float $pmThirdPlace, bool $allowUnstandardised, bool $allowJunior, bool $allowAuction): void
    {
        $showOptions = ShowOptions::create($allowOnlineRegistrations, $registrationFee, $allowUnstandardised, $allowJunior, $allowAuction, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace);
        $showOptionsRepository->saveShowOptions($locationID, $showOptions);
    }
}