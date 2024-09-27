<?php

class EntryBookRowController{
    private IRowService $rowService;

    public function __construct(IRowService $rowService)
    {
        $this->rowService = $rowService;
    }

    public function prepareEntryRowData(ShowEntry $entry, RowPlacementData $rowPlacementData){
        return $this->rowService->prepareRowData($entry, $rowPlacementData);
    }

    public function render(ShowEntry $entry, RowPlacementData $rowPlacementData){
        $data = $this->prepareEntryRowData($entry, $rowPlacementData);
        return EntryBookRowView::render($data);
    }
}