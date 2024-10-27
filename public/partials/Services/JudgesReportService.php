<?php

class JudgesReportService{

    public function prepareViewModel(int $eventPostID, int $locationID): JudgesReportViewModel
    {
        $eventMetaData = EventProperties::getEventMetaData($eventPostID);
        $showName = $eventMetaData['event_name'];
        $date = date("d F Y", strtotime($eventMetaData['event_start_date']));
        $userName =  wp_get_current_user()->display_name;
        $canAdmin = current_user_can('administrator');
        $viewModel = new JudgesReportViewModel($showName, $date, $userName, $canAdmin);

        $judgeCollection = $this->loadJudges($eventPostID);
        $generalCommentRepository = new GeneralCommentRepository($eventPostID);
        $judgeCollection->with([GeneralComment::class], ['id'], ['judge_id'], [$generalCommentRepository]);

        $challengeIndexCollection = $this->loadChallengeIndices($eventPostID, $locationID);
        $showClassesCollection = $this->loadShowClasses($eventPostID);
        $registrationCountCollection = $this->loadRegistrationCounts($eventPostID, $locationID);

        // Map registrations and placements
        $this->mapPlacements($showClassesCollection, $challengeIndexCollection, $eventPostID);
        $this->mapRegistrationCounts($showClassesCollection, $challengeIndexCollection, $registrationCountCollection);
        $this->mapJudgesToClasses($showClassesCollection, $challengeIndexCollection, $judgeCollection);

        $classCommentsRepository = new ClassCommentsRepository($eventPostID);
        $showClassesCollection->indices->with([ClassComment::class], ['id'], ['class_index_id'], [$classCommentsRepository]);

        $placementReportsRepository = new PlacementReportsRepository($eventPostID);
        $showClassesCollection->indices->placements->with([PlacementReport::class], ['id'], ['placement_id'], [$placementReportsRepository]);

        foreach($judgeCollection as $judgeModel){
            $commentID = (isset($judgeModel->{GeneralComment::class}) && $judgeModel->comment !== null) ? $judgeModel->comment->id : null;
            $comment = (isset($judgeModel->{GeneralComment::class}) && $judgeModel->comment !== null) ? $judgeModel->comment->comment : "";
            $viewModel->addJudgeComment($judgeModel->judge_name, $judgeModel->id, $commentID, $comment);

            foreach($judgeModel->sections() as $judgeSectionModel){
                $viewModel->addJudgeSection($judgeModel->judge_name, $judgeSectionModel->section);
            }
        }

        foreach($showClassesCollection->whereNot("section", "optional") as $entryClassModel){
            foreach($entryClassModel->classIndices as $classIndexModel){
                $commentID = (isset($classIndexModel->{ClassComment::class}) && $classIndexModel->comment !== null) ? $classIndexModel->comment->id : null;
                $comment = (isset($classIndexModel->{ClassComment::class}) && $classIndexModel->comment !== null) ? $classIndexModel->comment->comment : "";
                $judge = JudgeFormatter::getJudgeName($entryClassModel);
                $viewModel->addClassReport($classIndexModel->id, $commentID, $judge, $classIndexModel->class_index, $comment, $entryClassModel->section, $entryClassModel->class_name, $classIndexModel->age, $classIndexModel->registrationCount);
                foreach($classIndexModel->placements as $placementModel){
                    $placementReportID = (isset($placementModel->{PlacementReport::class}) && $placementModel->report !== null) ? $placementModel->report->id : null;
                    $gender = (isset($placementModel->{PlacementReport::class}) && $placementModel->report !== null) ? $placementModel->report->gender : null;
                    $comment = (isset($placementModel->{PlacementReport::class}) && $placementModel->report !== null) ? $placementModel->report->comment : "";
                    $userName = $placementModel->registration->user_name;
                    $viewModel->addPlacementReport($placementReportID, $judge, $entryClassModel->section, $classIndexModel->class_index, $placementModel->id, $placementModel->placement, $userName, $gender, $comment);
                }
            }
        }

        foreach($challengeIndexCollection->whereNot("challenge_name", EventProperties::GRANDCHALLENGE) as $challengeIndexModel){
            $judge = JudgeFormatter::getJudgeName($challengeIndexModel);
            $viewModel->addChallengeReport($judge, $challengeIndexModel->challenge_index, $challengeIndexModel->section, $challengeIndexModel->challenge_name, $challengeIndexModel->age, $challengeIndexModel->registrationCount);
            foreach($challengeIndexModel->placements as $placementModel){
                $fancierName = $placementModel->registration->user_name;
                $varietyName = $placementModel->entry->variety_name;
                $viewModel->addChallengePlacementReport($judge, $challengeIndexModel->section, $challengeIndexModel->challenge_index, $fancierName, $placementModel->placement, $varietyName);
            }
        }

        foreach($showClassesCollection->where("section", "optional")->whereNot("class_name", "Junior") as $entryClassModel){
            foreach($entryClassModel->classIndices as $classIndexModel){
                $commentID = (isset($classIndexModel->{ClassComment::class}) && $classIndexModel->comment !== null) ? $classIndexModel->comment->id : null;
                $comment = (isset($classIndexModel->{ClassComment::class}) && $classIndexModel->comment !== null) ? $classIndexModel->comment->comment : "";
                $viewModel->addOptionalClassReport($classIndexModel->id, $commentID, $classIndexModel->class_index, $comment, $entryClassModel->section, $entryClassModel->class_name, $classIndexModel->age, $classIndexModel->registrationCount);
                foreach($classIndexModel->placements as $placementModel){
                    $placementReportModel = $placementModel->report();
                    $placementReportID = (isset($placementModel->{PlacementReport::class}) && $placementModel->report !== null) ? $placementModel->report->id : null;
                    $gender = (isset($placementModel->{PlacementReport::class}) && $placementModel->report !== null) ? $placementModel->report->gender : null;
                    $comment = (isset($placementModel->{PlacementReport::class}) && $placementModel->report !== null) ? $placementModel->report->comment : "";
                    $userName = $placementModel->registration->user_name;
                    $viewModel->addOptionalClassPlacementReport($placementReportID, $entryClassModel->section, $classIndexModel->class_index, $placementModel->id, $placementModel->placement, $userName, $gender, $comment);
                }
            }
        }

        return $viewModel;
    }

    private function loadJudges(int $eventPostID): Collection
    {
        $judgesRepository = new JudgesRepository($eventPostID);
        $judgesSectionsRepository = new JudgesSectionsRepository($eventPostID);
        return $judgesRepository->getAll()->with([JudgeSectionModel::class], ['id'], ['judge_id'], [$judgesSectionsRepository]);
    }

    private function loadChallengeIndices(int $eventPostID, int $locationID): Collection
    {
        $challengeIndexRepository = new ChallengeIndexRepository($locationID);
        $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
        $awardRepository = new AwardsRepository($eventPostID);

        return $challengeIndexRepository->getAll()->with(
            [ChallengePlacementModel::class, AwardModel::class],
            ["id", "id"],
            ["index_id", "challenge_placement_id"],
            [$challengePlacementsRepository, $awardRepository]
        );
    }

    private function loadShowClasses(int $eventPostID): Collection
    {
        $locationID = EventProperties::getEventLocationID($eventPostID);
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

    public function submitClassComment(?int $commentID, int $eventPostID, ?string $comment, int $indexID, ClassCommentsRepository $classCommentsRepository){
        $classCommentModel = (isset($commentID)) ? ClassComment::createWithID($commentID, $eventPostID, $indexID, $comment) : ClassComment::create($eventPostID, $indexID, $comment);
        $classCommentsRepository->save($classCommentModel);
    }

    public function submitPlacementReports(?array $placementReports, int $eventPostID, int $indexID, PlacementReportsRepository $placementReportsRepository){
        foreach($placementReports as $placementReport){
            $reportID = isset($placementReport->id) && $placementReport->id !== '' ? intval($placementReport->id) : null;
            $gender = ($placementReport->buckChecked) ? "Buck" : "Doe";
            
            $placementReportModel = isset($reportID) ? PlacementReport::createWithID($reportID, $eventPostID, $indexID, $gender, $placementReport->comment, $placementReport->placement_id): PlacementReport::create($eventPostID, $indexID, $gender, $placementReport->comment, $placementReport->placement_id);
            $placementReportsRepository->save($placementReportModel);
        }
    }

    public function submitGeneralComment(?int $commentID, int $judgeID, ?string $comment, GeneralCommentRepository $generalCommentRepository){
        $generalCommentModel = isset($commentID) ? GeneralComment::createWithID($commentID, $judgeID, $comment) : GeneralComment::create($judgeID, $comment);
        $generalCommentRepository->save($generalCommentModel);
    }
}