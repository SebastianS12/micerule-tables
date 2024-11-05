<?php

class JuniorRowService implements IRowService{
    private $optionalClassRowService;
    public function __construct(OptionalClassRowService $optionalClassRowService)
    {
        $this->optionalClassRowService = $optionalClassRowService;
    }

    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age, string $section, bool $pastDeadline)
    {
        $rowData = $this->optionalClassRowService->prepareRowData($entry, $userName, $rowPlacementData, $age, $section, $pastDeadline);
        $rowData = $this->adjustForJuniorClass($rowData);
        return $rowData;
    }

    private function adjustForJuniorClass(array $rowData): array{
        $rowData['editVisibility'] = "hidden";
        $rowData['classPlacementData']['prize'] = "Junior";
        $rowData['showVarietySelect'] = "none";
        return $rowData;
    }
}