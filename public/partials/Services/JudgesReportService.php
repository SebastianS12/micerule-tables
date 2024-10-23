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
        $judgeCollection->with(['comment'], ['id'], ['judgeID'], [$generalCommentRepository]);

        $challengeIndexCollection = $this->loadChallengeIndices($eventPostID, $locationID);
        $showClassesCollection = $this->loadShowClasses($eventPostID);
        $registrationCountCollection = $this->loadRegistrationCounts($eventPostID, $locationID);

        // Map registrations and placements
        $this->mapPlacements($showClassesCollection, $challengeIndexCollection, $eventPostID);
        $this->mapRegistrationCounts($showClassesCollection, $challengeIndexCollection, $registrationCountCollection);
        $this->mapJudgesToClasses($showClassesCollection, $challengeIndexCollection, $judgeCollection);

        $classCommentsRepository = new ClassCommentsRepository($eventPostID);
        $showClassesCollection->indices->with(['comment'], ['id'], ['classIndexID'], [$classCommentsRepository]);

        $placementReportsRepository = new PlacementReportsRepository($eventPostID);
        $showClassesCollection->indices->placements->with(['report'], ['id'], ['placementID'], [$placementReportsRepository]);

        foreach($judgeCollection as $judgeModel){
            $commentModel = $judgeModel->comment();
            $commentID = (isset($commentModel)) ? $commentModel->id : null;
            $comment = (isset($commentModel)) ? $commentModel->comment : "";
            $viewModel->addJudgeComment($judgeModel->judgeName, $judgeModel->id, $commentID, $comment);

            foreach($judgeModel->sections() as $judgeSectionModel){
                $viewModel->addJudgeSection($judgeModel->judgeName, $judgeSectionModel->section);
            }
        }

        foreach($showClassesCollection->whereNot("sectionName", "optional") as $entryClassModel){
            foreach($entryClassModel->indices as $classIndexModel){
                $classCommentModel = $classIndexModel->comment();
                $commentID = (isset($classCommentModel)) ? $classCommentModel->id : null;
                $comment = (isset($classCommentModel)) ? $classCommentModel->comment : "";
                $judge = $entryClassModel->judgeSection()->judge()->judgeName;
                $viewModel->addClassReport($classIndexModel->id, $commentID, $judge, $classIndexModel->index, $comment, $entryClassModel->sectionName, $entryClassModel->className, $classIndexModel->age, $classIndexModel->registrationCount);
                foreach($classIndexModel->placements as $placementModel){
                    $placementReportModel = $placementModel->report();
                    $placementReportID = isset($placementReportModel) ? $placementReportModel->id : null;
                    $gender = isset($placementReportModel) ? $placementReportModel->gender : null;
                    $comment = isset($placementReportModel) ? $placementReportModel->comment : "";
                    $userName = $placementModel->registration()->userName;
                    $viewModel->addPlacementReport($placementReportID, $judge, $entryClassModel->sectionName, $classIndexModel->index, $placementModel->id, $placementModel->placement, $userName, $gender, $comment);
                }
            }
        }

        foreach($challengeIndexCollection->whereNot("challengeName", EventProperties::GRANDCHALLENGE) as $challengeIndexModel){
            $judge = $challengeIndexModel->judgeSection()->judge()->judgeName;
            $viewModel->addChallengeReport($judge, $challengeIndexModel->challengeIndex, $challengeIndexModel->section, $challengeIndexModel->challengeName, $challengeIndexModel->age, $challengeIndexModel->registrationCount);
            foreach($challengeIndexModel->placements as $placementModel){
                $fancierName = $placementModel->registration()->userName;
                $varietyName = $placementModel->entry()->varietyName;
                $viewModel->addChallengePlacementReport($judge, $challengeIndexModel->section, $challengeIndexModel->challengeIndex, $fancierName, $placementModel->placement, $varietyName);
            }
        }

        foreach($showClassesCollection->where("sectionName", "optional")->whereNot("className", "Junior") as $entryClassModel){
            foreach($entryClassModel->indices as $classIndexModel){
                $classCommentModel = $classIndexModel->comment();
                $commentID = (isset($classCommentModel)) ? $classCommentModel->id : null;
                $comment = (isset($classCommentModel)) ? $classCommentModel->comment : "";
                $viewModel->addOptionalClassReport($classIndexModel->id, $commentID, $classIndexModel->index, $comment, $entryClassModel->sectionName, $entryClassModel->className, $classIndexModel->age, $classIndexModel->registrationCount);
                foreach($classIndexModel->placements as $placementModel){
                    $placementReportModel = $placementModel->report();
                    $placementReportID = isset($placementReportModel) ? $placementReportModel->id : null;
                    $gender = isset($placementReportModel) ? $placementReportModel->gender : null;
                    $comment = isset($placementReportModel) ? $placementReportModel->comment : "";
                    $userName = $placementModel->registration()->userName;
                    $viewModel->addOptionalClassPlacementReport($placementReportID, $entryClassModel->sectionName, $classIndexModel->index, $placementModel->id, $placementModel->placement, $userName, $gender, $comment);
                }
            }
        }

        return $viewModel;
    }

    private function loadJudges(int $eventPostID): Collection
    {
        $judgesRepository = new JudgesRepository($eventPostID);
        $judgesSectionsRepository = new JudgesSectionsRepository($eventPostID);
        return $judgesRepository->getAll()->with(['sections'], ['id'], ['judgeID'], [$judgesSectionsRepository]);
    }

    private function loadChallengeIndices(int $eventPostID, int $locationID): Collection
    {
        $challengeIndexRepository = new ChallengeIndexRepository($locationID);
        $challengePlacementsRepository = new PlacementsRepository($eventPostID, new ChallengePlacementDAO());
        $awardRepository = new AwardsRepository($eventPostID);
        $awardRepository->getAll();

        return $challengeIndexRepository->getAll()->with(
            ["placements", "award"],
            ["id", "id"],
            ["indexID", "challengePlacementID"],
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
            ["indices", "registrations", "order", "entry"],
            ["id", "id", "id", "id"],
            ["classID", "classIndexID", "registrationID", "registrationOrderID"],
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
        $showClassesCollection->indices->with(
            ["placements"], 
            ["id"], 
            ["indexID"], 
            [$classPlacementsRepository]
        );

        // Map class placements to entries
        ModelHydrator::mapExistingCollections(
            $showClassesCollection->indices->registrations->order->entry,
            "placements", 
            $showClassesCollection->indices->placements, 
            "id", 
            "entryID"
        );

        // Challenge Placements
        ModelHydrator::mapExistingCollections(
            $showClassesCollection->indices->registrations->order->entry, 
            "placements", 
            $challengeIndexCollection->placements, 
            "id", 
            "entryID"
        );
    }

    private function mapRegistrationCounts(Collection $showClassesCollection, Collection $challengeIndexCollection, Collection $registrationCountCollection): void
    {
        ModelHydrator::mapAttribute($showClassesCollection->indices, $registrationCountCollection, "registrationCount", "index", "index_number", "entry_count", 0);
        ModelHydrator::mapAttribute($challengeIndexCollection, $registrationCountCollection, "registrationCount", "challengeIndex", "index_number", "entry_count", 0);
    }

    private function mapJudgesToClasses(Collection $showClassesCollection, Collection $challengeIndexCollection, Collection $judgeCollection): void
    {
        // Map judges to show classes
        ModelHydrator::mapExistingCollections(
            $showClassesCollection, 
            "judgeSection", 
            $judgeCollection->sections, 
            "sectionName", 
            "section"
        );

        // Map judges to challenge indices
        ModelHydrator::mapExistingCollections(
            $challengeIndexCollection, 
            "judgeSection", 
            $judgeCollection->sections, 
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
            
            $placementReportModel = isset($reportID) ? PlacementReport::createWithID($reportID, $eventPostID, $indexID, $gender, $placementReport->comment, $placementReport->placementID): PlacementReport::create($eventPostID, $indexID, $gender, $placementReport->comment, $placementReport->placementID);
            $placementReportsRepository->save($placementReportModel);
        }
    }

    public function submitGeneralComment(?int $commentID, int $judgeID, ?string $comment, GeneralCommentRepository $generalCommentRepository){
        $generalCommentModel = isset($commentID) ? GeneralComment::createWithID($commentID, $judgeID, $comment) : GeneralComment::create($judgeID, $comment);
        $generalCommentRepository->save($generalCommentModel);
    }
}