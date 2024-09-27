<?php

class EntryRowService implements IRowService{
    private $eventDeadline;
    //TODO: should call a service instead of a controller
    private $placementController;
    private $entries;
    private BreedsService $breedsService;

    public function __construct($eventDeadline, $placementController, $entries, $breedsService)
    {
        $this->eventDeadline = $eventDeadline;
        $this->placementController = $placementController;
        $this->entries = $entries;
        $this->breedsService = $breedsService;
    }

    //TODO: inject breeds service into function: breeds service depending on standard or optional, responsible for returning showVarietySelect + varietyOptions
    // Inject section standard breeds etc. into that service before to reduce db calls
    public function prepareRowData(ShowEntry $entry, RowPlacementData $rowPlacementData){
        $entryRowData = array();
        $entryRowData['classMoved'] = ($entry->moved) ? "moved" : "";
        $entryRowData['classAbsent'] = ($entry->absent) ? "absent" : "";
        $entryRowData['classAdded'] = ($entry->added) ? "added" : "";
        $entryRowData['entryID'] = $entry->ID;
        $entryRowData['penNumber'] = $entry->penNumber;
        $entryRowData['userName'] = $entry->userName;
        $entryRowData['absentChecked'] = ($entry->absent) ? "checked" : "";
        $entryRowData['absentVisibility'] = (PlacementsService::entryInPlacements($entry, $rowPlacementData->classPlacements)) ? "hidden" : "visible";
        $entryRowData['editVisibility'] = (PlacementsService::entryInPlacements($entry, $rowPlacementData->classPlacements) && time() > strtotime($this->eventDeadline)) ? "hidden" : "visible";
        $entryRowData['showVarietySelect'] = (PlacementsService::entryInPlacements($entry, $rowPlacementData->classPlacements) && !in_array($entry->className, $this->breedsService->getSectionBreedNames($entry->sectionName))) ? "flex" : "none";
        $entryRowData['varietyOptions'] = $this->breedsService->getClassSelectOptionsHtml($entry->sectionName, $entry->varietyName);
        $entryRowData['classPlacementData'] = $this->placementController->prepareEntryPlacementData($entry, null, $rowPlacementData->classPlacements, $rowPlacementData->sectionPlacements, $rowPlacementData->classIndexID, "Class", $this->entries);
        $entryRowData['sectionPlacementData'] = $this->placementController->prepareEntryPlacementData($entry, $rowPlacementData->classPlacements, $rowPlacementData->sectionPlacements, $rowPlacementData->grandChallengePlacements, $rowPlacementData->sectionIndexID,"Section Challenge", $this->entries);
        $entryRowData['grandChallengePlacementData'] = $this->placementController->prepareEntryPlacementData($entry, $rowPlacementData->sectionPlacements, $rowPlacementData->grandChallengePlacements, null, $rowPlacementData->grandChallengeIndexID,"Grand Challenge", $this->entries);

        return $entryRowData;
    }
}