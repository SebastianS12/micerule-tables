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

        $challengeIndexRepository = new ChallengeIndexRepository($this->locationID);

        $judgeCollection = $judgesRepository->getAll()->with(['sections'], ['id'], ['judgeID'], [$judgesSectionsRepository]);
        $showClassesCollection = $showClassRepository->getAll()->with(["classIndices", "registrations", "order", "entry"], ["id", "id", "id", "id"], ["classID", "classIndexID", "registrationID", "registrationOrderID"], [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, $entryRepository]);
        ModelHydrator::mapExistingCollections($judgeCollection->sections, "classes", $showClassesCollection, "section", "sectionName");
        $challengeIndexCollection = $challengeIndexRepository->getAll();
        ModelHydrator::mapExistingCollections($judgeCollection->sections, "challengeIndices", $challengeIndexCollection, "section", "section");

        foreach($judgeCollection as $judgeModel){
            foreach($judgeModel->sections() as $judgeSectionModel){
                foreach($judgeSectionModel->classes as $entryClassModel){
                    foreach($entryClassModel->classIndices() as $classIndexModel){
                        $viewModel->addClassSheet($judgeModel->judgeName, $judgeSectionModel->section, $entryClassModel->className, $classIndexModel->age, $classIndexModel->index);
                        foreach($classIndexModel->registrations->order->entry as $entryModel){
                            $viewModel->addPenNumber($judgeModel->judgeName, $judgeSectionModel->section, $classIndexModel->index, $entryModel->penNumber);
                        }
                    }
                }

                foreach($judgeSectionModel->challengeIndices as $challengeIndexModel){
                    $viewModel->addSectionSheet($judgeModel->judgeName, $challengeIndexModel->challengeName, $challengeIndexModel->challengeIndex, $challengeIndexModel->age, $challengeIndexModel->section);
                }
            }
        }

        //grand challenge sheets
        $judges = JudgeFormatter::getJudgesString($judgeCollection);
        foreach($challengeIndexCollection->where("challengeName", EventProperties::GRANDCHALLENGE) as $challengeIndexModel){
            $viewModel->addGrandChallengeSheet($judges, $challengeIndexModel->challengeName, $challengeIndexModel->challengeIndex, $challengeIndexModel->age, $challengeIndexModel->section);
        }

        //optional
        $juniorRegistrationsRepository = new JuniorRegistrationRepository($this->eventPostID);
        $juniorCollection = $juniorRegistrationsRepository->getAll();
        $registrationOrderCollection = $showClassesCollection->classIndices->registrations->order;
        ModelHydrator::mapExistingCollections($registrationOrderCollection, "junior", $juniorCollection, "id", "registrationOrderID");
        foreach($showClassesCollection->groupBy("sectionName")['optional'] as $optionalClassModel){
            foreach($optionalClassModel->classIndices() as $classIndexModel){
                $viewModel->addOptionalClassSheet($optionalClassModel->className, $classIndexModel->age, $classIndexModel->index);

                if($optionalClassModel->className == "Junior"){
                    foreach($juniorCollection as $juniorRegistrationModel){
                        $entry = $juniorRegistrationModel->order()->entry();
                        if(isset($entry)){
                            $viewModel->addOptionalClassPenNumber($classIndexModel->index, $entry->penNumber);
                        }
                    }
                }else{
                    foreach($classIndexModel->registrations->order->entry as $entryModel){
                        $viewModel->addOptionalClassPenNumber($classIndexModel->index, $entryModel->penNumber);
                    }
                }
            }
        }

        return $viewModel;
    }
}