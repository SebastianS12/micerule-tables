<?php

class EntryBookService{
    private ChallengeIndexRepository $challengeIndexRepository;

    public function __construct(ChallengeIndexRepository $challengeIndexRepository)
    {
        $this->challengeIndexRepository = $challengeIndexRepository;
    }

    public function prepareViewModel(int $eventPostID, int $locationID, string $eventDeadline): EntryBookViewModel{
        $viewModel = new EntryBookViewModel();
        $viewModel->pastDeadline = time() > strtotime($eventDeadline);

        $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
        $awardRepository = new AwardsRepository($eventPostID);
        $awardRepository->getAll();
        $challengeIndexModelCollection = $this->challengeIndexRepository->getAll()->with([ChallengePlacementModel::class, AwardModel::class], ["id", "id"], ["index_id", "challenge_placement_id"], [$challengePlacementsRepository, $awardRepository]);

        $sectionchallengeIndexModels = array();
        $grandChallengeIndexModels = array();
        foreach($challengeIndexModelCollection as $challengeIndexModel){
            if($challengeIndexModel->challenge_name == EventProperties::GRANDCHALLENGE){
                $grandChallengeIndexModels[$challengeIndexModel->age] = $challengeIndexModel;
            }else{
                $sectionchallengeIndexModels[$challengeIndexModel->section][$challengeIndexModel->age] = $challengeIndexModel;
            }
        }

        $showClassesRepository = new ShowClassesRepository(EventProperties::getEventLocationID($eventPostID));
        $classIndexRepository = new ClassIndexRepository(EventProperties::getEventLocationID($eventPostID));
        $registrationsRepository = new UserRegistrationsRepository($eventPostID);
        $registrationsOrderRepository = new RegistrationOrderRepository($eventPostID);
        $entryRepository = new EntryRepository($eventPostID);
        $entryRowService = new EntryRowService(new PlacementsRowService(), new BreedsService(new BreedsRepository(), new ShowClassesRepository($locationID)));

        $showClassesCollection = $showClassesRepository->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id", "id", "id"], 
            ["class_id", "class_index_id", "registration_id", "registration_order_id"], 
            [$classIndexRepository, $registrationsRepository, $registrationsOrderRepository, $entryRepository]);

        $classPlacementsRepository = new PlacementsRepository($eventPostID, new ClassPlacementDAO());
        $showClassesCollection->{ClassIndexModel::class}->with([ClassPlacementModel::class], ["id"], ["index_id"], [$classPlacementsRepository]);

        ModelHydrator::mapExistingCollections($showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class}->{EntryModel::class}, $showClassesCollection->{ClassIndexModel::class}->{ClassPlacementModel::class}, ClassPlacementModel::class, "id", "entry_id");
        ModelHydrator::mapExistingCollections($showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class}->{EntryModel::class}, $challengeIndexModelCollection->{ChallengePlacementModel::class}, ChallengePlacementModel::class, "id", "entry_id");

        $showClassesCollection = $showClassesCollection->groupBy("section");
        $classData = array();
        foreach(EventProperties::SECTIONNAMES as $sectionName){
            $sectionName = strtolower($sectionName);
            $classData[$sectionName] = array();
            foreach($showClassesCollection[$sectionName] as $showClassModel){
                $className = $showClassModel->class_name;
                $classData[$sectionName][$className] = array();
                foreach($showClassModel->classIndices as $classIndexModel){
                    $age = $classIndexModel->age;
                    $classData[$sectionName][$className][$age] = array();
                    $classData[$sectionName][$className][$age]['classIndex'] = $classIndexModel->class_index;
                    $classData[$sectionName][$className][$age]['entries'] = array();

                    $sectionIndexModel = $sectionchallengeIndexModels[$sectionName][$age];
                    $grandChallengeIndexModel = $grandChallengeIndexModels[$age];
                    foreach($classIndexModel->registrations as $userRegistration){
                        foreach($userRegistration->registrationOrder as $registrationOrder){
                            if(isset($registrationOrder->{EntryModel::class})){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, $sectionIndexModel->id, $sectionIndexModel->placements, $grandChallengeIndexModel->id, $grandChallengeIndexModel->placements);
                                $classData[$sectionName][$className][$age]['entries'][] = $entryRowService->prepareRowData($registrationOrder->entry, $userRegistration->user_name, $rowPlacementData, $age, $sectionName, $viewModel->pastDeadline);
                            }
                        }
                    }

                }
            }
        }
        $viewModel->classData = $classData;

        $juniorRegistrationRepository = new JuniorRegistrationRepository($eventPostID);
        $juniorRegistrationCollection = $juniorRegistrationRepository->getAll();
        ModelHydrator::mapExistingCollections($showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class}, $juniorRegistrationCollection, JuniorRegistrationModel::class,"id", "registration_order_id");

        $optionalClassData = array();
        $optionalClassRowService = new OptionalClassRowService(new PlacementsRowService());
        $juniorClassRowService = new JuniorRowService($optionalClassRowService);
        $section = "optional";
        foreach($showClassesCollection[$section] as $showClassModel){
            $className = $showClassModel->class_name;
            $optionalClassData[$className] = array();
            foreach($showClassModel->classIndices as $classIndexModel){
                $age = $classIndexModel->age;
                $optionalClassData[$className] = array();
                $optionalClassData[$className]['classIndex'] = $classIndexModel->class_index;
                $optionalClassData[$className]['entries'] = array();

                if($className != "Junior"){
                    foreach($classIndexModel->registrations as $userRegistration){
                        foreach($userRegistration->order as $registrationOrder){
                            if(isset($registrationOrder->{EntryModel::class})){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, 0, new Collection(), 0, new Collection());
                                $optionalClassData[$className]['entries'][] = $optionalClassRowService->prepareRowData($registrationOrder->entry, $userRegistration->user_name, $rowPlacementData, $age, $section, $viewModel->pastDeadline);
                            }
                        }
                    }
                }else{
                    foreach($juniorRegistrationCollection as $juniorRegistration){
                        if(isset($juniorRegistration->{RegistrationOrderModel::class})){
                            if(isset($juniorRegistration->order->{EntryModel::class}) && isset($juniorRegistration->order->{UserRegistrationModel::class})){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, 0, new Collection(), 0, new Collection());
                                $optionalClassData[$className]['entries'][] = $juniorClassRowService->prepareRowData($registrationOrder->entry, $registrationOrder->registration->user_name, $rowPlacementData, $age, $section, $viewModel->pastDeadline);
                            }
                        }
                    }
                }
            }
        }
        $viewModel->optionalClassData = $optionalClassData;

        $challengeRowService = new ChallengeRowService($eventPostID);
        foreach($sectionchallengeIndexModels as $section => $indexModels){
            $adIndexModel = $indexModels['Ad'];
            $u8IndexModel = $indexModels['U8'];
            $viewModel->challengeData[$section] = $challengeRowService->prepareChallengeRowData($adIndexModel, $u8IndexModel, $adIndexModel->placements(), $u8IndexModel->placements(), Prize::SECTION);
        }

        $adIndexModel = $grandChallengeIndexModels['Ad'];
        $u8IndexModel = $grandChallengeIndexModels['U8'];
        $viewModel->grandChallengeData = $challengeRowService->prepareChallengeRowData($adIndexModel, $u8IndexModel, $adIndexModel->placements(), $u8IndexModel->placements(), Prize::GRANDCHALLENGE);

        return $viewModel;
    }

    public function moveEntry(int $entryID, int $newClassIndexID, EntryRepository $entryRepository){
        $entry = $entryRepository->getByID($entryID);
        //get entry by id + lazy loading order + registration
        //get classIndexModel by id + lazy loading registrations
        // if registrations contains user -> add new registration with entry
        // else create new registration and add entry
    }
}