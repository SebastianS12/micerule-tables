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

        $showClassModel = new ShowClassesRepository($this->locationID);
        $classIndexRepository = new ClassIndexRepository($this->locationID);
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $entryRepository = new EntryRepository($this->eventPostID);

        $registrationCollection = $showClassModel->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id", "id", "id"], 
            ["class_id", "class_index_id", "registration_id", "registration_order_id"], 
            [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository, $entryRepository])->{ClassIndexModel::class}->{UserRegistrationModel::class};
        $registrationCollection = $registrationCollection->groupBy("user_name");

        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $showClassModel = $classIndexModel->class();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                foreach($registrationOrder as $registrationOrderModel){
                    $entry = $registrationOrderModel->entry();
                    if(isset($entry)){
                        $viewModel->addLabel($userName, $classIndexModel->class_index, $entry->pen_number, $entry->absent, $showClassModel->class_name, $classIndexModel->age, $showClassModel->section);
                    }
                }
            }
        }

        return $viewModel;
    }
}