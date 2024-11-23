<?php

class EntrySummaryService{
    private int $eventPostID;
    private int $locationID;

    public function __construct(int $eventPostID, int $locationID)
    {
        $this->eventPostID = $eventPostID;
        $this->locationID = $locationID;
    }

    public function prepareViewModel(): EntrySummaryViewModel
    {
        $viewModel = new EntrySummaryViewModel();

        $showClassRepository = new ShowClassesRepository($this->locationID);
        $classIndexRepository = new ClassIndexRepository($this->locationID);
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $entryRepository = new EntryRepository($this->eventPostID);

        $registrationCollection = $showClassRepository->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id", "id", "id"], 
            ["class_id", "class_index_id", "registration_id", "registration_order_id"], 
            [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, $entryRepository])->{ClassIndexModel::class}->{UserRegistrationModel::class};
        $registrationCollection = $registrationCollection->groupBy("user_name");

        //TODO: Service
        $showOptionsService = new ShowOptionsService();
        $showOptions = $showOptionsService->getShowOptions(new ShowOptionsRepository(), $this->locationID);
        $registrationFee = $showOptions->registration_fee;
        $auctionFee = $showOptions->auction_fee;

        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            $userRegistrationFee = 0;
            $allEntriesAbsent = false;
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $showClassModel = $classIndexModel->showClass();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                foreach($registrationOrder as $registrationOrderModel){
                    $entry = $registrationOrderModel->entry();
                    if(isset($entry)){
                        $viewModel->addUserEntry($userName, $showClassModel->class_name, $classIndexModel->class_index, $classIndexModel->age, $entry->pen_number);
                        $allEntriesAbsent = $allEntriesAbsent || $entry->absent;

                        if($showClassModel->class_name != "Auction"){
                            $userRegistrationFee += $registrationFee;
                        }else{
                            $userRegistrationFee += $auctionFee;
                        }
                    }
                }
            }

            $viewModel->addUserRegistrationFee($userName, $userRegistrationFee);
            $viewModel->addAllEntriesAbsent($userName, $allEntriesAbsent);
        }


        return $viewModel;
    }

    public function setAllAbsent(string $userName, bool $absent){
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $entryRepository = new EntryRepository($this->eventPostID);

        $registrationCollection = $userRegistrationsRepository->getAll(function(QueryBuilder $query) use ($userName){
            $query->where(Table::REGISTRATIONS->getAlias(), "user_name", "=", $userName);
        })->with(
            [RegistrationOrderModel::class, EntryModel::class],
            ["id", "id"], 
            ["registration_id", "registration_order_id"], 
            [$registrationOrderRepository, $entryRepository]);

        foreach($registrationCollection->{RegistrationOrderModel::class}->{EntryModel::class} as $fancierEntry){
            $fancierEntry->absent = $absent;
            $entryRepository->saveEntry($fancierEntry);
        }
    }
}