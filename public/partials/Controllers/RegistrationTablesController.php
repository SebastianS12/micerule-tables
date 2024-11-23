<?php

//TODO: Duplicate Code from ShowOptionsController -> create ShowOptions Model
class RegistrationTablesController{

    public function prepareViewModel(int $eventPostID, int $locationID, string $userName): RegistrationTablesViewModel{
        $registrationTablesService = new RegistrationTablesService(new ChallengeIndexRepository($locationID), new ShowClassesRepository($locationID), new ClassIndexRepository($locationID), new RegistrationCountRepository($eventPostID, $locationID));
        return $registrationTablesService->prepareViewModel($eventPostID, $locationID, $userName);
    }

    public static function getAllowOnlineRegistrations(int $locationID){
        $showOptionsService = new ShowOptionsService();
        $showOptions = $showOptionsService->getShowOptions(new ShowOptionsRepository(), $locationID);
        $allowOnlineRegistrations = $showOptions->allow_online_registrations;
        return $allowOnlineRegistrations;
    }

    public function register(array $classRegistrations, string $fancierName): WP_REST_Response
    {
        $eventPostID = EventHelper::getEventPostID();
        $userRegistrationsRepository = new UserRegistrationsRepository($eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($eventPostID);
        $registrationCountRepository = new RegistrationCountRepository($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $classIndexRepository = new ClassIndexRepository(LocationHelper::getIDFromEventPostID($eventPostID));

        $registrationService = new RegistrationService($eventPostID, $userRegistrationsRepository, $registrationOrderRepository, $registrationCountRepository, $classIndexRepository);
        $registrations = $registrationService->registerEntries($classRegistrations, $fancierName);

        $entriesService = new EntriesService(new EntryRepository($eventPostID));
        $entriesService->createEntriesFromRegistrations(LocationHelper::getIDFromEventPostID($eventPostID), $eventPostID);

        return new WP_REST_Response(RegistrationTablesView::getUserRegistrationOverviewHtml($fancierName, $registrations));
    }

    public function updateRegistrationTables(string $fancierName): WP_REST_Response
    {
        $eventPostID = EventHelper::getEventPostID();
        return new WP_REST_Response(RegistrationTablesView::getRegistrationTablesHtml($eventPostID, $fancierName));
    }
}