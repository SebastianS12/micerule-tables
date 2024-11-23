<?php

class EntryBookController{
    public function editPlacement(int $placementNumber, int $indexID, int $entryID, int $prizeID): WP_REST_Response
    {
        $event_id = EventHelper::getEventPostID();
        $prize = Prize::from($prizeID);
        $placementsRepository = new PlacementsRepository($event_id, PlacementDAOFactory::getPlacementDAO($prize->value));
        $placementsRowService = new PlacementsRowService();
        $placementsRowService->editPlacement($placementsRepository, $prize, $indexID, $placementNumber, $entryID);

        return new WP_REST_Response("");
    }

    public function editAwards(int $prizeID, int $bisChallengeIndexID, int $boaChallengeIndexID): WP_REST_Response
    {
        $eventPostID = EventHelper::getEventPostID();
        $placementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO);
        $awardsRepository = new AwardsRepository($eventPostID);
        $challengeRowService = new ChallengeRowService($eventPostID);
        $challengeRowService->editAwards($placementsRepository, $awardsRepository, $prizeID, $bisChallengeIndexID, $boaChallengeIndexID);

        return new WP_REST_Response("");
    }

    public static function addEntry(string $userName, int $classIndexID): WP_REST_Response
    {
        $eventPostID = EventHelper::getEventPostID();
        $locationID = LocationHelper::getIDFromEventPostID($eventPostID);
        $entryBookService = new EntryBookService();
        $entryBookService->addEntry($eventPostID, $classIndexID, $userName, new ClassIndexRepository($locationID), new UserRegistrationsRepository($eventPostID), new RegistrationOrderRepository($eventPostID), new EntryRepository($eventPostID));

        return new WP_REST_Response("");
    }

    public function editAbsent(int $entryID): WP_REST_Response
    {
        $entriesService = new EntriesService(new EntryRepository(EventHelper::getEventPostID()));
        $entriesService->editEntryAbsent($entryID);

        return new WP_REST_Response("");
    }

    public function deleteEntry(int $entryID): WP_REST_Response
    {
        $eventPostID = EventHelper::getEventPostID();
        $entryBookService = new EntryBookService();
        $entryBookService->deleteEntry($entryID, new EntryRepository($eventPostID), new RegistrationOrderRepository($eventPostID), new UserRegistrationsRepository($eventPostID), new JuniorRegistrationRepository($eventPostID));

        return new WP_REST_Response("");
    }

    public function moveEntry(int $entryID, int $newClassIndexID): WP_REST_Response
    {
        $eventPostID = EventHelper::getEventPostID();
        $locationID = LocationHelper::getIDFromEventPostID($eventPostID);
        $entryBookService = new EntryBookService();
        $entryBookService->moveEntry($entryID, $newClassIndexID, new EntryRepository($eventPostID), new ClassIndexRepository($locationID), new UserRegistrationsRepository($eventPostID), new RegistrationOrderRepository($eventPostID));

        return new WP_REST_Response("");
    }

    public static function editVarietyName(int $entryID, string $varietyName): WP_REST_Response{
        $eventPostID = EventHelper::getEventPostID();
        $entryRepository = new EntryRepository($eventPostID);
        $entryService = new EntriesService($entryRepository);
        if($varietyName != "")
            $entryService->editVarietyName($entryID, $varietyName, $entryRepository);

        return new WP_REST_Response("");
    }

    public static function getSelectOptions(): WP_REST_Response
    {
        $locationID = LocationHelper::getIDFromEventPostID(EventHelper::getEventPostID());
        $showClassesRepository = new ShowClassesRepository($locationID);
        $classIndexRepository = new ClassIndexRepository($locationID);
        $editEntryBookService = new EditEntryBookService();

        return new WP_REST_Response($editEntryBookService->getSelectOptions($showClassesRepository, $classIndexRepository));
    }
}