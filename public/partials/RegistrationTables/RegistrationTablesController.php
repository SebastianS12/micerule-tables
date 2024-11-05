<?php

//TODO: Duplicate Code from ShowOptionsController -> create ShowOptions Model
class RegistrationTablesController{
    private RegistrationTablesService $registrationTablesService;

    public function __construct(RegistrationTablesService $registrationTablesService)
    {
        $this->registrationTablesService = $registrationTablesService;
    }

    public function prepareViewModel(int $eventPostID, int $locationID, string $userName): RegistrationTablesViewModel{
        return $this->registrationTablesService->prepareViewModel($eventPostID, $locationID, $userName);
    }

    public static function getAllowOnlineRegistrations(int $locationID){
        $showOptionsService = new ShowOptionsService();
        $showOptions = $showOptionsService->getShowOptions(new ShowOptionsRepository(), $locationID);
        $allowOnlineRegistrations = $showOptions->allowOnlineRegistrations;
        return $allowOnlineRegistrations;
    }
}