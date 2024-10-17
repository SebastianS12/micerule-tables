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

        $showClassesCollection = $showClassesRepository->getAll()->with(["indices", "registrations", "order", "entry"], ["id", "id", "id", "id"], ["classID", "classIndexID", "registrationID", "registrationOrderID"], [$classIndexRepository, $registrationsRepository, $registrationsOrderRepository, $this->entryRepository]);
        $showClassesCollection = $showClassesCollection->groupBy("sectionName");

        $penNumber = 1;
        foreach($showClassesCollection as $showClassModelCollection){
            foreach($showClassModelCollection as $showClassModel){
                foreach($showClassModel->indices as $classIndexModel){
                    foreach($classIndexModel->registrations as $userRegistration){
                        foreach($userRegistration->order as $registrationOrder){
                            $entry = $registrationOrder->entry();
                            if(isset($entry)){
                                $entry->penNumber = $penNumber;
                            }else{
                                $entry = EntryModel::create($registrationOrder->id, $penNumber, $showClassModel->className, false, false, false);
                            }
                            $this->entryRepository->saveEntry($entry);
                            $penNumber++;
                        }
                    }

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
}