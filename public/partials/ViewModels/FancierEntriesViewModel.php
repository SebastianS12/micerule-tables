<?php

class FancierEntriesViewModel{
    public array $fancierEntries;

    public function __construct()
    {
        $this->fancierEntries = array();
    }

    public function addClassRegistration(string $userName, int $classIndex, string $className, string $age, int $registrationCount): void
    {
        if(!isset($this->fancierEntries[$userName])){
            $this->fancierEntries[$userName] = array();
        }

        $this->fancierEntries[$userName]['classData'][$classIndex] = array();
        $this->fancierEntries[$userName]['classData'][$classIndex]['className'] = $className;
        $this->fancierEntries[$userName]['classData'][$classIndex]['age'] = $age;
        $this->fancierEntries[$userName]['classData'][$classIndex]['registrationCount'] = $registrationCount;
    }

    public function addTotalRegistrationCount(string $userName, int $registrationCount): void
    {
        $this->fancierEntries[$userName]['totalRegistrationCount'] = $registrationCount;
    }

    public function addPrizeMoney(string $userName, float $prizeMoney): void
    {
        $this->fancierEntries[$userName]['prizeMoney'] = $prizeMoney;
    }
}