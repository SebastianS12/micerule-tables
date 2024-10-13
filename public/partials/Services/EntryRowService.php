<?php

class EntryRowService {
    private $eventDeadline;
    //TODO: should call a service instead of a controller
    private PlacementsRowService $placementsRowService;
    private $entries;
    private BreedsService $breedsService;

    public function __construct(PlacementsRowService $placementsRowService)
    {
        $this->placementsRowService = $placementsRowService;
    }

    //TODO: inject breeds service into function: breeds service depending on standard or optional, responsible for returning showVarietySelect + varietyOptions
    // Inject section standard breeds etc. into that service before to reduce db calls
    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age){
        $entryRowData = array();
        $entryRowData['classMoved'] = ($entry->moved) ? "moved" : "";
        $entryRowData['classAbsent'] = ($entry->absent) ? "absent" : "";
        $entryRowData['classAdded'] = ($entry->added) ? "added" : "";
        $entryRowData['entryID'] = $entry->id;
        $entryRowData['penNumber'] = $entry->penNumber;
        $entryRowData['userName'] = $userName;
        $entryRowData['absentChecked'] = ($entry->absent) ? "checked" : "";

        $entryRowData['absentVisibility'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD)) ? "hidden" : "visible";
        $entryRowData['editVisibility'] = (PlacementsService::entryInPlacements($entry, Prize::STANDARD)/* && time() > strtotime($this->eventDeadline)*/) ? "hidden" : "visible";
        $entryRowData['showVarietySelect'] = /*(PlacementsService::entryInPlacements($entry, $rowPlacementData->classPlacements) && !in_array($entry->className, $this->breedsService->getSectionBreedNames($entry->sectionName))) ? "flex" :*/ "none";
        $entryRowData['varietyOptions'] = "";//$this->breedsService->getClassSelectOptionsHtml($entry->sectionName, $entry->varietyName);
        $entryRowData['classPlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::STANDARD, new Collection(), $rowPlacementData->classPlacements, $rowPlacementData->sectionPlacements, $rowPlacementData->classIndexID, $age);
        // $entryRowData['sectionPlacementData'] = $this->placementController->prepareEntryPlacementData($entry, $rowPlacementData->classPlacements, $rowPlacementData->sectionPlacements, $rowPlacementData->grandChallengePlacements, $rowPlacementData->sectionIndexID,"Section Challenge", $this->entries);
        // $entryRowData['grandChallengePlacementData'] = $this->placementController->prepareEntryPlacementData($entry, $rowPlacementData->sectionPlacements, $rowPlacementData->grandChallengePlacements, null, $rowPlacementData->grandChallengeIndexID,"Grand Challenge", $this->entries);

        return $entryRowData;
    }
}