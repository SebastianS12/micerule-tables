<?php

class PrizeCardsService{
    private PrizeCardsRepository $prizeCardsRepository;

    public function __construct(PrizeCardsRepository $prizeCardsRepository)
    {
        $this->prizeCardsRepository = $prizeCardsRepository;
    }

    // public function prepareViewModel(int $eventPostID, int $locationID): PrizeCardsViewModel
    // {
    //     $viewModel = new PrizeCardsViewModel();

    //     $challengeIndexRepository = new ChallengeIndexRepository($locationID);
    //     $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
    //     $awardRepository = new AwardsRepository($eventPostID);
    //     $awardRepository->getAll();
    //     $challengeIndexModelCollection = $challengeIndexRepository->getAll()->with(["placements", "award"], ["id", "id"], ["indexID", "challengePlacementID"], [$challengePlacementsRepository, $awardRepository]);

    //     $showClassesRepository = new ShowClassesRepository(EventProperties::getEventLocationID($eventPostID));
    //     $classIndexRepository = new ClassIndexRepository(EventProperties::getEventLocationID($eventPostID));
    //     $registrationsRepository = new UserRegistrationsRepository($eventPostID);
    //     $registrationsOrderRepository = new RegistrationOrderRepository($eventPostID);
    //     $entryRepository = new EntryRepository($eventPostID);

    //     $showClassesCollection = $showClassesRepository->getAll()->with(["indices", "registrations", "order", "entry"], ["id", "id", "id", "id"], ["classID", "classIndexID", "registrationID", "registrationOrderID"], [$classIndexRepository, $registrationsRepository, $registrationsOrderRepository, $entryRepository]);

    //     $classPlacementsRepository = new PlacementsRepository($eventPostID, new ClassPlacementDAO());
    //     $showClassesCollection->indices->with(["placements"], ["id"], ["indexID"], [$classPlacementsRepository]);

    //     ModelHydrator::mapExistingCollections($showClassesCollection->indices->registrations->order->entry, "placements", $showClassesCollection->indices->placements, "id", "entryID");
    //     ModelHydrator::mapExistingCollections($showClassesCollection->indices->registrations->order->entry, "placements", $challengeIndexModelCollection->placements, "id", "entryID");

    //     $registrationCountRepository = new RegistrationCountRepository($eventPostID, $locationID);
    //     $registrationCountCollection = $registrationCountRepository->getAll();

    //     ModelHydrator::mapAttribute($showClassesCollection->indices, $registrationCountCollection, "registrationCount", "index", "index_number", "entry_count", 0);
    //     ModelHydrator::mapAttribute($challengeIndexModelCollection, $registrationCountCollection, "registrationCount", "challengeIndex", "index_number", "entry_count", 0);

    //     $judgesRepository = new JudgesRepository($eventPostID);
    //     $judgesSectionsRepository = new JudgesSectionsRepository($eventPostID);
    //     $judgeCollection = $judgesRepository->getAll()->with(['sections'], ['id'], ['judgeID'], [$judgesSectionsRepository]);
    //     $judgeGroupString = JudgeFormatter::getJudgesString($judgeCollection);

    //     ModelHydrator::mapExistingCollections($showClassesCollection, "judgeSection", $judgeCollection->sections, "sectionName", "section");
    //     ModelHydrator::mapExistingCollections($challengeIndexModelCollection, "judgeSection", $judgeCollection->sections, "section", "section");

    //     foreach($showClassesCollection as $entryClassModel){
    //         foreach($entryClassModel->indices as $classIndexModel){
    //             foreach($classIndexModel->placements as $placementModel){
    //                 $registrationModel = $placementModel->registration();
    //                 $entryModel = $placementModel->entry();
    //                 $prizeCardData = [];
    //                 $prizeCardData['placement_id'] = $placementModel->id;
    //                 $prizeCardData['placement'] = $placementModel->placement;
    //                 $prizeCardData['prize'] = $placementModel->prize;
    //                 $prizeCardData['age'] = $classIndexModel->age;
    //                 $prizeCardData['user_name'] = $registrationModel->userName;
    //                 $prizeCardData['class_name'] = $entryClassModel->className;
    //                 $prizeCardData['variety_name'] = $entryModel->varietyName;
    //                 $prizeCardData['pen_number'] = $entryModel->penNumber;
    //                 $prizeCardData['index_number'] = $classIndexModel->index;
    //                 $prizeCardData['section'] = $entryClassModel->sectionName;
    //                 $prizeCardData['printed'] = $placementModel->printed;
    //                 $prizeCardData['judge_name'] = $entryClassModel->judgeSection()->judge()->judgeName;
    //                 $prizeCardData['entry_count'] = $classIndexModel->registrationCount;
    //                 $prizeCardData['award'] = null;

    //                 $prizeCard = PrizeCardFactory::getPrizeCardModel($prizeCardData);
    //                 $viewModel->addPrizeCard($prizeCard);
    //             }
    //         }
    //     }

    //     foreach($challengeIndexModelCollection as $challengeIndexModel){
    //         foreach($challengeIndexModel->placements as $placementModel){
    //             $registrationModel = $placementModel->registration();
    //                 $entryModel = $placementModel->entry();
    //                 $prizeCardData = [];
    //                 $prizeCardData['placement_id'] = $placementModel->id;
    //                 $prizeCardData['placement'] = $placementModel->placement;
    //                 $prizeCardData['prize'] = $placementModel->prize;
    //                 $prizeCardData['age'] = $challengeIndexModel->age;
    //                 $prizeCardData['user_name'] = $registrationModel->userName;
    //                 $prizeCardData['class_name'] = $challengeIndexModel->challengeName;
    //                 $prizeCardData['variety_name'] = $entryModel->varietyName;
    //                 $prizeCardData['pen_number'] = $entryModel->penNumber;
    //                 $prizeCardData['index_number'] = $challengeIndexModel->challengeIndex;
    //                 $prizeCardData['section'] = $challengeIndexModel->section;
    //                 $prizeCardData['printed'] = $placementModel->printed;
    //                 $prizeCardData['judge_name'] = ($challengeIndexModel->challengeName == EventProperties::GRANDCHALLENGE) ? $judgeGroupString : $challengeIndexModel->judgeSection()->judge()->judgeName;
    //                 $prizeCardData['entry_count'] = $challengeIndexModel->registrationCount;
    //                 $prizeCardData['award'] = null;

    //                 $prizeCard = PrizeCardFactory::getPrizeCardModel($prizeCardData);
    //                 $viewModel->addPrizeCard($prizeCard);

    //                 $awardModel = $placementModel->award();
    //                 if(isset($awardModel)){
    //                     $prizeCardData['award'] = $awardModel->award->value;
    //                     $prizeCardData['prize'] = $awardModel->prize;
    //                     echo(var_dump($awardModel->prize));
    //                     $prizeCard = PrizeCardFactory::getPrizeCardModel($prizeCardData);
    //                     $viewModel->addPrizeCard($prizeCard);
    //                 }
    //         }
    //     }

    //     return $viewModel;
    // }

    public function prepareViewModel(int $eventPostID, int $locationID): PrizeCardsViewModel
    {
        $viewModel = new PrizeCardsViewModel();
        
        // Load Repositories and Collections
        $challengeIndexCollection = $this->loadChallengeIndices($eventPostID, $locationID);
        $showClassesCollection = $this->loadShowClasses($eventPostID);
        $registrationCountCollection = $this->loadRegistrationCounts($eventPostID, $locationID);

        // Map registrations and placements
        $this->mapPlacements($showClassesCollection, $challengeIndexCollection, $eventPostID);
        $this->mapRegistrationCounts($showClassesCollection, $challengeIndexCollection, $registrationCountCollection);
        
        // Judges data
        $judgeGroupString = $this->getJudgeGroupString($eventPostID);
        $judgeCollection = $this->loadJudges($eventPostID);

        $this->mapJudgesToClasses($showClassesCollection, $challengeIndexCollection, $judgeCollection);

        // Process Show Classes
        $this->processClassPlacements($viewModel, $showClassesCollection);

        // Process Challenge Placements
        $this->processChallengePlacements($viewModel, $challengeIndexCollection, $judgeGroupString);

        return $viewModel;
    }

    private function loadChallengeIndices(int $eventPostID, int $locationID): Collection
    {
        $challengeIndexRepository = new ChallengeIndexRepository($locationID);
        $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
        $awardRepository = new AwardsRepository($eventPostID);
        $awardRepository->getAll();

        return $challengeIndexRepository->getAll()->with(
            [ChallengePlacementModel::class, AwardModel::class],
            ["id", "id"],
            ["index_id", "challenge_placement_id"],
            [$challengePlacementsRepository, $awardRepository]
        );
    }

    private function loadShowClasses(int $eventPostID): Collection
    {
        $locationID = LocationHelper::getIDFromEventPostID($eventPostID);
        $showClassesRepository = new ShowClassesRepository($locationID);
        $classIndexRepository = new ClassIndexRepository($locationID);
        $registrationsRepository = new UserRegistrationsRepository($eventPostID);
        $registrationsOrderRepository = new RegistrationOrderRepository($eventPostID);
        $entryRepository = new EntryRepository($eventPostID);

        return $showClassesRepository->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id", "id", "id"],
            ["class_id", "class_index_id", "registration_id", "registration_order_id"],
            [$classIndexRepository, $registrationsRepository, $registrationsOrderRepository, $entryRepository]
        );
    }

    private function loadRegistrationCounts(int $eventPostID, int $locationID): Collection
    {
        $registrationCountRepository = new RegistrationCountRepository($eventPostID, $locationID);
        return $registrationCountRepository->getAll();
    }

    private function mapPlacements(Collection $showClassesCollection, Collection $challengeIndexCollection, int $eventPostID): void
    {
        // Class Placements
        $classPlacementsRepository = new PlacementsRepository($eventPostID, new ClassPlacementDAO());
        $showClassesCollection->{ClassIndexModel::class}->with(
            [ClassPlacementModel::class], 
            ["id"], 
            ["index_id"], 
            [$classPlacementsRepository]
        );

        // Map class placements to entries
        ModelHydrator::mapExistingCollections(
            $showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class}->{EntryModel::class},
            $showClassesCollection->{ClassIndexModel::class}->{ClassPlacementModel::class}, 
            ClassPlacementModel::class,
            "id", 
            "entry_id"
        );

        // Challenge Placements
        ModelHydrator::mapExistingCollections(
            $showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class}->{EntryModel::class}, 
            $challengeIndexCollection->{ChallengePlacementModel::class}, 
            ChallengePlacementModel::class,
            "id", 
            "entry_id"
        );
    }

    private function mapRegistrationCounts(Collection $showClassesCollection, Collection $challengeIndexCollection, Collection $registrationCountCollection): void
    {
        ModelHydrator::mapAttribute($showClassesCollection->{ClassIndexModel::class}, $registrationCountCollection, "registrationCount", "class_index", "index_number", "entry_count", 0);
        ModelHydrator::mapAttribute($challengeIndexCollection, $registrationCountCollection, "registrationCount", "challenge_index", "index_number", "entry_count", 0);
    }

    private function loadJudges(int $eventPostID): Collection
    {
        $judgesRepository = new JudgesRepository($eventPostID);
        $judgesSectionsRepository = new JudgesSectionsRepository($eventPostID);
        return $judgesRepository->getAll()->with([JudgeSectionModel::class], ['id'], ['judge_id'], [$judgesSectionsRepository]);
    }

    private function getJudgeGroupString(int $eventPostID): string
    {
        $judgesCollection = $this->loadJudges($eventPostID);
        return JudgeFormatter::getJudgesString($judgesCollection);
    }

    private function mapJudgesToClasses(Collection $showClassesCollection, Collection $challengeIndexCollection, Collection $judgeCollection): void
    {
        // Map judges to show classes
        ModelHydrator::mapExistingCollections(
            $showClassesCollection, 
            $judgeCollection->{JudgeSectionModel::class}, 
            JudgeSectionModel::class,
            "section", 
            "section"
        );

        // Map judges to challenge indices
        ModelHydrator::mapExistingCollections(
            $challengeIndexCollection, 
            $judgeCollection->{JudgeSectionModel::class}, 
            JudgeSectionModel::class,
            "section", 
            "section"
        );
    }

    private function processClassPlacements(PrizeCardsViewModel $viewModel, Collection $showClassesCollection): void
    {
        foreach ($showClassesCollection as $entryClassModel) {
            foreach ($entryClassModel->classIndices as $classIndexModel) {
                foreach ($classIndexModel->placements as $placementModel) {
                    $judgeName = JudgeFormatter::getJudgeName($entryClassModel); 
                    $this->addPrizeCardFromPlacement(
                        $viewModel,
                        $placementModel,
                        $classIndexModel->age,
                        $classIndexModel->class_index,
                        $entryClassModel->class_name,
                        $entryClassModel->section,
                        $judgeName,
                        $classIndexModel->registrationCount,
                    );
                }
            }
        }
    }

    private function processChallengePlacements(PrizeCardsViewModel $viewModel, Collection $challengeIndexCollection, string $judgeGroupString): void
    {
        foreach ($challengeIndexCollection as $challengeIndexModel) {
            foreach ($challengeIndexModel->placements as $placementModel) {
                $judge = ($challengeIndexModel->challenge_name == EventProperties::GRANDCHALLENGE) ? $judgeGroupString : JudgeFormatter::getJudgeName($challengeIndexModel);
                $this->addPrizeCardFromPlacement(
                    $viewModel,
                    $placementModel,
                    $challengeIndexModel->age,
                    $challengeIndexModel->challenge_index,
                    $challengeIndexModel->challenge_name,
                    $challengeIndexModel->section,
                    $judge,
                    $challengeIndexModel->registrationCount,
                );
            }
        }
    }

    private function addPrizeCardFromPlacement(
        PrizeCardsViewModel $viewModel,
        PlacementModel $placementModel,
        string $age,
        int $index,
        string $className,
        string $section,
        string $judge,
        int $registrationCount,
    ): void {
        $registrationModel = $placementModel->registration;
        $entryModel = $placementModel->entry;

        $prizeCardData = [
            'placement_id' => $placementModel->id,
            'placement' => $placementModel->placement,
            'prize' => $placementModel->prize,
            'age' => $age,
            'user_name' => $registrationModel->user_name,
            'class_name' => $className,
            'variety_name' => $entryModel->variety_name,
            'pen_number' => $entryModel->pen_number,
            'index_number' => $index,
            'section' => $section,
            'printed' => $placementModel->printed,
            'judge_name' => $judge,
            'entry_count' => $registrationCount,
            'award' => null
        ];

        $prizeCard = PrizeCardFactory::getPrizeCardModel($prizeCardData);
        $viewModel->addPrizeCard($prizeCard);
        
        if (isset($placementModel->{AwardModel::class}) && $placementModel->award !== null) {
            $prizeCardData['placement_id'] = $placementModel->award->id;
            $prizeCardData['award'] = $placementModel->award->award->value;
            $prizeCardData['prize'] = $placementModel->award->prize;
            $prizeCardData['printed'] = $placementModel->award->printed;
            $prizeCard = PrizeCardFactory::getPrizeCardModel($prizeCardData);
            $viewModel->addPrizeCard($prizeCard);
        }
    }

    public function printAll(array $prizeCardsToPrint){
        foreach($prizeCardsToPrint as $prizeCardData){
            $printDAO = PrintDAOFactory::getPrintDAO($prizeCardData['prize']);
            $this->prizeCardsRepository->updatePrinted((int)$prizeCardData['placementID'], true, $printDAO);
        }
    }

    public function moveToUnprinted(int $placementID, int $prizeID){
        $printDAO = PrintDAOFactory::getPrintDAO($prizeID);
        $this->prizeCardsRepository->updatePrinted($placementID, false, $printDAO);
    }
}