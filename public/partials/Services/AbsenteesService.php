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

        $judgeCollection = $judgesRepository->getAll()->with([JudgeSectionModel::class], ['id'], ['judge_id'], [$judgesSectionsRepository]);
        $showClassesCollection = $showClassRepository->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class], 
            ["id", "id", "id", "id"], 
            ["class_id", "class_index_id", "registration_id", "registration_order_id"], 
            [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, $entryRepository]);
        ModelHydrator::mapExistingCollections($judgeCollection->{JudgeSectionModel::class}, $showClassesCollection, EntryClassModel::class, "section", "section");

        foreach($judgeCollection as $judgeModel){
            $viewModel->addJudge($judgeModel->judge_name);
            foreach($judgeModel->sections() as $judgeSectionModel){
                foreach($judgeSectionModel->{EntryClassModel::class} as $entryClassModel){
                    foreach($entryClassModel->classIndices as $classIndexModel){
                        foreach($classIndexModel->registrations->order->entry->where("absent", true) as $entryModel){
                            $viewModel->addAbsentee($judgeModel->judge_name, $classIndexModel->class_index, $entryModel->pen_number);
                        }
                    }
                }
            }
        }

        return $viewModel;
    }
}