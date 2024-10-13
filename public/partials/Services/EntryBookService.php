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

    public function prepareViewModel(int $eventPostID): EntryBookViewModel{
        $viewModel = new EntryBookViewModel();

        $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
        $challengeIndexModelCollection = $this->challengeIndexRepository->getAll()->with(["placements"], ["id"], ["indexID"], [$challengePlacementsRepository]);

        foreach($challengeIndexModelCollection as $challengeIndexModel){
            if($challengeIndexModel->challengeName == EventProperties::GRANDCHALLENGE){
                $viewModel->grandChallengeData[$challengeIndexModel->age] = $challengeIndexModel;
            }else{
                $viewModel->challengeData[$challengeIndexModel->challengeName][$challengeIndexModel->age] = $challengeIndexModel;
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

        //pass placements to row service

        //TODO: Filter optional classes
        $showClassesCollection = $showClassesCollection->groupBy("sectionName");
        $classData = array();
        foreach($showClassesCollection as $sectionName => $showClassModelCollection){
            $classData[$sectionName] = array();
            foreach($showClassModelCollection as $showClassModel){
                $className = $showClassModel->className;
                $classData[$sectionName][$className] = array();
                foreach($showClassModel->indices as $classIndexModel){
                    $age = $classIndexModel->age;
                    $classData[$sectionName][$className][$age] = array();
                    $classData[$sectionName][$className][$age]['classIndex'] = $classIndexModel->index;
                    $classData[$sectionName][$className][$age]['entries'] = array();

                    $sectionIndexModel = $viewModel->challengeData[EventProperties::getChallengeName($sectionName)][$age];
                    $grandChallengeIndexModel = $viewModel->grandChallengeData[$age];
                    foreach($classIndexModel->registrations as $userRegistration){
                        foreach($userRegistration->order as $registrationOrder){
                            $entry = $registrationOrder->entry();
                            if(isset($entry)){
                                $rowPlacementData = new RowPlacementData($classIndexModel->id, $classIndexModel->placements, $sectionIndexModel->id, $sectionIndexModel->placements, $grandChallengeIndexModel->id, $grandChallengeIndexModel->placements);
                                $classData[$sectionName][$className][$age]['entries'][] = $entryRowService->prepareRowData($entry, $userRegistration->userName, $rowPlacementData, $age);
                                echo(var_dump($entry->belongsToOneThrough([RegistrationOrderModel::class, UserRegistrationModel::class, ClassIndexModel::class, EntryClassModel::class])));
                            }
                        }
                    }

                }
            }
        }
        $viewModel->classData = $classData;

        return $viewModel;
    }
}