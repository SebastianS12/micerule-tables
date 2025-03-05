<?php

class JudgingSheetsService{
    private int $eventPostID;
    private int $locationID;

    public function __construct(int $eventPostID, int $locationID)
    {
        $this->eventPostID = $eventPostID;
        $this->locationID = $locationID;
    }

    public function prepareViewModel(): JudgingSheetsViewModel
    {
        $viewModel = new JudgingSheetsViewModel();

        $judgesRepository = new JudgesRepository($this->eventPostID);
        $judgesSectionsRepository = new JudgesSectionsRepository($this->eventPostID);

        $showClassRepository = new ShowClassesRepository($this->locationID);
        $classIndexRepository = new ClassIndexRepository($this->locationID);
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $entryRepository = new EntryRepository($this->eventPostID);
        $breedsService = new BreedsService(new BreedsRepository(), $showClassRepository);

        $challengeIndexRepository = new ChallengeIndexRepository($this->locationID);

        $judgeCollection = $judgesRepository->getAll()->with([JudgeSectionModel::class], ['id'], ['judge_id'], [$judgesSectionsRepository]);
        $showClassesCollection = $showClassRepository->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id", "id", "id"], 
            ["class_id", "class_index_id", "registration_id", "registration_order_id"], 
            [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, $entryRepository]);
        ModelHydrator::mapExistingCollections($judgeCollection->{JudgeSectionModel::class}, $showClassesCollection, EntryClassModel::class, "section", "section");
        $challengeIndexCollection = $challengeIndexRepository->getAll();
        ModelHydrator::mapExistingCollections($judgeCollection->{JudgeSectionModel::class}, $challengeIndexCollection, ChallengeIndexModel::class, "section", "section");

        foreach($judgeCollection as $judgeModel){
            foreach($judgeModel->sections() as $judgeSectionModel){
                foreach($judgeSectionModel->{EntryClassModel::class} as $entryClassModel){
                    foreach($entryClassModel->classIndices() as $classIndexModel){
                        $showVarietyPrompt = (!$breedsService->isStandardBreed($entryClassModel->class_name));
                        $viewModel->addClassSheet($judgeModel->judge_name, $judgeSectionModel->section, $entryClassModel->class_name, $classIndexModel->age, $classIndexModel->class_index, $showVarietyPrompt);
                        foreach($classIndexModel->registrations()->{RegistrationOrderModel::class}->{EntryModel::class} as $entryModel){
                            $viewModel->addPenNumber($judgeModel->judge_name, $judgeSectionModel->section, $classIndexModel->class_index, $entryModel->pen_number);
                        }
                    }
                }

                foreach($judgeSectionModel->{ChallengeIndexModel::class} as $challengeIndexModel){
                    $addSectionBestSheet = $challengeIndexModel->age == "U8";
                    $viewModel->addSectionSheet($judgeModel->judge_name, $challengeIndexModel->challenge_name, $challengeIndexModel->challenge_index, $challengeIndexModel->age, $challengeIndexModel->section, $addSectionBestSheet);
                }
            }
        }

        //grand challenge sheets
        $judges = JudgeFormatter::getJudgesString($judgeCollection);
        foreach($challengeIndexCollection->where("challenge_name", EventProperties::GRANDCHALLENGE) as $challengeIndexModel){
            $addSectionBestSheet = $challengeIndexModel->age == "U8";
            $viewModel->addGrandChallengeSheet($judges, $challengeIndexModel->challenge_name, $challengeIndexModel->challenge_index, $challengeIndexModel->age, $challengeIndexModel->section, $addSectionBestSheet);
        }

        // //optional
        $juniorRegistrationsRepository = new JuniorRegistrationRepository($this->eventPostID);
        $juniorCollection = $juniorRegistrationsRepository->getAll();
        $registrationOrderCollection = $showClassesCollection->{ClassIndexModel::class}->{UserRegistrationModel::class}->{RegistrationOrderModel::class};
        ModelHydrator::mapExistingCollections($registrationOrderCollection, $juniorCollection, JuniorRegistrationModel::class,"id", "registration_order_id");
        foreach($showClassesCollection->groupBy("section")['optional'] as $optionalClassModel){
            foreach($optionalClassModel->classIndices as $classIndexModel){
                $showVarietyPrompt = true;//($optionalClassModel->class_name != "Junior");
                $viewModel->addOptionalClassSheet($optionalClassModel->class_name, $classIndexModel->age, $classIndexModel->class_index, $showVarietyPrompt);

                if($optionalClassModel->class_name == "Junior"){
                    foreach($juniorCollection as $juniorRegistrationModel){
                        $entry = $juniorRegistrationModel->order()->entry();
                        if(isset($entry)){
                            $viewModel->addOptionalClassPenNumber($classIndexModel->class_index, $entry->pen_number);
                        }
                    }
                }else{
                    foreach($classIndexModel->registrations()->{RegistrationOrderModel::class}->{EntryModel::class} as $entryModel){
                        $viewModel->addOptionalClassPenNumber($classIndexModel->class_index, $entryModel->pen_number);
                    }
                }
            }
        }

        return $viewModel;
    }
}