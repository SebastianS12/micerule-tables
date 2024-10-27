<?php

class EntryBookController{
    public function editPlacement(int $eventPostID, PlacementsRowService $placementsRowService, int $placementNumber, int $indexID, int $entryID, int $prizeID){
        $placementsRowService->editPlacement($eventPostID, Prize::from($prizeID), $indexID, $placementNumber, $entryID);
    }

    public function editAwards(ChallengeRowService $challengeRowService, PlacementsRepository $placementsRepository, AwardsRepository $awardsRepository, int $prizeID, int $bisChallengeIndexID, int $boaChallengeIndexID){
        $challengeRowService->editAwards($placementsRepository, $awardsRepository, $prizeID, $bisChallengeIndexID, $boaChallengeIndexID);
    }

    public static function addEntry($eventPostID, $userName, $className, $age){
        $nextPenNumber = NextPenNumber::getNextPennumber(EventProperties::getEventLocationID($eventPostID), $className, $age);
        $userClassRegistration = new UserClassRegistration($eventPostID, $userName, $className, $age);
        $userClassRegistration->addUserRegistration();
        $showEntry = ShowEntry::createWithPenNumber($eventPostID, $nextPenNumber);
        $showEntry->save($userClassRegistration->registrationID, $userClassRegistration->getHighestRegistrationOrder(), $className, true, false);
        NextPenNumber::saveNextPenNumber(EventProperties::getEventLocationID($eventPostID), $className, $age, $nextPenNumber + 1);
    }

    public function editEntryAbsent(EntriesService $entriesService, int $entryID){
        $entriesService->editEntryAbsent($entryID);
    }

    public function deleteEntry(EntriesService $entriesService, int $entryID){
        $entriesService->deleteEntry($entryID);
    }

    public static function moveEntry($eventPostID, $penNumber, $newClassName, $newAge){
        $showEntry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
        $userClassRegistration = new UserClassRegistration($eventPostID, $showEntry->userName, $showEntry->className, $showEntry->age);
        $userClassRegistration->deleteUserRegistration($showEntry->getRegistrationOrder());
        $movedClassRegistration = new UserClassRegistration($eventPostID, $showEntry->userName, $newClassName, $newAge);
        $movedClassRegistration->addUserRegistration();

        $movedShowEntry = ShowEntry::createWithPenNumber($eventPostID, $showEntry->penNumber);
        $movedShowEntry->save($movedClassRegistration->registrationID, $movedClassRegistration->getHighestRegistrationOrder(), $newClassName, false, true);
    }

    public static function editBIS($age, $checkValue, $challengeAwardModel){
        if($checkValue)
            $challengeAwardModel->addAwards($age);
        else
            $challengeAwardModel->removeAwards();
    }

    public static function editVarietyName(int $entryID, string $varietyName, EntryRepository $entryRepository){
        if($varietyName != "")
            $entryRepository->updateVariety($entryID, $varietyName);
    }

    public static function getSelectOptions(ShowClassesRepository $showClassesRepository, ClassIndexRepository $classIndexRepository): void
    {
        $editEntryBookService = new EditEntryBookService();
        wp_send_json($editEntryBookService->getSelectOptions($showClassesRepository, $classIndexRepository));
    }
}