<?php

class EntrySummaryViewModel{
    public array $fancierEntrySummaries;

    public function __construct()
    {
        $this->fancierEntrySummaries = array();
    }

    public function addUserEntry(string $userName, string $className, int $classIndex, string $age, int $penNumber): void
    {
        $this->initializeUserEntrySummary($userName);
        
        $userEntry = array();
        $userEntry['className'] = $className;
        $userEntry['classIndex'] = $classIndex;
        $userEntry['age'] = $age;
        $userEntry['penNumber'] = $penNumber;
        $this->fancierEntrySummaries[$userName]['entries'][] = $userEntry;
    }

    public function addUserRegistrationFee(string $userName, float $registrationFee): void
    {
        $this->initializeUserEntrySummary($userName);
        $this->fancierEntrySummaries[$userName]['registrationFee'] = $registrationFee;
    }

    public function addAllEntriesAbsent(string $userName, bool $allEntriesAbsent): void
    {
        $this->initializeUserEntrySummary($userName);
        $this->fancierEntrySummaries[$userName]['allEntriesAbsent'] = $allEntriesAbsent;
    }

    private function initializeUserEntrySummary(string $userName): void
    {
        if(!isset($this->fancierEntrySummaries[$userName])){
            $this->fancierEntrySummaries[$userName] = array();
            $this->fancierEntrySummaries[$userName]['entries'] = array();
        }
    }
}