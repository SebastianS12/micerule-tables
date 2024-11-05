<?php

interface IRowService{
    public function prepareRowData(EntryModel $entry, string $userName, RowPlacementData $rowPlacementData, string $age, string $section, bool $pastDeadline);
}