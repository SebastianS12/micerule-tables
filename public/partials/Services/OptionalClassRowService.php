<?php

class OptionalClassRowService implements IRowService{
    private PlacementsRowService $placementsRowService;

    public function __construct(PlacementsRowService $placementsRowService)
    {
        $this->placementsRowService = $placementsRowService;
    }

    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age, string $section, bool $pastDeadline): array{
        $rowData = array();
        $rowData['classMoved'] = ($entry->moved) ? "moved" : "";
        $rowData['classAbsent'] = ($entry->absent) ? "absent" : "";
        $rowData['classAdded'] = ($entry->added) ? "added" : "";
        $rowData['penNumber'] = $entry->pen_number;
        $rowData['entryID'] = $entry->id;
        $rowData['userName'] = $userName;
        $rowData['absentChecked'] = ($entry->absent) ? "checked" : "";
        $rowData['absentVisibility'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD)) ? "hidden" : "visible";
        $rowData['editVisibility'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD) && $pastDeadline) ? "hidden" : "visible";
        $rowData['showVarietySelect'] = "none";
        $rowData['varietyOptions'] = "";
        $rowData['classPlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::STANDARD, new Collection(), $rowPlacementData->classPlacements, Prize::SECTION, $rowPlacementData->classIndexID, $age);
        $rowData['sectionPlacementData'] = $this->getEmptyPlacementData();
        $rowData['grandChallengePlacementData'] = $this->getEmptyPlacementData();

        return $rowData;
    }

    private function getEmptyPlacementData(): array{
        $placementData = array();
        $placementData['sectionBestDisabled'] = "";
        $placementData['firstPlaceChecked'] = "";
        $placementData['secondPlaceChecked'] = "";
        $placementData['thirdPlaceChecked'] = "";
        $placementData['showFirstPlaceCheck'] = "none";
        $placementData['showSecondPlaceCheck'] = "none";
        $placementData['showThirdPlaceCheck'] = "none";
        $placementData['age'] = "";
        $placementData['entry_id'] = "";
        $placementData['index_id'] = "";
        $placementData['prize'] = "";

        return $placementData;
    }
}