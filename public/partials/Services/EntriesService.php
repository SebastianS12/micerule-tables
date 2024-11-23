<?php

class EntriesService{
    private EntryRepository $entryRepository;

    public function __construct(EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    public function createEntriesFromRegistrations(int $locationID, int $eventPostID): void{
        $showClassesRepository = new ShowClassesRepository($locationID);
        $classIndexRepository = new ClassIndexRepository($locationID);
        $registrationsRepository = new UserRegistrationsRepository($eventPostID);
        $registrationsOrderRepository = new RegistrationOrderRepository($eventPostID);
        $nextPenNumberRepository = new NextPenNumberRepository($locationID);

        $showClassesCollection = $showClassesRepository->getAll()->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class], 
            ["id", "id", "id", "id"], 
            ["class_id", "class_index_id", "registration_id", "registration_order_id"], 
            [$classIndexRepository, $registrationsRepository, $registrationsOrderRepository, $this->entryRepository]);
        $showClassesCollection->{ClassIndexModel::class}->with([NextPenNumberModel::class], ["id"], ["class_index_id"], [$nextPenNumberRepository]);
        $showClassesCollection = $showClassesCollection->groupBy("section");

        $penNumber = 1;
        foreach($showClassesCollection as $showClassModelCollection){
            foreach($showClassModelCollection as $showClassModel){
                foreach($showClassModel->classIndices() as $classIndexModel){
                    foreach($classIndexModel->registrations() as $userRegistration){
                        foreach($userRegistration->registrationOrder() as $registrationOrder){
                            $entry = $registrationOrder->entry();
                            if($entry !== null){
                                $registrationOrder->entry()->pen_number = $penNumber;
                            }else{
                                $entry = EntryModel::create($registrationOrder->id, $penNumber, $showClassModel->class_name, false, false, false);
                            }

                            $this->entryRepository->saveEntry($entry);
                            $penNumber++;
                        }
                    }
                    
                    $nextPenNumberModel = $classIndexModel->nextPenNumber();
                    if($nextPenNumberModel === null){
                        $nextPenNumberModel = NextPenNumberModel::create($classIndexModel->id, $penNumber);
                    } 
                    $nextPenNumberModel->next_pen_number = $penNumber;
                    $nextPenNumberRepository->save($nextPenNumberModel);

                    $penNumber = (floor($penNumber / 10) + 1) * 10 + 10;
                }
            }
        }
    }

    public function editEntryAbsent(int $entryID): void
    {
        $entryModel = $this->entryRepository->getByID($entryID);
        if(isset($entryModel)){
            $entryModel->absent = !$entryModel->absent;
        $this->entryRepository->saveEntry($entryModel);
        }
    }

    public function deleteEntry(int $entryID): void
    {
        $this->entryRepository->deleteEntry($entryID);
    }

    public function editVarietyName(int $entryID, string $varietyName, EntryRepository $entryRepository): void
    {
        $entryRepository->updateVariety($entryID, $varietyName);
    }
}