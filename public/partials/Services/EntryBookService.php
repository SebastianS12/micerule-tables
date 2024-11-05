<?php

class EntryBookService{
    public function prepareViewModel(int $eventPostID, int $locationID): EntryBookViewModel{
        $viewModel = new EntryBookViewModel();
        $eventDeadline = EventDeadlineService::getEventDeadline($eventPostID);
        $viewModel->pastDeadline = time() > strtotime($eventDeadline);

        $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
        $challengeIndexRepository = new ChallengeIndexRepository($locationID);
        $awardRepository = new AwardsRepository($eventPostID);
        $awardRepository->getAll();
        $challengeIndexModelCollection = $challengeIndexRepository->getAll()->with([ChallengePlacementModel::class, AwardModel::class], ["id", "id"], ["index_id", "challenge_placement_id"], [$challengePlacementsRepository, $awardRepository]);

        $sectionchallengeIndexModels = array();
        $grandChallengeIndexModels = array();
        foreach($challengeIndexModelCollection as $challengeIndexModel){
            if($challengeIndexModel->challenge_name == EventProperties::GRANDCHALLENGE){
                $grandChallengeIndexModels[$challengeIndexModel->age] = $challengeIndexModel;
            }else{
                $sectionchallengeIndexModels[$challengeIndexModel->section][$challengeIndexModel->age] = $challengeIndexModel;
            }
        }

        $showClassesRepository = new ShowClassesRepository(LocationHelper::getIDFromEventPostID($eventPostID));
        $classIndexRepository = new ClassIndexRepository(LocationHelper::getIDFromEventPostID($eventPostID));
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
            foreach($showClassModel->classIndices() as $classIndexModel){
                $age = $classIndexModel->age;
                $optionalClassData[$className] = array();
                $optionalClassData[$className]['classIndex'] = $classIndexModel->class_index;
                $optionalClassData[$className]['entries'] = array();

                if($className != "Junior"){
                    foreach($classIndexModel->registrations() as $userRegistration){
                        foreach($userRegistration->registrationOrder() as $registrationOrder){
                            if(isset($registrationOrder->{EntryModel::class})){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, 0, new Collection(), 0, new Collection());
                                $optionalClassData[$className]['entries'][] = $optionalClassRowService->prepareRowData($registrationOrder->entry, $userRegistration->user_name, $rowPlacementData, $age, $section, $viewModel->pastDeadline);
                            }
                        }
                    }
                }else{
                    foreach($juniorRegistrationCollection as $juniorRegistration){
                        if(isset($juniorRegistration->{RegistrationOrderModel::class})){
                            if(isset($juniorRegistration->registrationOrder()->{EntryModel::class}) && isset($juniorRegistration->registrationOrder()->{UserRegistrationModel::class})){
                                $registrationOrder = $juniorRegistration->registrationOrder();
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, 0, new Collection(), 0, new Collection());
                                $optionalClassData[$className]['entries'][] = $juniorClassRowService->prepareRowData($registrationOrder->entry, $registrationOrder->registration()->user_name, $rowPlacementData, $age, $section, $viewModel->pastDeadline);
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

    public function moveEntry(int $entryID, int $newClassIndexID, EntryRepository $entryRepository, ClassIndexRepository $classIndexRepository, UserRegistrationsRepository $userRegistrationsRepository, RegistrationOrderRepository $registrationOrderRepository): void
    {
        $entry = $entryRepository->getByID($entryID);
        $registrationOrderModel = $entry->registrationOrder();
        $registrationModel = $registrationOrderModel->registration();

        $classIndexModel = $classIndexRepository->getByID($newClassIndexID);
        $registrations = $classIndexModel->registrations();
        $userRegistration = $registrations->get("user_name", $registrationModel->user_name);
        $userRegistrationID = ($userRegistration !== null) ? $userRegistration->id : $userRegistrationsRepository->addRegistration($registrationModel->event_post_id, $registrationModel->user_name, $newClassIndexID);
        $registrationOrderID = $registrationOrderRepository->addRegistration($userRegistrationID, current_time('mysql'));
        $entry->registration_order_id = $registrationOrderID;
        $entry->moved = true;
        $entryRepository->saveEntry($entry);

        if(count($registrationModel->registrationOrder()) > 1){
            $registrationOrderRepository->removeRegistration($registrationOrderModel->id);
        }else{
            $userRegistrationsRepository->removeRegistration($registrationModel->id);
        }
    }

    public function addEntry(int $eventPostID, int $classIndexID, string $userName, ClassIndexRepository $classIndexRepository, UserRegistrationsRepository $userRegistrationsRepository, RegistrationOrderRepository $registrationOrderRepository, EntryRepository $entryRepository): void
    {
        $classIndexModel = $classIndexRepository->getByID($classIndexID);
        $registrations = $classIndexModel->registrations();
        $nextPenNumberModel = $classIndexModel->nextPenNumber();
        if($nextPenNumberModel !== null){
            $userRegistration = $registrations->get("user_name", $userName);
            $userRegistrationID = ($userRegistration !== null) ? $userRegistration->id : $userRegistrationsRepository->addRegistration($eventPostID, $userName, $classIndexID);
            $registrationOrderID = $registrationOrderRepository->addRegistration($userRegistrationID, current_time('mysql'));
            $entryModel = EntryModel::create($registrationOrderID, $nextPenNumberModel->next_pen_number, $classIndexModel->showClass()->class_name, false, true, false);
            $entryRepository->saveEntry($entryModel);

            if(JuniorHelper::addJunior(LocationHelper::getIDFromEventPostID($eventPostID), $userName, new ShowOptionsService())){
                $registrationOrderRepository->addJuniorRegistration($registrationOrderID, $userRegistrationID);
            }
        }
    }

    public function deleteEntry(int $entryID, EntryRepository $entryRepository, RegistrationOrderRepository $registrationOrderRepository, UserRegistrationsRepository $userRegistrationsRepository, JuniorRegistrationRepository $juniorRegistrationRepository): void
    {
        $entry = $entryRepository->getByID($entryID);
        $registrationOrderModel = $entry->registrationOrder();
        $registrationModel = $registrationOrderModel->registration();
        $juniorRegistration = $registrationOrderModel->juniorRegistration();

        if(isset($juniorRegistration)){
            $juniorRegistrationRepository->remove($juniorRegistration->id);
        }

        $entryRepository->deleteEntry($entry->id);
        if(count($registrationModel->registrationOrder()) > 1){
            $registrationOrderRepository->removeRegistration($registrationOrderModel->id);
        }else{
            $userRegistrationsRepository->removeRegistration($registrationModel->id);
        }
    }
}