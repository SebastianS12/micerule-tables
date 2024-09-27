<?php

class OptionalClassRowService implements IRowService{
    private $eventDeadline;
    private $placementController;
    private $entries;

    public function __construct(string $eventDeadline, EntryBookPlacementController $placementController, array $entries)
    {
        $this->eventDeadline = $eventDeadline;
        $this->placementController = $placementController;
        $this->entries = $entries;
    }

    public function prepareRowData(ShowEntry $entry, RowPlacementData $rowPlacementData): array{
        $rowData = array();
        $rowData['classMoved'] = ($entry->moved) ? "moved" : "";
        $rowData['classAbsent'] = ($entry->absent) ? "absent" : "";
        $rowData['classAdded'] = ($entry->added) ? "added" : "";
        $rowData['penNumber'] = $entry->penNumber;
        $rowData['entryID'] = $entry->ID;
        $rowData['userName'] = $entry->userName;
        $rowData['absentChecked'] = ($entry->absent) ? "checked" : "";
        $rowData['absentVisibility'] = (PlacementsService::entryInPlacements($entry, $rowPlacementData->classPlacements)) ? "hidden" : "visible";
        $rowData['editVisibility'] = (PlacementsService::entryInPlacements($entry, $rowPlacementData->classPlacements) && time() > strtotime($this->eventDeadline)) ? "hidden" : "visible";
        $rowData['showVarietySelect'] = "none";
        $rowData['varietyOptions'] = "";
        $rowData['classPlacementData'] = $this->placementController->prepareEntryPlacementData($entry, null, $rowPlacementData->classPlacements, null, $rowPlacementData->classIndexID, "Class", $this->entries);
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