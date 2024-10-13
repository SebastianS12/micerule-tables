<?php

class RegistrationService{
    private int $eventPostID;
    private UserRegistrationsRepository $userRegistrationsRepository;
    private RegistrationOrderRepository $registrationOrderRepository;
    private RegistrationCountRepository $registrationCountRepository;
    private ClassIndexRepository $classIndexRepository;

    public function __construct(int $eventPostID, UserRegistrationsRepository $userRegistrationsRepository, RegistrationOrderRepository $registrationOrderRepository, RegistrationCountRepository $registrationCountRepository, ClassIndexRepository $classIndexRepository)
    {
        $this->eventPostID = $eventPostID;
        $this->userRegistrationsRepository = $userRegistrationsRepository;
        $this->registrationOrderRepository = $registrationOrderRepository;
        $this->registrationCountRepository = $registrationCountRepository;
        $this->classIndexRepository = $classIndexRepository;
    }

    public function registerEntries(array $classRegistrations, string $userName): array{
        $registrations = array();
        $juniorRegistrationCount = 0;
        // $registrationsCollection = $userRegistrationsRepository->getAll()->with(["registrationOrder"], ["registrationID"], [$registrationOrderRepository]);
        // $classIndexCollection = $classIndexRepository->getAll();
        // ModelHydrator::mapAttribute($registrationsCollection, $classIndexCollection, "classIndex", "classIndexID", "id", "index", 0);
        // $userRegistrationCollections = $registrationsCollection->groupBy("userName");
        // $userRegistrationCounts = $registrationCountRepository->getUserRegistrationCounts($userName);
        // ModelHydrator::mapAttribute($userRegistrationCollections[$userName], $userRegistrationCounts, "registrationCount", "classIndex", "index_number","entry_count", 0);
        // $userRegistrationsByIndex = isset($userRegistrationCollections[$userName]) ? $userRegistrationCollections[$userName]->groupByUniqueKey("classIndex") : new Collection();

        $classRepository = new ShowClassesRepository(EventProperties::getEventLocationID($this->eventPostID));
        //TODO: make more readable, put into describing functions
        $userRegistrationCollections = $this->userRegistrationsRepository->getUserRegistrations($userName)->with(["registrationOrder"], ["id"], ["registrationID"], [$this->registrationOrderRepository]);
        $classIndexCollection = $this->classIndexRepository->getAll()->with(["class"], ["classID"], ["id"], [$classRepository]);

        ModelHydrator::mapAttribute($userRegistrationCollections, $classIndexCollection, "classIndex", "classIndexID", "id", "index", 0);
        $userRegistrationCounts = $this->registrationCountRepository->getUserRegistrationCounts($userName);
        ModelHydrator::mapAttribute($userRegistrationCollections, $userRegistrationCounts, "registrationCount", "classIndex", "index_number","entry_count", 0);        

        $userRegistrationsByIndex = $userRegistrationCollections->groupByUniqueKey("classIndex");
        $classIndexCollection = $classIndexCollection->groupByUniqueKey("index");

        $showOptionsModel = new ShowOptionsModel();
        $addJunior = EventUser::isJuniorMember($userName) && $showOptionsModel->getShowOptions(EventProperties::getEventLocationID($this->eventPostID))['allow_junior'];

        foreach($classRegistrations as $classRegistrationData){
            $classIndex = $classRegistrationData['classIndex'];
            $registrationCount = intval($classRegistrationData['registrationCount']);

            $userRegistrationID = (isset($userRegistrationsByIndex[$classIndex])) ? $userRegistrationsByIndex[$classIndex]->id : null;
            if(!isset($userRegistrationID) && $registrationCount > 0){
                // $userRegistrationID = $this->userRegistrationsRepository->addRegistration($this->eventPostID, $userName, $classIndexCollection[$classIndex]->id);
            }

            if(isset($userRegistrationID) && $registrationCount == 0){
                // $this->userRegistrationsRepository->removeRegistration($userRegistrationID);
            }

            if(isset($userRegistrationID) && $registrationCount > 0){
                $registrations = $this->addRegistrationRecord($registrations, $classIndexCollection[$classIndex], $registrationCount);

                $userRegistrationCount = (isset($userRegistrationsByIndex[$classIndex])) ? $userRegistrationsByIndex[$classIndex]->registrationCount : 0;
                // for($i = $userRegistrationCount; $i < $registrationCount; $i++){
                //     $registrationOrderID = $this->registrationOrderRepository->addRegistration($userRegistrationID, current_time("mysql"));
                //     if($addJunior){
                //         $this->registrationOrderRepository->addJuniorRegistration($registrationOrderID);
                        // $juniorRegistrationCount++;
                //     }
                // }
                // for($i = $userRegistrationCount; $i > $registrationCount; $i--){
                //     $this->registrationOrderRepository->removeRegistration($userRegistrationsByIndex[$classIndex]->registrationOrder->last()->id);
                // }
            }  
        }

        if($juniorRegistrationCount > 0){
            foreach($classIndexCollection as $classIndexModel){
                if($classIndexModel->class()->className == "Junior"){
                    $registrations = $this->addRegistrationRecord($registrations, $classIndexModel, $juniorRegistrationCount);
                }
            }
        }

        return $registrations;
    }

    private function addRegistrationRecord(array $registrations, ClassIndexModel $classIndexModel, int $registrationCount): array{
        $registration = array();
        $registration['classIndex'] = $classIndexModel->index;
        $registration['className'] = $classIndexModel->class()->className;
        $registration['age'] = $classIndexModel->age;
        $registration['registrationCount'] = $registrationCount;
        $registrations[] = $registration;

        return $registrations;
    }
}