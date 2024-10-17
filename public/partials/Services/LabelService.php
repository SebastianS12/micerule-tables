<?php

class LabelService{
    private int $eventPostID;
    private int $locationID;

    public function __construct(int $eventPostID, int $locationID)
    {
        $this->eventPostID = $eventPostID;
        $this->locationID = $locationID;
    }

    public function prepareViewModel(): LabelViewModel
    {
        $viewModel = new LabelViewModel();

        $classIndexRepository = new ClassIndexRepository($this->locationID);
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $entryRepository = new EntryRepository($this->eventPostID);

        $registrationCollection = $classIndexRepository->getAll()->with(["registrations", "order", "entry"], ["id", "id", "id"], ["classIndexID", "registrationID", "registrationOrderID"], [$userRegistrationsRepository, $registrationOrderRepository, $entryRepository])->registrations;
        $registrationCollection = $registrationCollection->groupBy("userName");

        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                foreach($registrationOrder as $registrationOrderModel){
                    $entry = $registrationOrderModel->entry();
                    if(isset($entry)){
                        $viewModel->addLabel($userName, $classIndexModel->index, $entry->penNumber, $entry->absent);
                    }
                }
            }
        }

        return $viewModel;
    }
}