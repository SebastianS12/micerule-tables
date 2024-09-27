<?php

class EntryBookViewModel{
    public string $addEntryVisibility;
    public array $grandChallengeData;
    public array $sectionData;
    public array $classData;

    public function __construct()
    {
        $this->sectionData = array();
        $this->classData = array();
    }

    public function addSectionData(string $sectionName, array $challengeRowData): void{
        $this->sectionData[$sectionName] = $challengeRowData;
    }
}