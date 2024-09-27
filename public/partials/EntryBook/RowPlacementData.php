<?php

class RowPlacementData{
    public $classIndexID;
    public $classPlacements;
    public $sectionIndexID;
    public $sectionPlacements;
    public $grandChallengeIndexID;
    public $grandChallengePlacements;

    public function __construct($classIndexID, $classPlacements, $sectionIndexID, $sectionPlacements, $grandChallengeIndexID, $grandChallengePlacements)
    {
        $this->classIndexID = $classIndexID;
        $this->classPlacements = $classPlacements;
        $this->sectionIndexID = $sectionIndexID;
        $this->sectionPlacements = $sectionPlacements;
        $this->grandChallengeIndexID = $grandChallengeIndexID;
        $this->grandChallengePlacements = $grandChallengePlacements;
    }
}