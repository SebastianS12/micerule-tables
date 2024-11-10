<?php

class ShowReportPostService{
    public function prepareViewModel(int $locationID, int $eventPostID): ShowReportPostViewModel
    {
        $eventMetaData = EventProperties::getEventMetaData($eventPostID);
        $showName = $eventMetaData['event_name'];
        $viewModel = new ShowReportPostViewModel($showName);

        $judgeDataLoader = new JudgeDataLoader();
        $judgeDataLoader->load(new JudgesRepository($eventPostID));
        $judgeDataLoader->withSections(new JudgesSectionsRepository($eventPostID));
        $judgeDataLoader->withComments(new GeneralCommentRepository($eventPostID));
        $judgeCollection = $judgeDataLoader->getCollection();

        $challengeIndexLoader = new ChallengeIndexDataLoader();
        $challengeIndexLoader->load(new ChallengeIndexRepository($locationID));
        $challengeIndexLoader->withAwards(new PlacementsRepository($eventPostID, new ChallengePlacementDAO()), new AwardsRepository($eventPostID));
        $challengeIndexCollection = $challengeIndexLoader->getCollection();

        $showClassDataLoader = new ShowClassDataLoader();
        $showClassDataLoader->load(new ShowClassesRepository($locationID));
        $classIndexRepository = new ClassIndexRepository($locationID);
        $registrationsRepository = new UserRegistrationsRepository($eventPostID);
        $registrationsOrderRepository = new RegistrationOrderRepository($eventPostID);
        $entryRepository = new EntryRepository($eventPostID);
        $showClassDataLoader->withEntries($classIndexRepository, $registrationsRepository, $registrationsOrderRepository, $entryRepository);
        $showClassDataLoader->withClassPlacements(new PlacementsRepository($eventPostID, new ClassPlacementDAO()));
        $showClassDataLoader->withClassComments(new ClassCommentsRepository($eventPostID));
        $showClassDataLoader->withClassPlacementReports(new PlacementReportsRepository($eventPostID));
        $showClassesCollection = $showClassDataLoader->getCollection();

        $registrationCountRepository = new RegistrationCountRepository($eventPostID, $locationID);
        $registrationCountCollection = $registrationCountRepository->getAll();

        PlacementsMapper::mapClassPlacementsToEntries($showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class}->{EntryModel::class}, $showClassesCollection->{ClassIndexModel::class}->{ClassPlacementModel::class});
        PlacementsMapper::mapChallengePlacementsToEntries($showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class}->{EntryModel::class}, $challengeIndexCollection->{ChallengePlacementModel::class});

        RegistrationCountMapper::mapRegistrationCountsToClassIndices($showClassesCollection->{ClassIndexModel::class}, $registrationCountCollection);
        RegistrationCountMapper::mapRegistrationCountsToChallengeIndices($challengeIndexCollection, $registrationCountCollection);
       
        JudgeMapper::mapJudgesSectionsToClasses($showClassesCollection, $judgeCollection->{JudgeSectionModel::class});
        JudgeMapper::mapJudgesSectionsToClasses($challengeIndexCollection, $judgeCollection->{JudgeSectionModel::class});

        foreach($judgeCollection as $judgeModel){
            $viewModel->addJudge($judgeModel->judge_name);
            if($judgeModel->comment() !== null) $viewModel->addJudgeComment($judgeModel->judge_name, $judgeModel->comment()->comment);

            foreach($judgeModel->sections() as $judgeSectionModel){
                $viewModel->addJudgeSection($judgeModel->judge_name, $judgeSectionModel->section);
            }
        }

        foreach($showClassesCollection->whereNot("section", "optional") as $entryClassModel){
            foreach($entryClassModel->classIndices as $classIndexModel){
                $comment = ($classIndexModel->comment() !== null) ? $classIndexModel->comment()->comment : "";
                $judge = JudgeFormatter::getJudgeName($entryClassModel);
                $viewModel->addClassReport($judge, $entryClassModel->section, $entryClassModel->class_name, $classIndexModel->class_index, $classIndexModel->age, $classIndexModel->registrationCount, $comment);
                foreach($classIndexModel->placements() as $placementModel){
                    echo(var_dump($placementModel));
                    $gender = ($placementModel->report() !== null) ? $placementModel->report()->gender : null;
                    $comment = ($placementModel->report() !== null) ? $placementModel->report()->comment : "";
                    $userName = $placementModel->registration()->user_name;
                    $varietyName = $placementModel->entry()->variety_name;
                    $viewModel->addPlacementReport($judge, $entryClassModel->section, $classIndexModel->class_index, $placementModel->placement, $varietyName, $userName, $gender, $comment);
                }
            }
        }

        foreach($challengeIndexCollection as $challengeIndexModel){
            if( Section::from($challengeIndexModel->section) != Section::GRAND_CHALLENGE){
                $judge =  JudgeFormatter::getJudgeName($challengeIndexModel);
                $viewModel->addSectionChallengeReport($judge, $challengeIndexModel->section, $challengeIndexModel->challenge_index, $challengeIndexModel->challenge_name, $challengeIndexModel->age, $challengeIndexModel->registrationCount);
            }else{
                $judge =  JudgeFormatter::getJudgesString($judgeCollection);
                $viewModel->addGrandChallengeReport($judge, $challengeIndexModel->challenge_index, $challengeIndexModel->challenge_name, $challengeIndexModel->age, $challengeIndexModel->registrationCount);
            }
            
            foreach($challengeIndexModel->placements as $placementModel){
                $fancierName = $placementModel->registration->user_name;
                $varietyName = $placementModel->entry->variety_name;
                if( $challengeIndexModel->section != Section::GRAND_CHALLENGE){
                    $viewModel->addChallengePlacementReport($judge, $challengeIndexModel->section, $challengeIndexModel->challenge_index, $placementModel->placement, $fancierName, $varietyName);
                }else{
                    $viewModel->addGrandChallengePlacementReport($challengeIndexModel->challenge_index, $placementModel->placement, $fancierName, $varietyName);
                }
            }
        }

        foreach($showClassesCollection->where("class_name", "Junior") as $entryClassModel){
            foreach($entryClassModel->classIndices as $classIndexModel){
                $comment = ($classIndexModel->comment() !== null) ? $classIndexModel->comment()->comment : "";
                $viewModel->addJuniorClassReport($entryClassModel->class_name, $classIndexModel->class_index, $classIndexModel->age, $classIndexModel->registrationCount, $comment);
                foreach($classIndexModel->placements as $placementModel){
                    $gender = ($placementModel->report() !== null) ? $placementModel->report()->gender : null;
                    $comment = ($placementModel->report() !== null) ? $placementModel->report()->comment : "";
                    $userName = $placementModel->registration->user_name;
                    $viewModel->addJuniorPlacementReport($classIndexModel->class_index, $placementModel->placement, $userName, $gender, $comment, $entryClassModel->class_name);
                }
            }
        }

        return $viewModel;
    }
}