<?php

class LabelViewModel{
    public array $userLabels;

    public function __construct()
    {
        $this->userLabels = array();
    }

    public function addLabel(string $userName, int $classIndex, int $penNumber, bool $absent): void
    {
        if(!isset($this->userLabels[$userName])){
            $this->userLabels[$userName] = array();
        }

        $labelData = array();
        $labelData['classIndex'] = $classIndex;
        $labelData['penNumber'] = $penNumber;
        $labelData['absent'] = $absent;
        $this->userLabels[$userName][] = $labelData;
    }
}