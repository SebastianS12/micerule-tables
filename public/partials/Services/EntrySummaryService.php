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

        $registrationCollection = $showClassRepository->getAll()->with(["classIndices", "registrations", "order", "entry"], ["id", "id", "id", "id"], ["classID", "classIndexID", "registrationID", "registrationOrderID"], [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, $entryRepository])->classIndices->registrations;
        $registrationCollection = $registrationCollection->groupBy("userName");

        //TODO: Service
        $showOptionsModel = new ShowOptionsModel();
        $registrationFee = $showOptionsModel->getRegistrationFee($this->locationID);

        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            $userRegistrationCount = 0;
            $allEntriesAbsent = false;
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $showClassModel = $classIndexModel->showClass();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                foreach($registrationOrder as $registrationOrderModel){
                    $entry = $registrationOrderModel->entry();
                    if(isset($entry)){
                        $viewModel->addUserEntry($userName, $showClassModel->className, $classIndexModel->index, $classIndexModel->age, $entry->penNumber);
                        $allEntriesAbsent = $allEntriesAbsent || $entry->absent;
                        $userRegistrationCount++;
                    }
                }
            }

            $viewModel->addUserRegistrationFee($userName, $userRegistrationCount * $registrationFee);
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
        })->with(["order", "entry"], ["id", "id", "id", "id"], ["registrationID", "registrationOrderID"], [$registrationOrderRepository, $entryRepository]);

        foreach($registrationCollection->order->entry as $fancierEntry){
            $fancierEntry->absent = $absent;
            $entryRepository->saveEntry($fancierEntry);
        }
    }
}