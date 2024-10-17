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

        $registrationCollection = $showClassRepository->getAll()->with(["classIndices", "registrations", "order"], ["id", "id", "id"], ["classID", "classIndexID", "registrationID"], [$classIndexRepository, $userRegistrationsRepository, $registrationOrderRepository])->classIndices->registrations;
        $juniorRegistrationCollection = $juniorRegistrationRepository->getAll();
        ModelHydrator::mapExistingCollections($registrationCollection, "junior", $juniorRegistrationCollection, "id", "registrationID");
        $registrationCollection = $registrationCollection->groupBy("userName");

        $juniorIndexModel = $classIndexRepository->getJuniorIndexModel();
        foreach($registrationCollection as $userName  => $userRegistrationCollection){
            $juniorRegistrationCount = 0;
            $totalRegistrationCount = 0;
            foreach($userRegistrationCollection as $userRegistrationModel){
                $classIndexModel = $userRegistrationModel->classIndex();
                $showClassModel = $classIndexModel->showClass();
                $registrationOrder = $userRegistrationModel->registrationOrder();
                $viewModel->addClassRegistration($userRegistrationModel->userName, $classIndexModel->index, $showClassModel->className, $classIndexModel->age, count($registrationOrder));
                $totalRegistrationCount += count($registrationOrder);
                $juniorRegistrationCount += count($userRegistrationModel->junior);
            }

            if($juniorRegistrationCount > 0 && isset($juniorIndexModel)){
                $viewModel->addClassRegistration($userName, $juniorIndexModel->index, "Junior", "AA", $juniorRegistrationCount);
            }

            $viewModel->addTotalRegistrationCount($userName, $totalRegistrationCount + $juniorRegistrationCount);
        }

        return $viewModel;
    }
}