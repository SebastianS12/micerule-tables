<?php

class LabelViewModel{
    public array $userLabels;

    public function __construct()
    {
        $this->userLabels = array();
    }

    public function addLabel(string $userName, int $classIndex, int $penNumber, bool $absent, string $className, string $age, string $section): void
    {
        if(!isset($this->userLabels[$userName])){
            $this->userLabels[$userName] = array();
        }

        $labelData = array();
        $labelData['classIndex'] = $classIndex;
        $labelData['penNumber'] = $penNumber;
        $labelData['absent'] = $absent;
        $labelData['className'] = $className;
        $labelData['age'] = $age;
        $labelData['section'] = Section::from($section)->getDisplayString();
        $this->userLabels[$userName][] = $labelData;
    }
}