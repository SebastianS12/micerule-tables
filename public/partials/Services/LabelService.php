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

        $registrationCollection = $classIndexRepository->getAll()->with(
            [UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id", "id"], 
            ["class_index_id", "registration_id", "registration_order_id"], 
            [$userRegistrationsRepository, $registrationOrderRepository, $entryRepository])->{UserRegistrationModel::class};
        $registrationCollection = $registrationCollection->groupBy("user_name");

        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                foreach($registrationOrder as $registrationOrderModel){
                    $entry = $registrationOrderModel->entry();
                    if(isset($entry)){
                        $viewModel->addLabel($userName, $classIndexModel->class_index, $entry->pen_number, $entry->absent);
                    }
                }
            }
        }

        return $viewModel;
    }
}