<?php

class EntryRowService implements IRowService{
    private PlacementsRowService $placementsRowService;

    public function __construct(PlacementsRowService $placementsRowService)
    {
        $this->placementsRowService = $placementsRowService;
    }

    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age, bool $pastDeadline){
        $entryRowData = array();
        $entryRowData['classMoved'] = ($entry->moved) ? "moved" : "";
        $entryRowData['classAbsent'] = ($entry->absent) ? "absent" : "";
        $entryRowData['classAdded'] = ($entry->added) ? "added" : "";
        $entryRowData['entryID'] = $entry->id;
        $entryRowData['penNumber'] = $entry->penNumber;
        $entryRowData['userName'] = $userName;
        $entryRowData['absentChecked'] = ($entry->absent) ? "checked" : "";

        $entryRowData['absentVisibility'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD)) ? "hidden" : "visible";
        $entryRowData['editVisibility'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD) && time() > strtotime($pastDeadline)) ? "hidden" : "visible";
        $entryRowData['showVarietySelect'] = /*(PlacementsService::entryInPlacements($entry, $rowPlacementData->classPlacements) && !in_array($entry->className, $this->breedsService->getSectionBreedNames($entry->sectionName))) ? "flex" :*/ "none";
        $entryRowData['varietyOptions'] = "";//$this->breedsService->getClassSelectOptionsHtml($entry->sectionName, $entry->varietyName);
        $entryRowData['classPlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::STANDARD, new Collection(), $rowPlacementData->classPlacements, Prize::SECTION, $rowPlacementData->classIndexID, $age);
        $entryRowData['sectionPlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::SECTION, $rowPlacementData->classPlacements, $rowPlacementData->sectionPlacements, Prize::GRANDCHALLENGE, $rowPlacementData->sectionIndexID, $age, Prize::STANDARD);
        $entryRowData['grandChallengePlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::GRANDCHALLENGE, $rowPlacementData->sectionPlacements, $rowPlacementData->grandChallengePlacements, Prize::SECTION_AWARD, $rowPlacementData->grandChallengeIndexID, $age, Prize::SECTION);

        return $entryRowData;
    }
}