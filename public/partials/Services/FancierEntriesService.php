<?php

class FancierEntriesService{
    private int $eventPostID;
    private int $locationID;

    public function __construct(int $eventPostID, int $locationID)
    {
        $this->eventPostID = $eventPostID;
        $this->locationID = $locationID;
    }

    public function prepareViewModel(): FancierEntriesViewModel
    {
        $viewModel = new FancierEntriesViewModel();

        $showClassRepository = new ShowClassesRepository($this->locationID);
        $classIndexRepository = new ClassIndexRepository($this->locationID);
        $userRegistrationsRepository = new UserRegistrationsRepository($this->eventPostID);
        $registrationOrderRepository = new RegistrationOrderRepository($this->eventPostID);
        $juniorRegistrationRepository = new JuniorRegistrationRepository($this->eventPostID);

        $registrationCollection = $showClassRepository->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class],
            ["id", "id", "id"], 
            ["class_id", "class_index_id", "registration_id"], 
            [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository])->{ClassIndexModel::class}->{UserRegistrationModel::class};
        $juniorRegistrationCollection = $juniorRegistrationRepository->getAll();
        ModelHydrator::mapExistingCollections($registrationCollection, $juniorRegistrationCollection, JuniorRegistrationModel::class, "id", "registration_id");
        $registrationCollection = $registrationCollection->groupBy("user_name");

        $juniorIndexModel = $classIndexRepository->getJuniorIndexModel();
        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            $juniorRegistrationCount = 0;
            $totalRegistrationCount = 0;
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $showClassModel = $classIndexModel->showClass();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                $viewModel->addClassRegistration($userRegistrationModel->user_name, $classIndexModel->class_index, $showClassModel->class_name, $classIndexModel->age, count($registrationOrder));
                $totalRegistrationCount += count($registrationOrder);
                if(isset($userRegistrationModel->juniorRegistrations)){
                    $juniorRegistrationCount += count($userRegistrationModel->juniorRegistrations);
                }
            }

            if($juniorRegistrationCount > 0 && isset($juniorIndexModel)){
                $viewModel->addClassRegistration($userName, $juniorIndexModel->class_index, "Junior", "AA", $juniorRegistrationCount);
            }

            $viewModel->addTotalRegistrationCount($userName, $totalRegistrationCount + $juniorRegistrationCount);
        }

        return $viewModel;
    }
}