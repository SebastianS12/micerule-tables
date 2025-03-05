<?php

class ShowClassesViewModel{
    public bool $canEditShowClasses;
    public array $standardClasses;
    public array $challenges;
    public array $optionalClasses;

    public function __construct(bool $canEditShowClasses)
    {
        $this->canEditShowClasses = $canEditShowClasses;
        $this->standardClasses = array();
        $this->challenges = array();
        foreach(Section::standardClasses() as $section){
            $this->standardClasses = $this->initializeSection($this->standardClasses, $section);
            $this->challenges = $this->initializeSection($this->challenges, $section);
            $this->initializeChallenge($section);
        }
        $this->challenges = $this->initializeSection($this->challenges, Section::GRAND_CHALLENGE);
        $this->initializeChallenge(Section::GRAND_CHALLENGE);
        $this->optionalClasses = array();
    }

    public function addClass(EntryClassModel $entryClassModel): void
    {
        $this->standardClasses[$entryClassModel->section][$entryClassModel->section_position] = array();
        $this->standardClasses[$entryClassModel->section][$entryClassModel->section_position]['className'] = $entryClassModel->class_name;
        $this->standardClasses[$entryClassModel->section][$entryClassModel->section_position]['classID'] = $entryClassModel->id;
        $classIndexModels = $entryClassModel->classIndices()->groupByUniqueKey("age");
        $this->standardClasses[$entryClassModel->section][$entryClassModel->section_position]['adIndex'] = (isset($classIndexModels["Ad"])) ? $classIndexModels["Ad"]->class_index : "";
        $this->standardClasses[$entryClassModel->section][$entryClassModel->section_position]['u8Index'] = (isset($classIndexModels["U8"])) ? $classIndexModels["U8"]->class_index : "";
    }

    public function addChallenge(ChallengeIndexModel $adChallengeIndexModel, ChallengeIndexModel $u8ChallengeIndexModel): void
    {
        $this->challenges[$adChallengeIndexModel->section]['challengeName'] = $adChallengeIndexModel->challenge_name;
        $this->challenges[$adChallengeIndexModel->section]['adIndex'] = $adChallengeIndexModel->challenge_index;
        $this->challenges[$adChallengeIndexModel->section]['u8Index'] = $u8ChallengeIndexModel->challenge_index;
    }

    private function initializeSection(array $sectionClasses, Section $section): array
    {
        $sectionClasses[$section->value] = array();
        return $sectionClasses;
    }

    private function initializeChallenge(Section $section): void
    {
        $this->challenges[$section->value]['challengeName'] = $section->getChallengeName();
        $this->challenges[$section->value]['adIndex'] = "";
        $this->challenges[$section->value]['u8Index'] = "";
    }

    public function addOptionalClass(EntryClassModel $entryClassModel): void
    {
        $this->optionalClasses[$entryClassModel->section_position] = array();
        $this->optionalClasses[$entryClassModel->section_position]['className'] = $entryClassModel->class_name;
        $this->optionalClasses[$entryClassModel->section_position]['classID'] = $entryClassModel->id;
        $classIndexModels = $entryClassModel->classIndices()->groupByUniqueKey("age");
        $this->optionalClasses[$entryClassModel->section_position]['index'] = (isset($classIndexModels["AA"])) ? $classIndexModels["AA"]->class_index : "";
    }
}