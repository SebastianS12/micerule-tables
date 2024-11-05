<?php

class RowPlacementData{
    public int $classIndexID;
    public Collection $classPlacements;
    public int $sectionIndexID;
    public Collection $sectionPlacements;
    public int $grandChallengeIndexID;
    public Collection $grandChallengePlacements;

    public function __construct(int $classIndexID, Collection $classPlacements, int $sectionIndexID, Collection $sectionPlacements, int $grandChallengeIndexID, Collection $grandChallengePlacements)
    {
        $this->classIndexID = $classIndexID;
        $this->classPlacements = $classPlacements;
        $this->sectionIndexID = $sectionIndexID;
        $this->sectionPlacements = $sectionPlacements;
        $this->grandChallengeIndexID = $grandChallengeIndexID;
        $this->grandChallengePlacements = $grandChallengePlacements;
    }
}