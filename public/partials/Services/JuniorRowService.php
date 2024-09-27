<?php

class JuniorRowService implements IRowService{
    private $optionalClassRowService;
    public function __construct(OptionalClassRowService $optionalClassRowService)
    {
        $this->optionalClassRowService = $optionalClassRowService;
    }

    public function prepareRowData(ShowEntry $entry, RowPlacementData $rowPlacementData)
    {
        $rowData = $this->optionalClassRowService->prepareRowData($entry, $rowPlacementData);
        $rowData = $this->adjustForJuniorClass($rowData);
        return $rowData;
    }

    private function adjustForJuniorClass(array $rowData): array{
        $rowData['editVisibility'] = "hidden";
        $rowData['classPlacementData']['prize'] = "Junior";
        return $rowData;
    }
}