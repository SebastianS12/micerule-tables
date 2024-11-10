<?php

class EntryRowService implements IRowService{
    private PlacementsRowService $placementsRowService;
    private BreedsService $breedsService;

    public function __construct(PlacementsRowService $placementsRowService, BreedsService $breedsService)
    {
        $this->placementsRowService = $placementsRowService;
        $this->breedsService = $breedsService;
    }

    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age, string $section, bool $pastDeadline){
        $entryRowData = array();
        $entryRowData['classMoved'] = ($entry->moved) ? "moved" : "";
        $entryRowData['classAbsent'] = ($entry->absent) ? "absent" : "";
        $entryRowData['classAdded'] = ($entry->added) ? "added" : "";
        $entryRowData['entryID'] = $entry->id;
        $entryRowData['penNumber'] = $entry->pen_number;
        $entryRowData['userName'] = $userName;
        $entryRowData['absentChecked'] = ($entry->absent) ? "checked" : "";

        $entryRowData['absentVisibility'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD)) ? "hidden" : "visible";
        $entryRowData['editVisibility'] = (!PlacementsService::entryInPlacements($entry, Prize::STANDARD) || !$pastDeadline);
        $entryRowData['showVarietySelect'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD) && !$this->breedsService->isStandardBreed($entry->showClass()->class_name)) ? "flex" : "none";
        $entryRowData['varietyOptions'] = ($entryRowData['showVarietySelect'] == "flex") ? $this->breedsService->getClassSelectOptionsHtml($section, $entry->variety_name) : "";
        $entryRowData['classPlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::STANDARD, new Collection(), $rowPlacementData->classPlacements, Prize::SECTION, $rowPlacementData->classIndexID, $age);
        $entryRowData['sectionPlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::SECTION, $rowPlacementData->classPlacements, $rowPlacementData->sectionPlacements, Prize::GRANDCHALLENGE, $rowPlacementData->sectionIndexID, $age, Prize::STANDARD);
        $entryRowData['grandChallengePlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::GRANDCHALLENGE, $rowPlacementData->sectionPlacements, $rowPlacementData->grandChallengePlacements, Prize::SECTION_AWARD, $rowPlacementData->grandChallengeIndexID, $age, Prize::SECTION);

        return $entryRowData;
    }
}