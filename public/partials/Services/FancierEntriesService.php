<?php

class FancierEntriesService{
    private int $eventPostID;
    private int $locationID;

    public function __construct(int $eventPostID, int $locationID)
    {
        $this->eventPostID = $eventPostID;
        $this->locationID = $locationID;
    }

    public function prepareViewModel(): FancierEntriesViewModel
    {
        $viewModel = new FancierEntriesViewModel();

        $showOptionsRepository = new ShowOptionsRepository();
        $showOptions = $showOptionsRepository->getShowOptions($this->locationID);

        $showClassRepository = new ShowClassesRepository($this->locationID);
        $classIndexRepository = new ClassIndexRepository($this->locationID);
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $juniorRegistrationRepository = new JuniorRegistrationRepository($this->eventPostID);

        $showClassDataLoader = new ShowClassDataLoader();
        $showClassDataLoader->load($showClassRepository);
        $showClassDataLoader->withEntries($classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, new EntryRepository($this->eventPostID));
        $showClassDataLoader->withClassPlacements(new PlacementsRepository($this->eventPostID, new ClassPlacementDAO));
        $showClassDataLoader->withChallengePlacements(new PlacementsRepository($this->eventPostID, new ChallengePlacementDAO));
        $showClassDataLoader->withAwards(new AwardsRepository($this->eventPostID));
        $registrationCollection = $showClassDataLoader->getCollection()->{ClassIndexModel::class}->{UserRegistrationModel::class};
        $juniorRegistrationCollection = $juniorRegistrationRepository->getAll();
        ModelHydrator::mapExistingCollections($registrationCollection, $juniorRegistrationCollection, JuniorRegistrationModel::class, "id", "registration_id");
        $registrationCollection = $registrationCollection->groupBy("user_name");

        $juniorIndexModel = $classIndexRepository->getJuniorIndexModel();
        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            $juniorRegistrationCount = 0;
            $totalRegistrationCount = 0;
            $prizeMoney = 0.0;
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $showClassModel = $classIndexModel->showClass();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                $viewModel->addClassRegistration($userRegistrationModel->user_name, $classIndexModel->class_index, $showClassModel->class_name, $classIndexModel->age, count($registrationOrder));
                $totalRegistrationCount += count($registrationOrder);

                foreach($registrationOrder as $registrationOrderModel){
                    $entry = $registrationOrderModel->entry();
                    if($entry !== null){
                        foreach($entry->placements() as $placementModel){
                            $prizeMoney += $this->getPrizeMoneyFromPlacement($placementModel, $showOptions);
                        }
                    }
                }

                if($userRegistrationModel->juniorRegistrations() !== null){
                    $juniorRegistrationCount += count($userRegistrationModel->juniorRegistrations);
                }
            }

            if($juniorRegistrationCount > 0 && isset($juniorIndexModel)){
                $viewModel->addClassRegistration($userName, $juniorIndexModel->class_index, "Junior", "AA", $juniorRegistrationCount);
            }

            $viewModel->addTotalRegistrationCount($userName, $totalRegistrationCount + $juniorRegistrationCount);
            $viewModel->addPrizeMoney($userName, $prizeMoney);
        }

        return $viewModel;
    }

    private function getPrizeMoneyFromPlacement(PlacementModel $placementModel, ShowOptionsModel $showOptions): float
    {
        $prizeMoney = 0.0;

        if($placementModel->prize == Prize::STANDARD){
            if($placementModel->placement == 1) $prizeMoney = $showOptions->pm_first_place;
            if($placementModel->placement == 2) $prizeMoney = $showOptions->pm_second_place;
            if($placementModel->placement == 3) $prizeMoney = $showOptions->pm_third_place;
        }

        if($placementModel->prize == Prize::SECTION){
            $prizeMoney = $this->getSectionChallengePrizeMoney($placementModel, $showOptions);
        }

        if($placementModel->prize == Prize::GRANDCHALLENGE){
            $prizeMoney = $this->getGrandChallengePrizeMoney($placementModel, $showOptions);
        }

        return $prizeMoney;
    }

    private function getSectionChallengePrizeMoney(ChallengePlacementModel $placementModel, ShowOptionsModel $showOptions): float
    {
        $prizeMoney = 0.0;

        if($placementModel->award() !== null){
            if($placementModel->award()->award == Award::BIS) $prizeMoney = $showOptions->pm_bisec;
            if($placementModel->award()->award == Award::BOA) $prizeMoney = $showOptions->pm_bosec;
        }

        return $prizeMoney;
    }

    private function getGrandChallengePrizeMoney(ChallengePlacementModel $placementModel, ShowOptionsModel $showOptions): float
    {
        $prizeMoney = 0.0;

        if($placementModel->award() !== null){
            if($placementModel->award()->award == Award::BIS) $prizeMoney = $showOptions->pm_bis;
            if($placementModel->award()->award == Award::BOA) $prizeMoney = $showOptions->pm_boa;
        }

        return $prizeMoney;
    }
}