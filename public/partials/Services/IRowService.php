<?php

interface IRowService{
    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age, bool $pastDeadline);
}