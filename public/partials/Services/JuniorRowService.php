<?php

class JuniorRowService implements IRowService{
    private OptionalClassRowService $optionalClassRowService;
    private PlacementsRowService $placementsRowService;

    public function __construct(OptionalClassRowService $optionalClassRowService, PlacementsRowService $placementsRowService)
    {
        $this->optionalClassRowService = $optionalClassRowService;
        $this->placementsRowService = $placementsRowService;
    }

    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age, string $section, bool $pastDeadline)
    {
        $rowData = $this->optionalClassRowService->prepareRowData($entry, $userName, $rowPlacementData, $age, $section, $pastDeadline);
        $rowData = $this->adjustForJuniorClass($rowData, $entry, $rowPlacementData, $age);
        return $rowData;
    }

    private function adjustForJuniorClass(array $rowData, EntryModel $entry, RowPlacementData $rowPlacementData, string $age): array{
        $rowData['editVisibility'] = "hidden";
        $rowData['classPlacementData'] = $this->placementsRowService->prepareEntryPlacementData($entry, Prize::JUNIOR, new Collection(), $rowPlacementData->classPlacements, Prize::SECTION, $rowPlacementData->classIndexID, $age);
        $rowData['classPlacementData']['sectionBestDisabled'] = "";
        $rowData['showVarietySelect'] = "none";
        return $rowData;
    }
}