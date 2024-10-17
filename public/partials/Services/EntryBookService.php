<?php

class EntryBookService{
    private ChallengeIndexRepository $challengeIndexRepository;
    private PlacementsRepository $challengePlacementsRepository;
    private ChallengeRowService $challengeRowService;
    private ShowSectionRepository $showSectionRepository;

    public function __construct(ChallengeIndexRepository $challengeIndexRepository)
    {
        $this->challengeIndexRepository = $challengeIndexRepository;
    }

    public function prepareViewModel(int $eventPostID, string $eventDeadline): EntryBookViewModel{
        $viewModel = new EntryBookViewModel();
        $viewModel->pastDeadline = time() > strtotime($eventDeadline);

        $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
        $awardRepository = new AwardsRepository($eventPostID);
        $awardRepository->getAll();
        $challengeIndexModelCollection = $this->challengeIndexRepository->getAll()->with(["placements", "award"], ["id", "id"], ["indexID", "challengePlacementID"], [$challengePlacementsRepository, $awardRepository]);

        $sectionchallengeIndexModels = array();
        $grandChallengeIndexModels = array();
        foreach($challengeIndexModelCollection as $challengeIndexModel){
            if($challengeIndexModel->challengeName == EventProperties::GRANDCHALLENGE){
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
        $entryRowService = new EntryRowService(new PlacementsRowService());

        $showClassesCollection = $showClassesRepository->getAll()->with(["indices", "registrations", "order", "entry"], ["id", "id", "id", "id"], ["classID", "classIndexID", "registrationID", "registrationOrderID"], [$classIndexRepository, $registrationsRepository, $registrationsOrderRepository, $entryRepository]);

        $classPlacementsRepository = new PlacementsRepository($eventPostID, new ClassPlacementDAO());
        $showClassesCollection->indices->with(["placements"], ["id"], ["indexID"], [$classPlacementsRepository]);

        ModelHydrator::mapExistingCollections($showClassesCollection->indices->registrations->order->entry, "placements", $showClassesCollection->indices->placements, "id", "entryID");
        ModelHydrator::mapExistingCollections($showClassesCollection->indices->registrations->order->entry, "placements", $challengeIndexModelCollection->placements, "id", "entryID");

        $showClassesCollection = $showClassesCollection->groupBy("sectionName");
        $classData = array();
        foreach(EventProperties::SECTIONNAMES as $sectionName){
            $sectionName = strtolower($sectionName);
            $classData[$sectionName] = array();
            foreach($showClassesCollection[$sectionName] as $showClassModel){
                $className = $showClassModel->className;
                $classData[$sectionName][$className] = array();
                foreach($showClassModel->indices as $classIndexModel){
                    $age = $classIndexModel->age;
                    $classData[$sectionName][$className][$age] = array();
                    $classData[$sectionName][$className][$age]['classIndex'] = $classIndexModel->index;
                    $classData[$sectionName][$className][$age]['entries'] = array();

                    $sectionIndexModel = $sectionchallengeIndexModels[$sectionName][$age];
                    $grandChallengeIndexModel = $grandChallengeIndexModels[$age];
                    foreach($classIndexModel->registrations as $userRegistration){
                        foreach($userRegistration->order as $registrationOrder){
                            $entry = $registrationOrder->entry();
                            if(isset($entry)){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, $sectionIndexModel->id, $sectionIndexModel->placements, $grandChallengeIndexModel->id, $grandChallengeIndexModel->placements);
                                $classData[$sectionName][$className][$age]['entries'][] = $entryRowService->prepareRowData($entry, $userRegistration->userName, $rowPlacementData, $age, $viewModel->pastDeadline);
                            }
                        }
                    }

                }
            }
        }
        $viewModel->classData = $classData;

        $juniorRegistrationRepository = new JuniorRegistrationRepository($eventPostID);
        $juniorRegistrationCollection = $juniorRegistrationRepository->getAll();
        ModelHydrator::mapExistingCollections($showClassesCollection->indices->registrations->order, "junior", $juniorRegistrationCollection, "id", "registrationOrderID");

        $optionalClassData = array();
        $optionalClassRowService = new OptionalClassRowService(new PlacementsRowService());
        $juniorClassRowService = new JuniorRowService($optionalClassRowService);
        foreach($showClassesCollection['optional'] as $showClassModel){
            $className = $showClassModel->className;
            $optionalClassData[$className] = array();
            foreach($showClassModel->indices as $classIndexModel){
                $age = $classIndexModel->age;
                $optionalClassData[$className] = array();
                $optionalClassData[$className]['classIndex'] = $classIndexModel->index;
                $optionalClassData[$className]['entries'] = array();

                if($className != "Junior"){
                    foreach($classIndexModel->registrations as $userRegistration){
                        foreach($userRegistration->order as $registrationOrder){
                            $entry = $registrationOrder->entry();
                            if(isset($entry)){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, 0, new Collection(), 0, new Collection());
                                $optionalClassData[$className]['entries'][] = $optionalClassRowService->prepareRowData($entry, $userRegistration->userName, $rowPlacementData, $age, $viewModel->pastDeadline);
                            }
                        }
                    }
                }else{
                    foreach($juniorRegistrationCollection as $juniorRegistration){
                        $registrationOrder = $juniorRegistration->order();
                        if(isset($registrationOrder)){
                            $entry = $registrationOrder->entry();
                            $registration = $registrationOrder->registration();
                            if(isset($entry) && isset($registration)){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, 0, new Collection(), 0, new Collection());
                                $optionalClassData[$className]['entries'][] = $juniorClassRowService->prepareRowData($entry, $registration->userName, $rowPlacementData, $age, $viewModel->pastDeadline);
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
}