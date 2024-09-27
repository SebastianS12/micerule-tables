<?php

class EntriesService{
    private $entryRepository;
    private $userRegistrationsRepository;
    private $showClassesRepository;

    public function __construct(EntryRepository $entryRepository, UserRegistrationsRepository $userRegistrationsRepository, ShowClassesRepository $showClassesRepository)
    {
        $this->entryRepository = $entryRepository;
        $this->userRegistrationsRepository = $userRegistrationsRepository;
        $this->showClassesRepository = $showClassesRepository;
    }

    public function getAllEntries(): array{
        $entries = $this->entryRepository->getAll();
        //TODO: fetch userRegistrations and ShowClasses in Bulk
        foreach($entries as $entry){
            $userRegistration = $this->userRegistrationsRepository->getByID($entry->classRegistrationID);
            $userRegistration->showClass = $this->showClassesRepository->getByID($userRegistration->classID);
            $entry->userRegistration = $userRegistration;
        }

        return $entries;
    }

    public function getEntriesByClassIndex(): array{
        $entries = array();

        foreach($this->getAllEntries() as $entry){
            $entries[]
        }


        return $entries;
    }
}