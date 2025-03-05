<?php

class JudgingSheetsViewModel{
    public array $classSheets;
    public array $sectionChallengeSheets;
    public array $grandChallengeSheets;
    public array $optionalClassSheets;

    public function __construct()
    {
        $this->classSheets = array();
        $this->sectionChallengeSheets = array();
        $this->grandChallengeSheets = array();
        $this->optionalClassSheets = array();
    }

    public function addClassSheet(string $judgeName, string $section, string $className, string $age, int $classIndex, bool $showVarietyPrompt): void
    {
        if(!isset($this->classSheets[$judgeName])){
            $this->classSheets[$judgeName] = array();
        }
        if(!isset($this->classSheets[$judgeName][$section])){
            $this->classSheets[$judgeName][$section] = array();
        }

        $classSheetData = array();
        $classSheetData['className'] = $className;
        $classSheetData['age'] = $age;
        $classSheetData['classIndex'] = $classIndex;
        $classSheetData['judgeName'] = $judgeName;
        $classSheetData['showVarietyPrompt'] = $showVarietyPrompt;
        $classSheetData['penNumbers'] = array();

        $this->classSheets[$judgeName][$section][$classIndex] = $classSheetData;
    }

    public function addPenNumber(string $judgeName, string $section, int $classIndex, int $penNumber): void
    {
        if(isset($this->classSheets[$judgeName][$section][$classIndex])){
            $this->classSheets[$judgeName][$section][$classIndex]['penNumbers'][] = $penNumber;
        }
    }

    public function addSectionSheet(string $judgeName, string $challengeName, int $challengeIndex, string $age, string $section, bool $addSectionBestSheet): void
    {
        if(!isset($this->sectionChallengeSheets[$judgeName])){
            $this->sectionChallengeSheets[$judgeName] = array();
        }
        if(!isset($this->sectionChallengeSheets[$judgeName][$section])){
            $this->sectionChallengeSheets[$judgeName][$section] = array();
        }

        $sectionSheetData = array();
        $sectionSheetData['challengeName'] = $challengeName;
        $sectionSheetData['age'] = $age;
        $sectionSheetData['challengeIndex'] = $challengeIndex;
        $sectionSheetData['judgeName'] = $judgeName;
        $sectionSheetData['section'] = $section;
        $sectionSheetData['addSectionBestSheet'] = $addSectionBestSheet;

        $this->sectionChallengeSheets[$judgeName][$section][$challengeIndex] = $sectionSheetData;
    }

    public function addGrandChallengeSheet(string $judgeName, string $challengeName, int $challengeIndex, string $age, string $section, bool $addSectionBestSheet): void
    {
        $grandChallengeSheetData = array();
        $grandChallengeSheetData['challengeName'] = $challengeName;
        $grandChallengeSheetData['age'] = $age;
        $grandChallengeSheetData['challengeIndex'] = $challengeIndex;
        $grandChallengeSheetData['judgeName'] = $judgeName;
        $grandChallengeSheetData['section'] = $section;
        $grandChallengeSheetData['addSectionBestSheet'] = $addSectionBestSheet;

        $this->grandChallengeSheets[$challengeIndex] = $grandChallengeSheetData;
    }

    public function addOptionalClassSheet(string $className, string $age, int $classIndex, bool $showVarietyPrompt):void
    {
        $classSheetData = array();
        $classSheetData['className'] = $className;
        $classSheetData['age'] = $age;
        $classSheetData['classIndex'] = $classIndex;
        $classSheetData['judgeName'] = "";
        $classSheetData['showVarietyPrompt'] = $showVarietyPrompt;
        $classSheetData['penNumbers'] = array();

        $this->optionalClassSheets[$classIndex] = $classSheetData;
    }

    public function addOptionalClassPenNumber(int $classIndex, int $penNumber): void
    {
        if(isset($this->optionalClassSheets[$classIndex])){
            $this->optionalClassSheets[$classIndex]['penNumbers'][] = $penNumber;
        }
    }
}