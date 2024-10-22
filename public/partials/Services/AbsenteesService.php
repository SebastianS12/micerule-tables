<?php

class AbsenteesService{
    private int $eventPostID;
    private int $locationID;

    public function __construct(int $eventPostID, int $locationID)
    {
        $this->eventPostID = $eventPostID;
        $this->locationID = $locationID;
    }

    public function prepareViewModel(): AbsenteesViewModel
    {
        $viewModel = new AbsenteesViewModel();

        $judgesRepository = new JudgesRepository($this->eventPostID);
        $judgesSectionsRepository = new JudgesSectionsRepository($this->eventPostID);

        $showClassRepository = new ShowClassesRepository($this->locationID);
        $classIndexRepository = new ClassIndexRepository($this->locationID);
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $entryRepository = new EntryRepository($this->eventPostID);

        $judgeCollection = $judgesRepository->getAll()->with(['sections'], ['id'], ['judgeID'], [$judgesSectionsRepository]);
        $showClassesCollection = $showClassRepository->getAll()->with(["classIndices", "registrations", "order", "entry"], ["id", "id", "id", "id"], ["classID", "classIndexID", "registrationID", "registrationOrderID"], [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, $entryRepository]);
        ModelHydrator::mapExistingCollections($judgeCollection->sections, "classes", $showClassesCollection, "section", "sectionName");

        foreach($judgeCollection as $judgeModel){
            $viewModel->addJudge($judgeModel->judgeName);
            foreach($judgeModel->sections() as $judgeSectionModel){
                foreach($judgeSectionModel->classes as $entryClassModel){
                    foreach($entryClassModel->classIndices() as $classIndexModel){
                        foreach($classIndexModel->registrations->order->entry->where("absent", true) as $entryModel){
                            $viewModel->addAbsentee($judgeModel->judgeName, $classIndexModel->index, $entryModel->penNumber);
                        }
                    }
                }
            }
        }

        return $viewModel;
    }
}