<?php

class EntryBookController{
    public function editPlacement(int $eventPostID, PlacementsRowService $placementsRowService, int $placementNumber, int $indexID, int $entryID, int $prizeID){
        $placementsRowService->editPlacement($eventPostID, Prize::from($prizeID), $indexID, $placementNumber, $entryID);
    }

    public function editAwards(ChallengeRowService $challengeRowService, PlacementsRepository $placementsRepository, AwardsRepository $awardsRepository, int $prizeID, int $bisChallengeIndexID, int $boaChallengeIndexID){
        $challengeRowService->editAwards($placementsRepository, $awardsRepository, $prizeID, $bisChallengeIndexID, $boaChallengeIndexID);
    }

    public static function addEntry(EntryBookService $entryBookService, int $eventPostID, int $locationID, string $userName, int $classIndexID){
        $entryBookService->addEntry($eventPostID, $classIndexID, $userName, new ClassIndexRepository($locationID), new UserRegistrationsRepository($eventPostID), new RegistrationOrderRepository($eventPostID), new EntryRepository($eventPostID));
    }

    public function editEntryAbsent(EntriesService $entriesService, int $entryID){
        $entriesService->editEntryAbsent($entryID);
    }

    public function deleteEntry(int $entryID, int $eventPostID){
        $entryBookService = new EntryBookService();
        $entryBookService->deleteEntry($entryID, new EntryRepository($eventPostID), new RegistrationOrderRepository($eventPostID), new UserRegistrationsRepository($eventPostID), new JuniorRegistrationRepository($eventPostID));
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